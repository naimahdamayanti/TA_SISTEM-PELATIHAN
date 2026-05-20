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
use Carbon\Carbon;

class SesiPelatihanController extends Controller
{
    /* ═══════════════════════════════════════════════
     |  GET /api/sesi
     |  Admin      → semua sesi (bisa filter pelatihan_id)
     |  Instruktur → sesi dari pelatihan yang diampu
     |  Peserta    → sesi dari pelatihan yang diikuti
     ═══════════════════════════════════════════════ */

    public function index(Request $request): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        $query = SesiPelatihanModel::with('pelatihan:id_pelatihan,nama_pelatihan,kode_pelatihan,instruktur_id')
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai');

        match ($user->role) {
            'instruktur' => $query->whereHas('pelatihan', fn($q) =>
                $q->where('instruktur_id', $user->id_user)
            ),
            'peserta' => $query->whereHas('pelatihan.pendaftaran', fn($q) =>
                $q->where('peserta_id', $user->id_user)->where('status', 'diterima')
            ),
            default => null, // admin → semua
        };

        if ($request->filled('pelatihan_id')) {
            $query->where('pelatihan_id', $request->pelatihan_id);
        }
        if ($request->filled('dari')) {
            $query->where('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->where('tanggal', '<=', $request->sampai);
        }
        // Hanya sesi mendatang
        if ($request->boolean('mendatang')) {
            $query->where('tanggal', '>=', Carbon::today());
        }

        $sesi = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $sesi,
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  GET /api/sesi/{id}
     ═══════════════════════════════════════════════ */

    public function show(Request $request, int $id): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        $sesi = SesiPelatihanModel::with('pelatihan.instruktur:id_user,nama')->findOrFail($id);

        // Instruktur: hanya bisa lihat sesi dari pelatihannya
        if ($user->role === 'instruktur' && $sesi->pelatihan->instruktur_id !== $user->id_user) {
            return $this->forbidden();
        }

        // Peserta: hanya bisa lihat sesi dari pelatihan yang diikutinya
        if ($user->role === 'peserta') {
            $terdaftar = PendaftaranModel::where('peserta_id', $user->id_user)
                ->where('pelatihan_id', $sesi->pelatihan_id)
                ->where('status', 'diterima')
                ->exists();

            if (!$terdaftar) {
                return $this->forbidden();
            }

            // Tambahkan status kehadiran peserta di sesi ini
            $logbook = LogbookModel::where('sesi_id', $id)
                ->where('peserta_id', $user->id_user)
                ->first(['status', 'catatan']);

            return response()->json([
                'success'          => true,
                'data'             => $sesi,
                'status_kehadiran' => $logbook?->status ?? 'belum dicatat',
                'catatan'          => $logbook?->catatan,
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => $sesi,
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  POST /api/sesi   [ADMIN]
     ═══════════════════════════════════════════════ */

    public function store(Request $request): JsonResponse
    {
        $this->onlyAdmin($request);

        $validated = $request->validate([
            'pelatihan_id'  => 'required|exists:pelatihan,id_pelatihan',
            'judul_sesi'    => 'nullable|string|max:100',
            'tanggal'       => 'required|date',
            'waktu_mulai'   => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'lokasi'        => 'required|string|max:255',
        ]);

        $sesi = SesiPelatihanModel::create($validated);
        $sesi->load('pelatihan:id_pelatihan,nama_pelatihan');

        return response()->json([
            'success' => true,
            'message' => 'Sesi berhasil ditambahkan.',
            'data'    => $sesi,
        ], 201);
    }

    /* ═══════════════════════════════════════════════
     |  PUT /api/sesi/{id}   [ADMIN]
     ═══════════════════════════════════════════════ */

    public function update(Request $request, int $id): JsonResponse
    {
        $this->onlyAdmin($request);

        $sesi = SesiPelatihanModel::findOrFail($id);

        $validated = $request->validate([
            'judul_sesi'    => 'nullable|string|max:100',
            'tanggal'       => 'sometimes|date',
            'waktu_mulai'   => 'sometimes|date_format:H:i',
            'waktu_selesai' => 'sometimes|date_format:H:i|after:waktu_mulai',
            'lokasi'        => 'sometimes|string|max:255',
        ]);

        $sesi->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Sesi berhasil diperbarui.',
            'data'    => $sesi,
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  DELETE /api/sesi/{id}   [ADMIN]
     ═══════════════════════════════════════════════ */

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->onlyAdmin($request);

        $sesi = SesiPelatihanModel::findOrFail($id);
        $sesi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesi berhasil dihapus.',
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
