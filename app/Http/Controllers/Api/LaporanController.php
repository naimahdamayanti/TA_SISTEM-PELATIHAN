<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KualifikasiSertifikasiModel;
use App\Models\LogbookModel;
use App\Models\PelatihanModel;
use App\Models\PendaftaranModel;
use App\Models\SertifikatModel;
use App\Models\UserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /* ═══════════════════════════════════════════════
     |  GET /api/laporan   [ADMIN]
     |  Statistik lengkap untuk halaman laporan
     ═══════════════════════════════════════════════ */

    public function index(Request $request): JsonResponse
    {
        $this->onlyAdmin($request);

        $tahun = (int) $request->input('tahun', Carbon::now()->year);

        /* ── Statistik global ── */
        $statistik = [
            'total_pelatihan'  => PelatihanModel::count(),
            'total_peserta'    => UserModel::where('role', 'peserta')->count(),
            'total_instruktur' => UserModel::where('role', 'instruktur')->count(),
            'total_sertifikat' => SertifikatModel::count(),
        ];

        /* ── Grafik pelatihan per bulan ── */
        $grafikRaw = PelatihanModel::selectRaw('MONTH(tgl_mulai) as bulan, COUNT(*) as total')
            ->whereYear('tgl_mulai', $tahun)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $grafikBulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            $grafikBulanan[] = ['bulan' => $i, 'total' => $grafikRaw[$i] ?? 0];
        }

        /* ── Pendaftaran per status ── */
        $pendaftaranPerStatus = PendaftaranModel::selectRaw('status, COUNT(*) as total')
            ->whereYear('tgl_daftar', $tahun)
            ->groupBy('status')
            ->pluck('total', 'status');

        /* ── Top pelatihan diminati ── */
        $topPelatihan = PelatihanModel::withCount(['pendaftaran' => fn($q) =>
            $q->where('status', 'diterima')
        ])
            ->orderByDesc('pendaftaran_count')
            ->limit(10)
            ->get(['id_pelatihan', 'nama_pelatihan', 'kode_pelatihan', 'kategori', 'status'])
            ->map(fn($p) => [
                'id_pelatihan'   => $p->id_pelatihan,
                'nama_pelatihan' => $p->nama_pelatihan,
                'kode_pelatihan' => $p->kode_pelatihan,
                'kategori'       => $p->kategori,
                'status'         => $p->status,
                'total_peserta'  => $p->pendaftaran_count,
            ]);

        /* ── Rekap kehadiran per pelatihan ── */
        $rekapKehadiran = PelatihanModel::with('sesiPelatihan')
            ->whereYear('tgl_mulai', $tahun)
            ->get()
            ->map(function ($pelatihan) {
                $totalSesi    = $pelatihan->sesiPelatihan->count();
                $totalPeserta = PendaftaranModel::where('pelatihan_id', $pelatihan->id_pelatihan)
                    ->where('status', 'diterima')->count();
                $totalHadir   = LogbookModel::whereHas('sesi', fn($q) =>
                    $q->where('pelatihan_id', $pelatihan->id_pelatihan)
                )->where('status', 'hadir')->count();

                $maxKehadiran    = $totalSesi * $totalPeserta;
                $persenKehadiran = $maxKehadiran > 0
                    ? round(($totalHadir / $maxKehadiran) * 100, 1)
                    : 0;

                return [
                    'id_pelatihan'     => $pelatihan->id_pelatihan,
                    'nama_pelatihan'   => $pelatihan->nama_pelatihan,
                    'total_sesi'       => $totalSesi,
                    'total_peserta'    => $totalPeserta,
                    'persen_kehadiran' => $persenKehadiran,
                ];
            });

        /* ── Rekap sertifikasi per pelatihan ── */
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
                    'id_pelatihan'     => $pelatihan->id_pelatihan,
                    'nama_pelatihan'   => $pelatihan->nama_pelatihan,
                    'peserta_diterima' => $pesertaDiterima,
                    'memenuhi_syarat'  => $memenuhiSyarat,
                    'sertifikat_terbit'=> $sertifikatTerbit,
                ];
            });

        /* ── Performa instruktur ── */
        $performaInstruktur = UserModel::where('role', 'instruktur')
            ->withCount('pelatihan')
            ->get()
            ->map(function ($instr) {
                $totalPeserta = PendaftaranModel::whereHas('pelatihan', fn($q) =>
                    $q->where('instruktur_id', $instr->id_user)
                )->where('status', 'diterima')->count();

                $totalSertifikat = SertifikatModel::whereHas('pendaftaran.pelatihan', fn($q) =>
                    $q->where('instruktur_id', $instr->id_user)
                )->count();

                return [
                    'id_user'          => $instr->id_user,
                    'nama'             => $instr->nama,
                    'email'            => $instr->email,
                    'total_pelatihan'  => $instr->pelatihan_count,
                    'peserta_dibimbing'=> $totalPeserta,
                    'sertifikat'       => $totalSertifikat,
                ];
            });

        /* ── Tahun tersedia untuk filter dropdown ── */
        $tahunTersedia = PelatihanModel::selectRaw('YEAR(tgl_mulai) as tahun')
            ->whereNotNull('tgl_mulai')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return response()->json([
            'success' => true,
            'tahun'   => $tahun,
            'data'    => [
                'statistik'           => $statistik,
                'grafik_bulanan'      => $grafikBulanan,
                'pendaftaran_status'  => $pendaftaranPerStatus,
                'top_pelatihan'       => $topPelatihan,
                'rekap_kehadiran'     => $rekapKehadiran,
                'rekap_sertifikasi'   => $rekapSertifikasi,
                'performa_instruktur' => $performaInstruktur,
            ],
            'tahun_tersedia' => $tahunTersedia,
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  GET /api/laporan/export?jenis=pelatihan&tahun=2026
     |  Export CSV — Admin only
     ═══════════════════════════════════════════════ */

    public function export(Request $request)
    {
        $this->onlyAdmin($request);

        $request->validate([
            'jenis'  => 'required|in:pelatihan,pendaftaran,sertifikat,kehadiran',
            'tahun'  => 'nullable|integer|min:2020|max:2100',
        ]);

        $tahun = (int) $request->input('tahun', Carbon::now()->year);

        [$headers, $rows, $namaFile] = match ($request->jenis) {

            'pelatihan' => [
                ['ID', 'Kode', 'Nama Pelatihan', 'Kategori', 'Instruktur', 'Kuota', 'Tgl Mulai', 'Tgl Selesai', 'Status'],
                PelatihanModel::with('instruktur')->whereYear('tgl_mulai', $tahun)->get()
                    ->map(fn($p) => [
                        $p->id_pelatihan, $p->kode_pelatihan, $p->nama_pelatihan,
                        $p->kategori, $p->instruktur?->nama, $p->kuota,
                        $p->tgl_mulai, $p->tgl_selesai, $p->status,
                    ])->toArray(),
                "laporan_pelatihan_{$tahun}",
            ],

            'pendaftaran' => [
                ['ID', 'Nama', 'Email', 'Pelatihan', 'Perusahaan', 'Tgl Daftar', 'Status'],
                PendaftaranModel::with('pelatihan')->whereYear('tgl_daftar', $tahun)->get()
                    ->map(fn($p) => [
                        $p->id_pendaftaran,
                        trim($p->first_name . ' ' . $p->last_name),
                        $p->email,
                        $p->pelatihan?->nama_pelatihan,
                        $p->perusahaan,
                        $p->tgl_daftar,
                        $p->status,
                    ])->toArray(),
                "laporan_pendaftaran_{$tahun}",
            ],

            'sertifikat' => [
                ['Kode Sertifikat', 'Nama Peserta', 'Pelatihan', 'Tgl Terbit', 'Diterbitkan Oleh'],
                SertifikatModel::with(['pendaftaran.pelatihan'])->whereYear('tgl_terbit', $tahun)->get()
                    ->map(fn($s) => [
                        $s->kode_sertifikat,
                        trim(($s->pendaftaran->first_name ?? '') . ' ' . ($s->pendaftaran->last_name ?? '')),
                        $s->pendaftaran->pelatihan?->nama_pelatihan,
                        $s->tgl_terbit,
                        $s->diterbitkan_oleh,
                    ])->toArray(),
                "laporan_sertifikat_{$tahun}",
            ],

            'kehadiran' => [
                ['Peserta', 'Pelatihan', 'Sesi', 'Tanggal', 'Status', 'Catatan'],
                LogbookModel::with(['peserta', 'sesi.pelatihan'])
                    ->whereHas('sesi', fn($q) => $q->whereYear('tanggal', $tahun))
                    ->get()
                    ->map(fn($l) => [
                        $l->peserta?->nama,
                        $l->sesi?->pelatihan?->nama_pelatihan,
                        $l->sesi?->judul_sesi,
                        $l->sesi?->tanggal,
                        $l->status,
                        $l->catatan,
                    ])->toArray(),
                "laporan_kehadiran_{$tahun}",
            ],
        };

        $callback = function () use ($headers, $rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
            fputcsv($out, $headers, ';');
            foreach ($rows as $row) {
                fputcsv($out, $row, ';');
            }
            fclose($out);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv;charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$namaFile}.csv\"",
        ]);
    }

    /* ─── Helper ─── */

    private function onlyAdmin(Request $request): void
    {
        if ($request->user()->role !== 'admin') {
            abort(response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403));
        }
    }
}
