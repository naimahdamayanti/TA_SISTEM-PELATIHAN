<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\PendaftaranModel;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function index()
    {
        return PendaftaranModel::with(['peserta','pelatihan'])->get();
    }

    public function store(Request $request)
    {
        return PendaftaranModel::create([
            ...$request->all(),
            'status' => 'menunggu'
        ]);
    }

    public function approve($id)
    {
        $data = PendaftaranModel::findOrFail($id);
        $data->update(['status' => 'diterima']);

        return response()->json(['message' => 'Disetujui']);
    }

    public function destroy($id)
    {
        PendaftaranModel::destroy($id);
        return response()->json(['message' => 'Dihapus']);
    }
}