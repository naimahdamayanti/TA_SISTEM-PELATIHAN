<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\SertifikatModel;
use App\Models\PendaftaranModel;
use Illuminate\Http\Request;

class SertifikatController extends Controller
{
    public function index()
    {
        return SertifikatModel::with('pendaftaran')->get();
    }

    public function store(Request $request)
    {
        $pendaftaran = PendaftaranModel::findOrFail($request->pendaftaran_id);

        if ($pendaftaran->status != 'diterima') {
            return response()->json(['message' => 'Belum disetujui'], 400);
        }

        return SertifikatModel::create([
            'pendaftaran_id' => $request->pendaftaran_id,
            'kode_sertifikat' => 'SERT-' . rand(1000,9999),
            'tgl_terbit' => now(),
            'diterbitkan_oleh' => 'Admin',
            'file' => 'sertifikat.pdf'
        ]);
    }

    public function cek($kode)
    {
        $data = SertifikatModel::where('kode_sertifikat', $kode)->first();

        if (!$data) {
            return response()->json(['message' => 'Tidak ditemukan'], 404);
        }

        return $data;
    }
}