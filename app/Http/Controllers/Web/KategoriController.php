<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\KategoriModel;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = KategoriModel::withCount('pelatihan')
                              ->orderBy('nama_kategori')
                              ->paginate(20);

        return redirect()->route('admin.pelatihan.index');
    }

    public function create()
    {
        return redirect()->route('admin.pelatihan.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori,nama_kategori',
            'aktif'         => 'nullable|boolean',
        ]);

        $validated['aktif'] = $validated['aktif'] ?? true;
        KategoriModel::create($validated);

        return redirect()->route('admin.pelatihan.index')
                         ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kategori = KategoriModel::findOrFail($id);

        return view('admin.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriModel::findOrFail($id);

        $validated = $request->validate([
            'nama_kategori' => "required|string|max:100|unique:kategori,nama_kategori,{$id},id_kategori",
            'aktif'         => 'nullable|boolean',
        ]);

        $validated['aktif'] = $validated['aktif'] ?? $kategori->aktif;
        $kategori->update($validated);

        return redirect()->route('admin.pelatihan.index')
                         ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kategori = KategoriModel::withCount('pelatihan')->findOrFail($id);

        if ($kategori->pelatihan_count > 0) {
            return back()->with('error',
                "Kategori tidak dapat dihapus karena masih digunakan oleh {$kategori->pelatihan_count} pelatihan.");
        }

        $kategori->delete();

        return redirect()->route('admin.pelatihan.index')
                         ->with('success', 'Kategori berhasil dihapus.');
    }
}