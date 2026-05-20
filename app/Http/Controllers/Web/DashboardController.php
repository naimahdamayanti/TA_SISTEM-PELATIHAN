<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\PelatihanModel;
use App\Models\PendaftaranModel;
use App\Models\SertifikatModel;
use App\Models\SesiPelatihanModel;
use App\Models\LogbookModel;
use App\Models\KualifikasiSertifikasiModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Redirect ke dashboard sesuai role pengguna yang sedang login.
     */
    public function index()
    {
        $user = Auth::user();

        return match ($user->role) {
            'admin'      => $this->dashboardAdmin(),
            'instruktur' => $this->dashboardInstruktur(),
            'peserta'    => $this->dashboardPeserta(),
            default      => abort(403, 'Role tidak dikenali.'),
        };
    }

    /* ──────────────────────────────────────────
     |  ADMIN DASHBOARD
     ────────────────────────────────────────── */
    private function dashboardAdmin()
    {

        $totalPeserta     = UserModel::where('role', 'peserta')->count();
        $totalInstruktur  = UserModel::where('role', 'instruktur')->count();
        $pelatihanAktif   = PelatihanModel::where('status', 'tersedia')->count();
        $pelatihanSelesai = PelatihanModel::where('status', 'selesai')->count();
        $totalSertifikat  = SertifikatModel::count();

        // Grafik: jumlah pelatihan yang mulai per bulan di tahun berjalan
        $tahun = request('tahun', Carbon::now()->year);
        $grafikBulanan = PelatihanModel::selectRaw('MONTH(tgl_mulai) as bulan, COUNT(*) as total')
            ->whereYear('tgl_mulai', $tahun)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        // Susun data 12 bulan (isi 0 untuk bulan yang kosong)
        $grafikData = [];
        for ($i = 1; $i <= 12; $i++) {
            $grafikData[$i] = $grafikBulanan[$i] ?? 0;
        }

        // Top pelatihan berdasarkan jumlah pendaftar
        $topPelatihan = PelatihanModel::withCount(['pendaftaran' => function ($q) {
            $q->where('status', 'diterima');
        }])
            ->orderByDesc('pendaftaran_count')
            ->limit(5)
            ->get();

        // Pendaftaran menunggu konfirmasi
        $pendaftaranMenunggu = PendaftaranModel::with(['peserta', 'pelatihan'])
            ->where('status', 'menunggu')
            ->latest('tgl_daftar')
            ->limit(10)
            ->get();

        return view('dashboard.admin', compact(
            'totalPeserta',
            'totalInstruktur',
            'pelatihanAktif',
            'pelatihanSelesai',
            'totalSertifikat',
            'grafikData',
            'tahun',
            'topPelatihan',
            'pendaftaranMenunggu'
        ));
    }

    /* ──────────────────────────────────────────
     |  INSTRUKTUR DASHBOARD
     ────────────────────────────────────────── */
    private function dashboardInstruktur()
    {
        $instruktur = Auth::user();

        $pelatihan = PelatihanModel::where('instruktur_id', $instruktur->id_user)
            ->withCount(['pendaftaran' => fn($q) => $q->where('status', 'diterima')])
            ->get();

        $totalPelatihan   = $pelatihan->count();
        $pelatihanAktif   = $pelatihan->where('status', 'tersedia')->count();
        $pelatihanSelesai = $pelatihan->where('status', 'selesai')->count();

        // Sesi terdekat yang akan datang
        $sesiMendatang = SesiPelatihanModel::whereHas('pelatihan', fn($q) =>
            $q->where('instruktur_id', $instruktur->id_user)
        )
            ->where('tanggal', '>=', Carbon::today())
            ->with('pelatihan')
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->limit(5)
            ->get();

        // Jumlah peserta yang sudah diisi logbook-nya (kehadiran hari ini)
        $logbookHariIni = LogbookModel::where('instruktur_id', $instruktur->id_user)
            ->whereHas('sesiPelatihan', fn($q) => $q->whereDate('tanggal', Carbon::today()))
            ->count();

        // Peserta menunggu penilaian kelayakan
        $menungguKelayakan = PendaftaranModel::whereHas('pelatihan', fn($q) =>
            $q->where('instruktur_id', $instruktur->id_user)->where('status', 'selesai')
        )
            ->where('status', 'diterima')
            ->whereDoesntHave('kualifikasiSertifikasi')
            ->count();

        return view('dashboard.instruktur', compact(
            'pelatihan',
            'totalPelatihan',
            'pelatihanAktif',
            'pelatihanSelesai',
            'sesiMendatang',
            'logbookHariIni',
            'menungguKelayakan'
        ));
    }

    /* ──────────────────────────────────────────
     |  PESERTA DASHBOARD
     ────────────────────────────────────────── */
    private function dashboardPeserta()
    {
        $peserta = Auth::user();

        // Pendaftaran peserta beserta status
        $pendaftaran = PendaftaranModel::with(['pelatihan'])
            ->where('peserta_id', $peserta->id_user)
            ->latest('tgl_daftar')
            ->get();

        $totalDaftar    = $pendaftaran->count();
        $diterima       = $pendaftaran->where('status', 'diterima')->count();
        $menunggu       = $pendaftaran->where('status', 'menunggu')->count();
        $totalSertifikat = SertifikatModel::whereHas('pendaftaran', fn($q) =>
            $q->where('peserta_id', $peserta->id_user)
        )->count();

        // Pelatihan yang sedang diikuti (status tersedia, pendaftaran diterima)
        $pelatihanAktif = $pendaftaran->filter(fn($p) =>
            $p->status === 'diterima' && $p->pelatihan?->status === 'tersedia'
        );

        // Sesi mendatang untuk peserta ini
        $sesiMendatang = SesiPelatihanModel::whereHas('pelatihan.pendaftaran', fn($q) =>
            $q->where('peserta_id', $peserta->id_user)->where('status', 'diterima')
        )
            ->where('tanggal', '>=', Carbon::today())
            ->with('pelatihan')
            ->orderBy('tanggal')
            ->limit(3)
            ->get();

        return view('dashboard.peserta', compact(
            'pendaftaran',
            'totalDaftar',
            'diterima',
            'menunggu',
            'totalSertifikat',
            'pelatihanAktif',
            'sesiMendatang'
        ));
    }
}
