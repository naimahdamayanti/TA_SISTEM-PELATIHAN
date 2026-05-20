<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class KategoriController extends Controller
{
    // GET /api/kategori
    public function index(): JsonResponse
    {
        $kategoris = KategoriModel::withCount('pelatihan')
                              ->orderBy('kode_kategori')
                              ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data kategori berhasil diambil.',
            'data'    => $kategoris,
        ]);
    }

    // GET /api/kategori/aktif
    // Khusus untuk populate dropdown pada form pelatihan
    public function aktif(): JsonResponse
    {
        $kategoris = KategoriModel::aktif()->get(['id_kategori', 'kode_kategori', 'nama_kategori']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data kategori aktif berhasil diambil.',
            'data'    => $kategoris,
        ]);
    }

    // GET /api/kategori/{id}
    public function show($id): JsonResponse
    {
        $kategori = KategoriModel::withCount('pelatihan')->find($id);

        if (! $kategori) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kategori tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail kategori berhasil diambil.',
            'data'    => $kategori,
        ]);
    }

    // POST /api/kategori
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori,nama_kategori',
            'aktif'         => 'nullable|boolean',
        ]);

        $validated['aktif'] = $validated['aktif'] ?? true;
        $kategori = KategoriModel::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Kategori berhasil ditambahkan.',
            'data'    => $kategori,
        ], 201);
    }

    // PUT /api/kategori/{id}
    public function update(Request $request, $id): JsonResponse
    {
        $kategori = KategoriModel::find($id);

        if (! $kategori) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kategori tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'nama_kategori' => "required|string|max:100|unique:kategori,nama_kategori,{$id},id_kategori",
            'aktif'         => 'nullable|boolean',
        ]);

        $validated['aktif'] = $validated['aktif'] ?? $kategori->aktif;
        $kategori->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Kategori berhasil diperbarui.',
            'data'    => $kategori->fresh(),
        ]);
    }

    // DELETE /api/kategori/{id}
    public function destroy($id): JsonResponse
    {
        $kategori = KategoriModel::withCount('pelatihan')->find($id);

        if (! $kategori) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kategori tidak ditemukan.',
            ], 404);
        }

        if ($kategori->pelatihan_count > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => "Kategori tidak dapat dihapus karena masih digunakan oleh {$kategori->pelatihan_count} pelatihan.",
            ], 422);
        }

        $kategori->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Kategori berhasil dihapus.',
        ]);
    }
}