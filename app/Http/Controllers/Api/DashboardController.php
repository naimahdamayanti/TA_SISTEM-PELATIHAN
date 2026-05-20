<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LogbookModel;
use App\Models\PelatihanModel;
use App\Models\PendaftaranModel;
use App\Models\SertifikatModel;
use App\Models\SesiPelatihanModel;
use App\Models\UserModel;
use App\Models\KualifikasiSertifikasiModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /* ═══════════════════════════════════════════════
     |  GET /api/dashboard
     |  Dispatch otomatis berdasarkan role token
     ═══════════════════════════════════════════════ */

    public function index(Request $request): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        return match ($user->role) {
            'admin'      => $this->admin($user),
            'instruktur' => $this->instruktur($user),
            'peserta'    => $this->peserta($user),
            default      => response()->json(['message' => 'Role tidak dikenali.'], 403),
        };
    }

    /* ─── Admin ─── */

    private function admin(UserModel $user): JsonResponse
    {
        $tahun = request('tahun', Carbon::now()->year);

        $grafikRaw = PelatihanModel::selectRaw('MONTH(tgl_mulai) as bulan, COUNT(*) as total')
            ->whereYear('tgl_mulai', $tahun)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $grafik = [];
        for ($i = 1; $i <= 12; $i++) {
            $grafik[] = ['bulan' => $i, 'total' => $grafikRaw[$i] ?? 0];
        }

        $topPelatihan = PelatihanModel::withCount(['pendaftaran' => fn($q) => $q->where('status', 'diterima')])
            ->orderByDesc('pendaftaran_count')
            ->limit(5)
            ->get(['id_pelatihan', 'nama_pelatihan', 'kode_pelatihan', 'kategori', 'status'])
            ->map(fn($p) => array_merge($p->toArray(), ['total_peserta' => $p->pendaftaran_count]));

        return response()->json([
            'success' => true,
            'role'    => 'admin',
            'data'    => [
                'statistik' => [
                    'total_peserta'     => UserModel::where('role', 'peserta')->count(),
                    'total_instruktur'  => UserModel::where('role', 'instruktur')->count(),
                    'pelatihan_aktif'   => PelatihanModel::where('status', 'tersedia')->count(),
                    'pelatihan_selesai' => PelatihanModel::where('status', 'selesai')->count(),
                    'total_sertifikat'  => SertifikatModel::count(),
                    'menunggu_konfirmasi' => PendaftaranModel::where('status', 'menunggu')->count(),
                ],
                'grafik_bulanan' => $grafik,
                'tahun'          => $tahun,
                'top_pelatihan'  => $topPelatihan,
            ],
        ]);
    }

    /* ─── Instruktur ─── */

    private function instruktur(UserModel $user): JsonResponse
    {
        $pelatihan = PelatihanModel::where('instruktur_id', $user->id_user)
            ->withCount(['pendaftaran' => fn($q) => $q->where('status', 'diterima')])
            ->get();

        $sesiMendatang = SesiPelatihanModel::whereHas('pelatihan', fn($q) =>
            $q->where('instruktur_id', $user->id_user)
        )
            ->where('tanggal', '>=', Carbon::today())
            ->with('pelatihan:id_pelatihan,nama_pelatihan,kode_pelatihan')
            ->orderBy('tanggal')->orderBy('waktu_mulai')
            ->limit(5)
            ->get();

        $menungguKelayakan = PendaftaranModel::whereHas('pelatihan', fn($q) =>
            $q->where('instruktur_id', $user->id_user)->where('status', 'selesai')
        )
            ->where('status', 'diterima')
            ->whereDoesntHave('kualifikasiSertifikasi')
            ->count();

        return response()->json([
            'success' => true,
            'role'    => 'instruktur',
            'data'    => [
                'statistik' => [
                    'total_pelatihan'    => $pelatihan->count(),
                    'pelatihan_aktif'    => $pelatihan->where('status', 'tersedia')->count(),
                    'pelatihan_selesai'  => $pelatihan->where('status', 'selesai')->count(),
                    'menunggu_kelayakan' => $menungguKelayakan,
                ],
                'sesi_mendatang' => $sesiMendatang,
                'pelatihan'      => $pelatihan,
            ],
        ]);
    }

    /* ─── Peserta ─── */

    private function peserta(UserModel $user): JsonResponse
    {
        $pendaftaran = PendaftaranModel::with('pelatihan:id_pelatihan,nama_pelatihan,kode_pelatihan,status,tgl_mulai,tgl_selesai')
            ->where('peserta_id', $user->id_user)
            ->latest('tgl_daftar')
            ->get();

        $sesiMendatang = SesiPelatihanModel::whereHas('pelatihan.pendaftaran', fn($q) =>
            $q->where('peserta_id', $user->id_user)->where('status', 'diterima')
        )
            ->where('tanggal', '>=', Carbon::today())
            ->with('pelatihan:id_pelatihan,nama_pelatihan')
            ->orderBy('tanggal')
            ->limit(3)
            ->get();

        $totalSertifikat = SertifikatModel::whereHas('pendaftaran', fn($q) =>
            $q->where('peserta_id', $user->id_user)
        )->count();

        return response()->json([
            'success' => true,
            'role'    => 'peserta',
            'data'    => [
                'statistik' => [
                    'total_daftar'    => $pendaftaran->count(),
                    'diterima'        => $pendaftaran->where('status', 'diterima')->count(),
                    'menunggu'        => $pendaftaran->where('status', 'menunggu')->count(),
                    'ditolak'         => $pendaftaran->where('status', 'ditolak')->count(),
                    'total_sertifikat'=> $totalSertifikat,
                ],
                'pendaftaran_terbaru' => $pendaftaran->take(5)->values(),
                'sesi_mendatang'      => $sesiMendatang,
            ],
        ]);
    }
}
