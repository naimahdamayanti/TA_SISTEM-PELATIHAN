<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LogbookModel;
use App\Models\PelatihanModel;
use App\Models\PendaftaranModel;
use App\Models\SesiPelatihanModel;
use App\Models\UserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogbookController extends Controller
{
    /* ═══════════════════════════════════════════════
     |  GET /api/logbook
     |  Admin      → semua logbook (filter pelatihan_id / sesi_id)
     |  Instruktur → logbook dari sesi pelatihannya
     |  Peserta    → kehadiran diri sendiri
     ═══════════════════════════════════════════════ */

    public function index(Request $request): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        $query = LogbookModel::with([
            'peserta:id_user,nama,email',
            'instruktur:id_user,nama',
            'sesi:id_sesi,pelatihan_id,judul_sesi,tanggal,waktu_mulai,waktu_selesai,lokasi',
        ])->latest();

        match ($user->role) {
            'instruktur' => $query->where('instruktur_id', $user->id_user),
            'peserta'    => $query->where('peserta_id', $user->id_user),
            default      => null,
        };

        if ($request->filled('sesi_id')) {
            $query->where('sesi_id', $request->sesi_id);
        }
        if ($request->filled('pelatihan_id')) {
            $query->whereHas('sesi', fn($q) =>
                $q->where('pelatihan_id', $request->pelatihan_id)
            );
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logbook = $query->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => $logbook,
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  GET /api/logbook/sesi/{sesi_id}
     |  Detail kehadiran satu sesi (admin & instruktur)
     ═══════════════════════════════════════════════ */

    public function detailSesi(Request $request, int $sesiId): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        $sesi = SesiPelatihanModel::with('pelatihan')->findOrFail($sesiId);

        // Instruktur hanya bisa akses sesi pelatihannya
        if ($user->role === 'instruktur' && $sesi->pelatihan->instruktur_id !== $user->id_user) {
            return $this->forbidden();
        }
        if ($user->role === 'peserta') {
            return $this->forbidden();
        }

        // Peserta terdaftar di sesi ini
        $pesertaTerdaftar = PendaftaranModel::with('peserta:id_user,nama,email,no_hp')
            ->where('pelatihan_id', $sesi->pelatihan_id)
            ->where('status', 'diterima')
            ->get();

        // Logbook yang sudah diisi
        $logbookTerisi = LogbookModel::where('sesi_id', $sesiId)
            ->get()
            ->keyBy('peserta_id');

        $data = $pesertaTerdaftar->map(fn($daftar) => [
            'pendaftaran_id' => $daftar->id_pendaftaran,
            'peserta'        => $daftar->peserta,
            'status'         => $logbookTerisi->get($daftar->peserta_id)?->status ?? null,
            'catatan'        => $logbookTerisi->get($daftar->peserta_id)?->catatan ?? null,
            'sudah_diisi'    => $logbookTerisi->has($daftar->peserta_id),
        ]);

        return response()->json([
            'success' => true,
            'sesi'    => $sesi,
            'data'    => $data,
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  POST /api/logbook/sesi/{sesi_id}   [INSTRUKTUR]
     |  Simpan/update kehadiran seluruh peserta satu sesi
     ═══════════════════════════════════════════════ */

    public function simpanKehadiran(Request $request, int $sesiId): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        if ($user->role !== 'instruktur') {
            return $this->forbidden();
        }

        $sesi = SesiPelatihanModel::with('pelatihan')->findOrFail($sesiId);

        if ($sesi->pelatihan->instruktur_id !== $user->id_user) {
            return $this->forbidden();
        }

        $request->validate([
            'kehadiran'              => 'required|array|min:1',
            'kehadiran.*.peserta_id' => 'required|exists:users,id_user',
            'kehadiran.*.status'     => 'required|in:hadir,izin,tidak hadir',
            'kehadiran.*.catatan'    => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $sesiId, $user) {
            foreach ($request->input('kehadiran') as $row) {
                LogbookModel::updateOrCreate(
                    [
                        'sesi_id'    => $sesiId,
                        'peserta_id' => $row['peserta_id'],
                    ],
                    [
                        'instruktur_id' => $user->id_user,
                        'status'        => $row['status'],
                        'catatan'       => $row['catatan'] ?? null,
                    ]
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Logbook kehadiran berhasil disimpan.',
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  GET /api/logbook/rekap/{pelatihan_id}
     |  Rekap matriks kehadiran satu pelatihan
     |  Admin & Instruktur
     ═══════════════════════════════════════════════ */

    public function rekap(Request $request, int $pelatihanId): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        $pelatihan = PelatihanModel::findOrFail($pelatihanId);

        if ($user->role === 'instruktur' && $pelatihan->instruktur_id !== $user->id_user) {
            return $this->forbidden();
        }
        if ($user->role === 'peserta') {
            return $this->forbidden();
        }

        $sesiList = SesiPelatihanModel::where('pelatihan_id', $pelatihanId)
            ->orderBy('tanggal')
            ->get(['id_sesi', 'judul_sesi', 'tanggal', 'waktu_mulai', 'waktu_selesai', 'lokasi']);

        $pesertaList = PendaftaranModel::with('peserta:id_user,nama,email')
            ->where('pelatihan_id', $pelatihanId)
            ->where('status', 'diterima')
            ->get();

        $logbookMatrix = LogbookModel::whereIn('sesi_id', $sesiList->pluck('id_sesi'))
            ->get()
            ->groupBy('peserta_id')
            ->map(fn($rows) => $rows->keyBy('sesi_id')
                ->map(fn($lb) => ['status' => $lb->status, 'catatan' => $lb->catatan])
            );

        $totalSesi = $sesiList->count();

        $pesertaRekap = $pesertaList->map(function ($daftar) use ($sesiList, $logbookMatrix, $totalSesi) {
            $kehadiran = $logbookMatrix->get($daftar->peserta_id, collect());
            $hadirCount = $kehadiran->filter(fn($lb) => $lb['status'] === 'hadir')->count();

            return [
                'peserta'      => $daftar->peserta,
                'persen_hadir' => $totalSesi > 0
                    ? round(($hadirCount / $totalSesi) * 100, 1)
                    : 0,
                'hadir'        => $hadirCount,
                'detail'       => $sesiList->map(fn($s) => [
                    'sesi_id' => $s->id_sesi,
                    'tanggal' => $s->tanggal,
                    'status'  => $kehadiran->get($s->id_sesi)['status'] ?? null,
                ]),
            ];
        });

        return response()->json([
            'success'    => true,
            'pelatihan'  => $pelatihan->only(['id_pelatihan', 'nama_pelatihan', 'kode_pelatihan']),
            'sesi_list'  => $sesiList,
            'data'       => $pesertaRekap,
        ]);
    }

    /* ─── Helper ─── */

    private function forbidden(): JsonResponse
    {
        return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
    }
}
