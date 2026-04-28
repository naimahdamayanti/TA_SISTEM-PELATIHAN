<?php

namespace App\Http\Controllers\Web;

use Illuminate\Support\Facades\DB;
use App\Models\UserModel;
use App\Models\LogbookModel;
use App\Models\PendaftaranModel;
use App\Models\PelatihanModel;
use App\Models\SesiPelatihanModel;
use App\Models\SertifikatModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
     public function index()
    {
        $user = (object) [
            'id' => session('id_user'),
            'nama' => session('nama', 'User'),
            'email' => session('email'),
            'role' => session('role', 'user')
        ];

        // ================= ADMIN =================
        if ($user->role === 'admin') {

            $totalPeserta = PendaftaranModel::count();

            $pelatihanBerlangsung = PelatihanModel::where('status', 'berlangsung')->count();
            $pelatihanSelesai = PelatihanModel::where('status', 'selesai')->count();

            $sertifikatTerkirim = SertifikatModel::count();

            $totalInstruktur = UserModel::where('role', 'instruktur')->count();

            // Grafik bulanan
            $grafik = PendaftaranModel::select(
                DB::raw('MONTH(tgl_daftar) as bulan'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total');

            // Top 5 pelatihan
            $topPelatihan = PendaftaranModel::selectRaw('pelatihan_id, count(*) as total')
                ->groupBy('pelatihan_id')
                ->orderByDesc('total')
                ->with('pelatihan')
                ->limit(5)
                ->get();

            return view('dashboard', compact(
                'user',
                'totalPeserta',
                'pelatihanBerlangsung',
                'pelatihanSelesai',
                'sertifikatTerkirim',
                'totalInstruktur',
                'grafik',
                'topPelatihan'
            ));
        }

        // ================= INSTRUKTUR =================
        if ($user->role === 'instruktur') {

            $pelatihanBerlangsung = SesiPelatihanModel::where('id_instruktur', $user->id)
                ->whereHas('pelatihan', fn($q) => $q->where('status', 'berlangsung'))
                ->count();

            $pelatihanSelesai = SesiPelatihanModel::where('id_instruktur', $user->id)
                ->whereHas('pelatihan', fn($q) => $q->where('status', 'selesai'))
                ->count();

            $totalPeserta = PendaftaranModel::whereHas('pelatihan.sesi', function ($q) use ($user) {
                $q->where('id_instruktur', $user->id);
            })->count();

            $totalSertifikat = SertifikatModel::whereHas('pelatihan.sesi', function ($q) use ($user) {
                $q->where('id_instruktur', $user->id);
            })->count();

            return view('dashboard', compact(
                'user',
                'pelatihanBerlangsung',
                'pelatihanSelesai',
                'totalPeserta',
                'totalSertifikat'
            ));
        }

        // ================= PESERTA =================
        if ($user->role === 'user') {

            $pelatihanTerdaftar = PendaftaranModel::where('id_user', $user->id)->count();

            $pelatihanBerlangsung = PendaftaranModel::where('id_user', $user->id)
                ->whereHas('pelatihan', fn($q) => $q->where('status', 'berlangsung'))
                ->count();

            $pelatihanSelesai = PendaftaranModel::where('id_user', $user->id)
                ->whereHas('pelatihan', fn($q) => $q->where('status', 'selesai'))
                ->count();

            $sertifikat = SertifikatModel::where('id_user', $user->id)->count();

            return view('dashboard', compact(
                'user',
                'pelatihanTerdaftar',
                'pelatihanBerlangsung',
                'pelatihanSelesai',
                'sertifikat'
            ));
        }

        // fallback
        return view('dashboard', compact('user'));
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
