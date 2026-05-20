<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PelatihanModel;
use App\Models\PendaftaranModel;
use App\Models\SertifikatModel;
use App\Models\LogbookModel;
use App\Models\UserModel;
use App\Models\KualifikasiSertifikasiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function __construct()
    {
        // Laporan hanya untuk admin
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman laporan.');
            }
            return $next($request);
        });
    }

    /* ═══════════════════════════════════════════════
     |  ADMIN – Laporan Utama
     ═══════════════════════════════════════════════ */

    /**
     * [ADMIN] Halaman laporan dengan ringkasan statistik dan berbagai data.
     */
    public function index(Request $request)
    {
        $tahun  = $request->input('tahun', Carbon::now()->year);
        $bulan  = $request->input('bulan'); // opsional, filter per bulan

        /* ── Statistik Utama ── */
        $totalPelatihan  = PelatihanModel::count();
        $totalPeserta    = UserModel::where('role', 'peserta')->count();
        $totalInstruktur = UserModel::where('role', 'instruktur')->count();
        $totalSertifikat = SertifikatModel::count();

        /* ── Grafik Pelatihan Bulanan ── */
        $grafikQuery = PelatihanModel::selectRaw('MONTH(tgl_mulai) as bulan, COUNT(*) as total')
            ->whereYear('tgl_mulai', $tahun)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $grafikBulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            $grafikBulanan[$i] = $grafikQuery[$i] ?? 0;
        }

        /* ── Grafik Pendaftaran per Status ── */
        $pendaftaranPerStatus = PendaftaranModel::selectRaw('status, COUNT(*) as total')
            ->whereYear('tgl_daftar', $tahun)
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        /* ── Pelatihan Paling Diminati ── */
        $topPelatihan = PelatihanModel::withCount(['pendaftaran' => fn($q) =>
            $q->where('status', 'diterima')
        ])
            ->orderByDesc('pendaftaran_count')
            ->limit(10)
            ->get();

        /* ── Laporan Kehadiran per Pelatihan ── */
        $rekapKehadiran = PelatihanModel::with(['sesiPelatihan'])
            ->whereYear('tgl_mulai', $tahun)
            ->get()
            ->map(function ($pelatihan) {
                $totalSesi = $pelatihan->sesiPelatihan->count();
                $totalHadir = LogbookModel::whereHas('sesiPelatihan', fn($q) =>
                    $q->where('pelatihan_id', $pelatihan->id_pelatihan)
                )->where('status', 'hadir')->count();

                $totalPeserta = PendaftaranModel::where('pelatihan_id', $pelatihan->id_pelatihan)
                    ->where('status', 'diterima')->count();

                $maxKehadiran = $totalSesi * $totalPeserta;
                $persenKehadiran = $maxKehadiran > 0
                    ? round(($totalHadir / $maxKehadiran) * 100, 1)
                    : 0;

                return [
                    'pelatihan'       => $pelatihan,
                    'total_sesi'      => $totalSesi,
                    'total_peserta'   => $totalPeserta,
                    'persen_kehadiran'=> $persenKehadiran,
                ];
            });

        /* ── Laporan Sertifikasi ── */
        $rekapSertifikasi = PelatihanModel::where('status', 'selesai')
            ->whereYear('tgl_selesai', $tahun)
            ->get()
            ->map(function ($pelatihan) {
                $pesertaDiterima = PendaftaranModel::where('pelatihan_id', $pelatihan->id_pelatihan)
                    ->where('status', 'diterima')->count();

                $memenuhiSyarat = KualifikasiSertifikasiModel::whereHas('pendaftaran', fn($q) =>
                    $q->where('pelatihan_id', $pelatihan->id_pelatihan)
                )->where('memenuhi_syarat', true)->count();

                $sertifikatTerbit = SertifikatModel::whereHas('pendaftaran', fn($q) =>
                    $q->where('pelatihan_id', $pelatihan->id_pelatihan)
                )->count();

                return [
                    'pelatihan'        => $pelatihan,
                    'peserta_diterima' => $pesertaDiterima,
                    'memenuhi_syarat'  => $memenuhiSyarat,
                    'sertifikat_terbit'=> $sertifikatTerbit,
                ];
            });

        /* ── Performa Instruktur ── */
        $performaInstruktur = UserModel::where('role', 'instruktur')
            ->withCount('pelatihan')
            ->with(['pelatihan' => fn($q) => $q->where('status', 'selesai')])
            ->get()
            ->map(function ($instr) {
                $totalPesertaDibimbing = PendaftaranModel::whereHas('pelatihan', fn($q) =>
                    $q->where('instruktur_id', $instr->id_user)
                )->where('status', 'diterima')->count();

                $totalSertifikat = SertifikatModel::whereHas('pendaftaran.pelatihan', fn($q) =>
                    $q->where('instruktur_id', $instr->id_user)
                )->count();

                return [
                    'instruktur'      => $instr,
                    'total_pelatihan' => $instr->pelatihan_count,
                    'peserta_dibimbing'=> $totalPesertaDibimbing,
                    'sertifikat'      => $totalSertifikat,
                ];
            });

        $tahunTersedia = PelatihanModel::selectRaw('YEAR(tgl_mulai) as tahun')
            ->whereNotNull('tgl_mulai')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('admin.laporan.index', compact(
            'tahun',
            'bulan',
            'totalPelatihan',
            'totalPeserta',
            'totalInstruktur',
            'totalSertifikat',
            'grafikBulanan',
            'pendaftaranPerStatus',
            'topPelatihan',
            'rekapKehadiran',
            'rekapSertifikasi',
            'performaInstruktur',
            'tahunTersedia'
        ));
    }

    /**
     * [ADMIN] Export laporan ke CSV / Excel (sederhana, tanpa package tambahan).
     * Untuk export lengkap, integrasikan dengan maatwebsite/excel atau spatie/laravel-export.
     */
    public function export(Request $request)
    {
        $request->validate([
            'jenis'  => 'required|in:pelatihan,pendaftaran,sertifikat,kehadiran',
            'format' => 'required|in:csv',
            'tahun'  => 'nullable|integer|min:2020|max:2100',
        ]);

        $tahun = $request->input('tahun', Carbon::now()->year);

        $data    = [];
        $headers = [];
        $nama    = '';

        switch ($request->jenis) {
            case 'pelatihan':
                $headers = ['ID', 'Kode', 'Nama Pelatihan', 'Kategori', 'Instruktur', 'Kuota', 'Tgl Mulai', 'Tgl Selesai', 'Status'];
                $rows    = PelatihanModel::with('instruktur')->whereYear('tgl_mulai', $tahun)->get();
                $data    = $rows->map(fn($p) => [
                    $p->id_pelatihan, $p->kode_pelatihan, $p->nama_pelatihan,
                    $p->kategori, $p->instruktur?->nama, $p->kuota,
                    $p->tgl_mulai, $p->tgl_selesai, $p->status,
                ])->toArray();
                $nama = "laporan_pelatihan_{$tahun}";
                break;

            case 'pendaftaran':
                $headers = ['ID', 'Nama', 'Email', 'Pelatihan', 'Perusahaan', 'Tgl Daftar', 'Status'];
                $rows    = PendaftaranModel::with('pelatihan')->whereYear('tgl_daftar', $tahun)->get();
                $data    = $rows->map(fn($p) => [
                    $p->id_pendaftaran,
                    trim($p->first_name . ' ' . $p->last_name),
                    $p->email,
                    $p->pelatihan?->nama_pelatihan,
                    $p->perusahaan,
                    $p->tgl_daftar,
                    $p->status,
                ])->toArray();
                $nama = "laporan_pendaftaran_{$tahun}";
                break;

            case 'sertifikat':
                $headers = ['Kode Sertifikat', 'Nama Peserta', 'Pelatihan', 'Tgl Terbit', 'Diterbitkan Oleh'];
                $rows    = SertifikatModel::with(['pendaftaran.pelatihan'])
                    ->whereYear('tgl_terbit', $tahun)->get();
                $data    = $rows->map(fn($s) => [
                    $s->kode_sertifikat,
                    trim(($s->pendaftaran->first_name ?? '') . ' ' . ($s->pendaftaran->last_name ?? '')),
                    $s->pendaftaran->pelatihan?->nama_pelatihan,
                    $s->tgl_terbit,
                    $s->diterbitkan_oleh,
                ])->toArray();
                $nama = "laporan_sertifikat_{$tahun}";
                break;

            case 'kehadiran':
                $headers = ['Peserta', 'Pelatihan', 'Sesi', 'Tanggal', 'Status', 'Catatan'];
                $rows    = LogbookModel::with(['peserta', 'sesiPelatihan.pelatihan'])
                    ->whereHas('sesiPelatihan', fn($q) => $q->whereYear('tanggal', $tahun))
                    ->get();
                $data    = $rows->map(fn($l) => [
                    $l->peserta?->nama,
                    $l->sesiPelatihan?->pelatihan?->nama_pelatihan,
                    $l->sesiPelatihan?->judul_sesi,
                    $l->sesiPelatihan?->tanggal,
                    $l->status,
                    $l->catatan,
                ])->toArray();
                $nama = "laporan_kehadiran_{$tahun}";
                break;
        }

        // Generate CSV response
        $callback = function () use ($headers, $data) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8 agar Excel terbaca dengan benar
            fputcsv($out, $headers, ';');
            foreach ($data as $row) {
                fputcsv($out, $row, ';');
            }
            fclose($out);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv;charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$nama}.csv\"",
        ]);
    }

    public function exportPdf(Request $request)
    {
        $tahun = $request->input('tahun', Carbon::now()->year);

        $topPelatihan = PelatihanModel::withCount(['pendaftaran' => fn($q) =>
            $q->where('status', 'diterima')
        ])->orderByDesc('pendaftaran_count')->limit(10)->get();

        $pdf = Pdf::loadView('admin.laporan.exportPdf', [
            'tahun'        => $tahun,
            'topPelatihan' => $topPelatihan,
            'totalPelatihan'  => PelatihanModel::count(),
            'totalPeserta'    => UserModel::where('role','peserta')->count(),
            'totalInstruktur' => UserModel::where('role','instruktur')->count(),
            'totalSertifikat' => SertifikatModel::count(),
            'dicetak_pada'    => Carbon::now()->translatedFormat('j F Y, H:i'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download("laporan_pelatihan_{$tahun}.pdf");
    }
}
