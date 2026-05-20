<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KualifikasiSertifikasiModel;
use App\Models\LogbookModel;
use App\Models\PelatihanModel;
use App\Models\PendaftaranModel;
use App\Models\SesiPelatihanModel;
use App\Models\UserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KualifikasiSertifikasiController extends Controller
{
    /* ═══════════════════════════════════════════════
     |  GET /api/kualifikasi
     |  Admin      → semua kualifikasi
     |  Instruktur → kualifikasi dari pelatihannya
     |  Peserta    → status kelayakan diri sendiri
     ═══════════════════════════════════════════════ */

    public function index(Request $request): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        $query = KualifikasiSertifikasiModel::with([
            'pendaftaran.peserta:id_user,nama,email',
            'pendaftaran.pelatihan:id_pelatihan,nama_pelatihan,kode_pelatihan',
            'instruktur:id_user,nama',
        ])->latest('tgl_penilaian');

        match ($user->role) {
            'instruktur' => $query->where('instruktur_id', $user->id_user),
            'peserta'    => $query->whereHas('pendaftaran', fn($q) =>
                $q->where('peserta_id', $user->id_user)
            ),
            default => null,
        };

        if ($request->filled('memenuhi_syarat')) {
            $query->where('memenuhi_syarat', (bool) $request->memenuhi_syarat);
        }
        if ($request->filled('pelatihan_id')) {
            $query->whereHas('pendaftaran', fn($q) =>
                $q->where('pelatihan_id', $request->pelatihan_id)
            );
        }

        $data = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  GET /api/kualifikasi/pelatihan/{id}
     |  Daftar peserta + data kelayakan satu pelatihan
     |  Admin & Instruktur
     ═══════════════════════════════════════════════ */

    public function pesertaPerPelatihan(Request $request, int $pelatihanId): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        if ($user->role === 'peserta') {
            return $this->forbidden();
        }

        $pelatihan = PelatihanModel::findOrFail($pelatihanId);

        if ($user->role === 'instruktur' && $pelatihan->instruktur_id !== $user->id_user) {
            return $this->forbidden();
        }

        $totalSesi = SesiPelatihanModel::where('pelatihan_id', $pelatihanId)->count();

        $pesertaList = PendaftaranModel::with(['peserta:id_user,nama,email', 'kualifikasiSertifikasi'])
            ->where('pelatihan_id', $pelatihanId)
            ->where('status', 'diterima')
            ->get()
            ->map(function ($daftar) use ($pelatihanId, $totalSesi) {
                $hadir = LogbookModel::where('peserta_id', $daftar->peserta_id)
                    ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $pelatihanId))
                    ->where('status', 'hadir')
                    ->count();

                $persen = $totalSesi > 0 ? round(($hadir / $totalSesi) * 100, 1) : 0;

                return [
                    'pendaftaran_id'  => $daftar->id_pendaftaran,
                    'peserta'         => $daftar->peserta,
                    'persen_hadir'    => $persen,
                    'hadir'           => $hadir,
                    'total_sesi'      => $totalSesi,
                    'kualifikasi'     => $daftar->kualifikasiSertifikasi
                        ? [
                            'memenuhi_syarat' => (bool) $daftar->kualifikasiSertifikasi->memenuhi_syarat,
                            'catatan'         => $daftar->kualifikasiSertifikasi->catatan,
                            'tgl_penilaian'   => $daftar->kualifikasiSertifikasi->tgl_penilaian,
                        ]
                        : null,
                ];
            });

        return response()->json([
            'success'    => true,
            'pelatihan'  => $pelatihan->only(['id_pelatihan', 'nama_pelatihan', 'kode_pelatihan', 'status']),
            'total_sesi' => $totalSesi,
            'data'       => $pesertaList,
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  POST /api/kualifikasi/{pendaftaran_id}   [INSTRUKTUR]
     |  Simpan/update penilaian kelayakan satu peserta
     ═══════════════════════════════════════════════ */

    public function simpan(Request $request, int $pendaftaranId): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        if ($user->role !== 'instruktur') {
            return $this->forbidden();
        }

        $pendaftaran = PendaftaranModel::with('pelatihan')->findOrFail($pendaftaranId);

        if ($pendaftaran->pelatihan->instruktur_id !== $user->id_user) {
            return $this->forbidden();
        }

        $request->validate([
            'memenuhi_syarat' => 'required|boolean',
            'catatan'         => 'nullable|string',
        ]);

        // Hitung persen kehadiran otomatis dari logbook
        $totalSesi  = SesiPelatihanModel::where('pelatihan_id', $pendaftaran->pelatihan_id)->count();
        $hadirCount = LogbookModel::where('peserta_id', $pendaftaran->peserta_id)
            ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $pendaftaran->pelatihan_id))
            ->where('status', 'hadir')
            ->count();

        $persenHadir = $totalSesi > 0 ? round(($hadirCount / $totalSesi) * 100, 2) : 0;

        $kualifikasi = KualifikasiSertifikasiModel::updateOrCreate(
            ['pendaftaran_id' => $pendaftaranId],
            [
                'instruktur_id'  => $user->id_user,
                'persen_hadir'   => $persenHadir,
                'memenuhi_syarat'=> $request->boolean('memenuhi_syarat'),
                'catatan'        => $request->catatan,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Penilaian kelayakan berhasil disimpan.',
            'data'    => $kualifikasi,
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  POST /api/kualifikasi/massal/{pelatihan_id}   [INSTRUKTUR]
     |  Simpan kelayakan semua peserta sekaligus
     ═══════════════════════════════════════════════ */

    public function simpanMassal(Request $request, int $pelatihanId): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        if ($user->role !== 'instruktur') {
            return $this->forbidden();
        }

        $pelatihan = PelatihanModel::findOrFail($pelatihanId);

        if ($pelatihan->instruktur_id !== $user->id_user) {
            return $this->forbidden();
        }

        $request->validate([
            'kelayakan'                   => 'required|array|min:1',
            'kelayakan.*.pendaftaran_id'  => 'required|exists:pendaftaran,id_pendaftaran',
            'kelayakan.*.memenuhi_syarat' => 'required|boolean',
            'kelayakan.*.catatan'         => 'nullable|string',
        ]);

        $totalSesi = SesiPelatihanModel::where('pelatihan_id', $pelatihanId)->count();
        $berhasil  = 0;

        DB::transaction(function () use ($request, $pelatihan, $user, $totalSesi, &$berhasil) {
            foreach ($request->input('kelayakan') as $row) {
                $pendaftaran = PendaftaranModel::findOrFail($row['pendaftaran_id']);

                $hadir = LogbookModel::where('peserta_id', $pendaftaran->peserta_id)
                    ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $pelatihan->id_pelatihan))
                    ->where('status', 'hadir')
                    ->count();

                $persen = $totalSesi > 0 ? round(($hadir / $totalSesi) * 100, 2) : 0;

                KualifikasiSertifikasiModel::updateOrCreate(
                    ['pendaftaran_id' => $row['pendaftaran_id']],
                    [
                        'instruktur_id'  => $user->id_user,
                        'persen_hadir'   => $persen,
                        'memenuhi_syarat'=> (bool) $row['memenuhi_syarat'],
                        'catatan'        => $row['catatan'] ?? null,
                    ]
                );
                $berhasil++;
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Penilaian kelayakan {$berhasil} peserta berhasil disimpan.",
        ]);
    }

    /* ─── Helper ─── */

    private function forbidden(): JsonResponse
    {
        return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
    }
}
