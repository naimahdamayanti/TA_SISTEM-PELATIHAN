<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\SesiPelatihanModel;
use Illuminate\Http\Request;

class SesiPelatihanController extends Controller
{
    public function index()
    {
        return SesiPelatihanModel::with('pelatihan')->get();
    }

    public function store(Request $request)
    {
        return SesiPelatihanModel::create($request->all());
    }

    public function show($id)
    {
        return SesiPelatihanModel::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $data = SesiPelatihanModel::findOrFail($id);
        $data->update($request->all());
        return $data;
    }

    public function destroy($id)
    {
        SesiPelatihanModel::destroy($id);
        return response()->json(['message' => 'Sesi dihapus']);
    }
}