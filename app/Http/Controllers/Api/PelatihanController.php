<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\PelatihanModel;
use Illuminate\Http\Request;

class PelatihanController extends Controller
{
    public function index()
    {
        return PelatihanModel::with('instruktur')->get();
    }

    public function store(Request $request)
    {
        return PelatihanModel::create($request->all());
    }

    public function show($id)
    {
        return PelatihanModel::with('sesi')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $data = PelatihanModel::findOrFail($id);
        $data->update($request->all());
        return $data;
    }

    public function destroy($id)
    {
        PelatihanModel::destroy($id);
        return response()->json(['message' => 'Pelatihan dihapus']);
    }
}