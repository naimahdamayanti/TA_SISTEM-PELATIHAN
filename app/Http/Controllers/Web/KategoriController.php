<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\KategoriModel;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    // GET /admin/kategori
    public function index()
    {
        $kategoris = KategoriModel::withCount('pelatihan')
                              ->orderBy('nama_kategori')
                              ->paginate(20);

        return view('admin.pelatihan.index', compact('kategoris'));
    }

    // GET /admin/kategori/create
    public function create()
    {
        return view('admin.pelatihan.index');
    }

    // POST /admin/kategori
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori,nama_kategori',
            'aktif'         => 'nullable|boolean',
        ]);

        $validated['aktif'] = $validated['aktif'] ?? true;
        KategoriModel::create($validated);

        return redirect()->route('admin.kategori.index')
                         ->with('success', 'Kategori berhasil ditambahkan.');
    }

    // GET /admin/kategori/{id}/edit
    public function edit($id)
    {
        $kategori = KategoriModel::findOrFail($id);

        return view('admin.kategori.edit', compact('kategori'));
    }

    // PUT /admin/kategori/{id}
    public function update(Request $request, $id)
    {
        $kategori = KategoriModel::findOrFail($id);

        $validated = $request->validate([
            'nama_kategori' => "required|string|max:100|unique:kategori,nama_kategori,{$id},id_kategori",
            'aktif'         => 'nullable|boolean',
        ]);

        $validated['aktif'] = $validated['aktif'] ?? $kategori->aktif;
        $kategori->update($validated);

        return redirect()->route('admin.kategori.index')
                         ->with('success', 'Kategori berhasil diperbarui.');
    }

    // DELETE /admin/kategori/{id}
    public function destroy($id)
    {
        $kategori = KategoriModel::withCount('pelatihan')->findOrFail($id);

        if ($kategori->pelatihan_count > 0) {
            return back()->with('error',
                "Kategori tidak dapat dihapus karena masih digunakan oleh {$kategori->pelatihan_count} pelatihan.");
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')
                         ->with('success', 'Kategori berhasil dihapus.');
    }
}