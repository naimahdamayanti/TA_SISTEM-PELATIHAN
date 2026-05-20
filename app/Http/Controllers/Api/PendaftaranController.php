<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranModel;
use App\Models\PelatihanModel;
use App\Models\UserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PendaftaranController extends Controller
{
    /* ═══════════════════════════════════════════════
     |  GET /api/pendaftaran
     |  Admin      → semua pendaftaran + filter
     |  Instruktur → pendaftaran di pelatihannya
     |  Peserta    → riwayat daftar sendiri
     ═══════════════════════════════════════════════ */

    public function index(Request $request): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        $query = PendaftaranModel::with([
            'peserta:id_user,nama,email,no_hp',
            'pelatihan:id_pelatihan,nama_pelatihan,kode_pelatihan,instruktur_id',
        ])->latest('tgl_daftar');

        match ($user->role) {
            'instruktur' => $query->whereHas('pelatihan', fn($q) =>
                $q->where('instruktur_id', $user->id_user)
            ),
            'peserta' => $query->where('peserta_id', $user->id_user),
            default   => null, // admin → semua
        };

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('pelatihan_id')) {
            $query->where('pelatihan_id', $request->pelatihan_id);
        }
        if ($request->filled('search') && $user->role === 'admin') {
            $query->where(fn($q) =>
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
            );
        }

        $pendaftaran = $query->paginate($request->input('per_page', 15));

        // Statistik hanya untuk admin
        $stats = null;
        if ($user->role === 'admin') {
            $stats = [
                'menunggu' => PendaftaranModel::where('status', 'menunggu')->count(),
                'diterima' => PendaftaranModel::where('status', 'diterima')->count(),
                'ditolak'  => PendaftaranModel::where('status', 'ditolak')->count(),
            ];
        }

        return response()->json([
            'success' => true,
            'data'    => $pendaftaran,
            'stats'   => $stats,
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  GET /api/pendaftaran/{id}
     ═══════════════════════════════════════════════ */

    public function show(Request $request, int $id): JsonResponse
    {
        /** @var UserModel $user */
        $user  = $request->user();
        $daftar = PendaftaranModel::with([
            'peserta:id_user,nama,email,no_hp',
            'pelatihan.instruktur:id_user,nama',
            'kualifikasiSertifikasi',
            'sertifikat:id_sertifikat,pendaftaran_id,kode_sertifikat,tgl_terbit',
        ])->findOrFail($id);

        // Peserta hanya bisa lihat miliknya
        if ($user->role === 'peserta' && $daftar->peserta_id !== $user->id_user) {
            return $this->forbidden();
        }
        // Instruktur hanya bisa lihat yang ada di pelatihannya
        if ($user->role === 'instruktur' && $daftar->pelatihan->instruktur_id !== $user->id_user) {
            return $this->forbidden();
        }

        return response()->json([
            'success' => true,
            'data'    => $daftar,
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  POST /api/pendaftaran   [PESERTA]
     |  Kirim formulir pendaftaran pelatihan
     ═══════════════════════════════════════════════ */

    public function store(Request $request): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        if ($user->role !== 'peserta') {
            return $this->forbidden();
        }

        $validated = $request->validate([
            'pelatihan_id'   => 'required|exists:pelatihan,id_pelatihan',
            'first_name'     => 'required|string|max:50',
            'last_name'      => 'nullable|string|max:50',
            'email'          => 'required|email|max:100',
            'no_hp'          => 'nullable|string|max:20',
            'perusahaan'     => 'nullable|string|max:100',
            'alamat'         => 'nullable|string',
            'pekerjaan'      => 'nullable|string|max:100',
            'tlp_perusahaan' => 'nullable|string|max:20',
            'pesan'          => 'nullable|string',
        ]);

        $pelatihan = PelatihanModel::findOrFail($validated['pelatihan_id']);

        if ($pelatihan->status !== 'tersedia') {
            return response()->json([
                'success' => false,
                'message' => 'Pelatihan ini sudah tidak menerima pendaftaran.',
            ], 422);
        }

        // Cegah duplikasi
        $sudahDaftar = PendaftaranModel::where('peserta_id', $user->id_user)
            ->where('pelatihan_id', $validated['pelatihan_id'])
            ->exists();

        if ($sudahDaftar) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah terdaftar di pelatihan ini.',
            ], 422);
        }

        $pendaftaran = PendaftaranModel::create(array_merge($validated, [
            'peserta_id' => $user->id_user,
            'status'     => 'menunggu',
            'tgl_daftar' => Carbon::now(),
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil dikirim. Menunggu konfirmasi admin.',
            'data'    => $pendaftaran,
        ], 201);
    }

    /* ═══════════════════════════════════════════════
     |  POST /api/pendaftaran/{id}/terima   [ADMIN]
     ═══════════════════════════════════════════════ */

    public function terima(Request $request, int $id): JsonResponse
    {
        $this->onlyAdmin($request);

        $pendaftaran = PendaftaranModel::with('pelatihan')->findOrFail($id);

        if ($pendaftaran->status !== 'menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pendaftaran berstatus "menunggu" yang dapat diproses.',
            ], 422);
        }

        // Cek kuota
        $terisiCount = PendaftaranModel::where('pelatihan_id', $pendaftaran->pelatihan_id)
            ->where('status', 'diterima')
            ->count();

        if ($terisiCount >= $pendaftaran->pelatihan->kuota) {
            $pendaftaran->pelatihan->update(['status' => 'penuh']);
            return response()->json([
                'success' => false,
                'message' => 'Kuota pelatihan sudah penuh.',
            ], 422);
        }

        $pendaftaran->update(['status' => 'diterima']);

        // Hubungkan peserta_id jika belum
        if (is_null($pendaftaran->peserta_id)) {
            $userPeserta = UserModel::where('email', $pendaftaran->email)->first();
            if ($userPeserta) {
                $pendaftaran->update(['peserta_id' => $userPeserta->id_user]);
            }
        }

        // Tandai pelatihan penuh jika kuota habis
        $terisBaru = PendaftaranModel::where('pelatihan_id', $pendaftaran->pelatihan_id)
            ->where('status', 'diterima')->count();
        if ($terisBaru >= $pendaftaran->pelatihan->kuota) {
            $pendaftaran->pelatihan->update(['status' => 'penuh']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil diterima.',
            'data'    => $pendaftaran->fresh(),
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  POST /api/pendaftaran/{id}/tolak   [ADMIN]
     ═══════════════════════════════════════════════ */

    public function tolak(Request $request, int $id): JsonResponse
    {
        $this->onlyAdmin($request);

        $pendaftaran = PendaftaranModel::findOrFail($id);

        if ($pendaftaran->status !== 'menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pendaftaran berstatus "menunggu" yang dapat ditolak.',
            ], 422);
        }

        $pendaftaran->update(['status' => 'ditolak']);

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran telah ditolak.',
            'data'    => $pendaftaran->fresh(),
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  DELETE /api/pendaftaran/{id}   [ADMIN]
     ═══════════════════════════════════════════════ */

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->onlyAdmin($request);

        $pendaftaran = PendaftaranModel::findOrFail($id);
        $pendaftaran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pendaftaran berhasil dihapus.',
        ]);
    }

    /* ─── Helper ─── */

    private function onlyAdmin(Request $request): void
    {
        if ($request->user()->role !== 'admin') {
            abort(response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403));
        }
    }

    private function forbidden(): JsonResponse
    {
        return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
    }
}
