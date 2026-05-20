<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PelatihanModel;
use App\Models\PendaftaranModel;
use App\Models\SesiPelatihanModel;
use App\Models\UserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PelatihanController extends Controller
{
    /* ═══════════════════════════════════════════════
     |  GET /api/pelatihan
     |  Admin   → semua pelatihan + filter
     |  Instruktur → hanya pelatihan yang diampu
     |  Peserta → hanya yang status=tersedia (katalog)
     ═══════════════════════════════════════════════ */

    public function index(Request $request): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        $query = PelatihanModel::with('instruktur:id_user,nama,email,no_hp')
            ->withCount(['pendaftaran' => fn($q) => $q->where('status', 'diterima')]);

        match ($user->role) {
            'instruktur' => $query->where('instruktur_id', $user->id_user),
            'peserta'    => $query->where('status', 'tersedia'),
            default      => null, // admin → semua
        };

        // Filter opsional
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('search')) {
            $query->where(fn($q) =>
                $q->where('nama_pelatihan', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_pelatihan', 'like', '%' . $request->search . '%')
            );
        }

        $pelatihan = $query->latest()->paginate($request->input('per_page', 10));

        // Untuk peserta: tandai pelatihan yang sudah didaftarkan
        if ($user->role === 'peserta') {
            $sudahDaftar = PendaftaranModel::where('peserta_id', $user->id_user)
                ->pluck('pelatihan_id')
                ->toArray();

            $pelatihan->getCollection()->transform(function ($p) use ($sudahDaftar) {
                $p->sudah_daftar = in_array($p->id_pelatihan, $sudahDaftar);
                return $p;
            });
        }

        return response()->json([
            'success' => true,
            'data'    => $pelatihan,
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  GET /api/pelatihan/{id}
     ═══════════════════════════════════════════════ */

    public function show(Request $request, int $id): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        $pelatihan = PelatihanModel::with([
            'instruktur:id_user,nama,email,no_hp',
            'sesiPelatihan' => fn($q) => $q->orderBy('tanggal')->orderBy('waktu_mulai'),
        ])
            ->withCount(['pendaftaran' => fn($q) => $q->where('status', 'diterima')])
            ->findOrFail($id);

        // Instruktur hanya boleh lihat pelatihan miliknya
        if ($user->role === 'instruktur' && $pelatihan->instruktur_id !== $user->id_user) {
            return $this->forbidden();
        }

        $extra = [];
        if ($user->role === 'peserta') {
            $extra['sudah_daftar'] = PendaftaranModel::where('peserta_id', $user->id_user)
                ->where('pelatihan_id', $id)
                ->exists();
        }

        return response()->json([
            'success' => true,
            'data'    => array_merge($pelatihan->toArray(), $extra),
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  POST /api/pelatihan   [ADMIN]
     ═══════════════════════════════════════════════ */

    public function store(Request $request): JsonResponse
    {
        $this->onlyAdmin($request);

        $validated = $request->validate([
            'instruktur_id'  => 'required|exists:users,id_user',
            'nama_pelatihan' => 'required|string|max:100',
            'kode_pelatihan' => 'required|string|max:15|unique:pelatihan,kode_pelatihan',
            'kategori'       => 'required|string|max:50',
            'deskripsi'      => 'required|string',
            'kuota'          => 'required|integer|min:1|max:500',
            'tgl_mulai'      => 'nullable|date',
            'tgl_selesai'    => 'nullable|date|after_or_equal:tgl_mulai',
            'status'         => 'required|in:tersedia,penuh,selesai',
        ]);

        // Pastikan instruktur_id benar-benar role instruktur
        $instruktur = UserModel::where('id_user', $validated['instruktur_id'])
            ->where('role', 'instruktur')
            ->first();

        if (!$instruktur) {
            return response()->json([
                'success' => false,
                'message' => 'ID instruktur tidak valid.',
            ], 422);
        }

        $pelatihan = PelatihanModel::create($validated);
        $pelatihan->load('instruktur:id_user,nama');

        return response()->json([
            'success' => true,
            'message' => 'Pelatihan berhasil ditambahkan.',
            'data'    => $pelatihan,
        ], 201);
    }

    /* ═══════════════════════════════════════════════
     |  PUT /api/pelatihan/{id}   [ADMIN]
     ═══════════════════════════════════════════════ */

    public function update(Request $request, int $id): JsonResponse
    {
        $this->onlyAdmin($request);

        $pelatihan = PelatihanModel::findOrFail($id);

        $validated = $request->validate([
            'instruktur_id'  => 'sometimes|exists:users,id_user',
            'nama_pelatihan' => 'sometimes|string|max:100',
            'kode_pelatihan' => ['sometimes', 'string', 'max:15',
                Rule::unique('pelatihan', 'kode_pelatihan')->ignore($id, 'id_pelatihan'),
            ],
            'kategori'       => 'sometimes|string|max:50',
            'deskripsi'      => 'sometimes|string',
            'kuota'          => 'sometimes|integer|min:1|max:500',
            'tgl_mulai'      => 'nullable|date',
            'tgl_selesai'    => 'nullable|date|after_or_equal:tgl_mulai',
            'status'         => 'sometimes|in:tersedia,penuh,selesai',
        ]);

        $pelatihan->update($validated);
        $pelatihan->load('instruktur:id_user,nama');

        return response()->json([
            'success' => true,
            'message' => 'Pelatihan berhasil diperbarui.',
            'data'    => $pelatihan,
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  DELETE /api/pelatihan/{id}   [ADMIN]
     ═══════════════════════════════════════════════ */

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->onlyAdmin($request);

        $pelatihan = PelatihanModel::findOrFail($id);
        $pelatihan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pelatihan berhasil dihapus.',
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  GET /api/pelatihan/kategori   (dropdown)
     ═══════════════════════════════════════════════ */

    public function kategori(): JsonResponse
    {
        $kategori = PelatihanModel::distinct()->orderBy('kategori')->pluck('kategori');

        return response()->json([
            'success' => true,
            'data'    => $kategori,
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
