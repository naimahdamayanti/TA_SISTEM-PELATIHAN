<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SertifikatModel;
use App\Models\PendaftaranModel;
use App\Models\KualifikasiSertifikasiModel;
use App\Models\PelatihanModel;
use Barryvdh\DomPDF\Facade\Pdf;          // <-- import facade DomPDF
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SertifikatController extends Controller
{
    /* ═══════════════════════════════════════════════
     |  PUBLIC – Cek Sertifikat (tanpa login)
     ═══════════════════════════════════════════════ */

    public function cekForm()
    {
        return view('cek-sertifikat');
    }

    public function cekKode(Request $request)
    {
        $request->validate([
            'kode_sertifikat' => 'required|string|max:20',
        ]);

        $sertifikat = SertifikatModel::with(['pendaftaran.peserta', 'pendaftaran.pelatihan'])
            ->where('kode_sertifikat', strtoupper(trim($request->kode_sertifikat)))
            ->first();

        if (!$sertifikat) {
            return back()
                ->withInput()
                ->with('error', 'Kode sertifikat tidak ditemukan.');
        }

        return view('cek-sertifikat', compact('sertifikat'));
    }

    /* ═══════════════════════════════════════════════
     |  ADMIN – Daftar Sertifikat
     ══════════════════════════════════════════════ */

    public function index(Request $request)
    {
        $this->authorizeRole(['admin']);

        $query = SertifikatModel::with(['pendaftaran.peserta', 'pendaftaran.pelatihan'])
            ->latest('tgl_terbit');

        if ($request->filled('search')) {
            $query->where('kode_sertifikat', 'like', '%' . $request->search . '%')
                ->orWhereHas('pendaftaran', fn($q) =>
                    $q->where('first_name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%')
                );
        }

        if ($request->filled('pelatihan_id')) {
            $query->whereHas('pendaftaran', fn($q) =>
                $q->where('pelatihan_id', $request->pelatihan_id)
            );
        }

        $sertifikat = $query->paginate(15)->withQueryString();
        $pelatihan  = PelatihanModel::orderBy('nama_pelatihan')->get();

        $menungguSertifikat = KualifikasiSertifikasiModel::where('memenuhi_syarat', true)
            ->whereDoesntHave('pendaftaran.sertifikat')
            ->count();

        return view('admin.sertifikat.index', compact('sertifikat', 'pelatihan', 'menungguSertifikat'));
    }

    public function uploadTemplate(Request $request, PelatihanModel $pelatihan)
    {
        $this->authorizeRole(['admin']);

        $request->validate([
            'template' => 'required|image|mimes:png,jpg,jpeg|max:5120',
        ]);

        if ($pelatihan->template_sertifikat) {
            Storage::disk('public')->delete($pelatihan->template_sertifikat);
        }

        $path = $request->file('template')->store('template-sertifikat', 'public');
        $pelatihan->update(['template_sertifikat' => $path]);

        return back()->with('success', 'Template sertifikat berhasil diupload.');
    }   

    /* ═══════════════════════════════════════════════
     |  ADMIN – Preview & Generate Sertifikat
     ═══════════════════════════════════════════════ */

    public function preview(PendaftaranModel $pendaftaran)
    {
        $this->authorizeRole(['admin']);
        $pendaftaran->load(['peserta', 'pelatihan.instruktur', 'kualifikasiSertifikasi']);

        if (!$pendaftaran->kualifikasiSertifikasi?->memenuhi_syarat) {
            return back()->with('error', 'Peserta ini belum memenuhi syarat sertifikasi.');
        }

        $sudahAda = SertifikatModel::where('pendaftaran_id', $pendaftaran->id_pendaftaran)->exists();

        return view('admin.sertifikat.preview', compact('pendaftaran', 'sudahAda'));
    }

    public function generate(Request $request, PendaftaranModel $pendaftaran)
    {
        $this->authorizeRole(['admin']);
        $pendaftaran->load(['peserta', 'pelatihan.instruktur', 'kualifikasiSertifikasi']);

        if (!$pendaftaran->kualifikasiSertifikasi?->memenuhi_syarat)
            return back()->with('error', 'Peserta belum memenuhi syarat sertifikasi.');

        if (SertifikatModel::where('pendaftaran_id', $pendaftaran->id_pendaftaran)->exists())
            return back()->with('info', 'Sertifikat sudah pernah diterbitkan.');

        $request->validate(['diterbitkan_oleh' => 'required|string|max:100']);

        do {
            $kode = 'CERT-' . strtoupper($pendaftaran->pelatihan->kode_pelatihan)
                . '-' . strtoupper(Str::random(6));
        } while (SertifikatModel::where('kode_sertifikat', $kode)->exists());

        $tglTerbit = Carbon::now();

        // Ambil path template gambar
        $templatePath = $pendaftaran->pelatihan->template_sertifikat
            ? storage_path('app/public/' . $pendaftaran->pelatihan->template_sertifikat)
            : null;

        // Convert gambar ke base64 untuk DomPDF
        $templateBase64 = null;
        if ($templatePath && file_exists($templatePath)) {
            $templateBase64   = $pendaftaran->pelatihan->template_sertifikat
                ? $this->toBase64($pendaftaran->pelatihan->template_sertifikat)
                : null;

            $ttdBase64 = $pendaftaran->pelatihan->tanda_tangan
                ? $this->toBase64($pendaftaran->pelatihan->tanda_tangan)
                : null;
                    }

        $nomorSertifikat = $this->generateNomorSertifikat($pendaftaran->pelatihan, $tglTerbit);

        $pdf = Pdf::loadView('admin.sertifikat.template', [
            'pendaftaran'      => $pendaftaran,
            'peserta'          => $pendaftaran->peserta,
            'pelatihan'        => $pendaftaran->pelatihan,
            'instruktur'       => $pendaftaran->pelatihan->instruktur,
            'kode'             => $kode,
            'nomor_sertifikat' => $nomorSertifikat,
            'tgl_terbit'       => $tglTerbit,
            'diterbitkan_oleh' => $request->diterbitkan_oleh,
            'persen_hadir'     => $pendaftaran->kualifikasiSertifikasi->persen_hadir,
            'templateBase64'   => $templateBase64,
            'ttdBase64'        => $ttdBase64,
            'posisi'           => $this->resolvePosisi($pendaftaran->pelatihan), 
        ])
        ->setPaper('a4', 'landscape')
        ->setOption('dpi', 150)
        ->setOption('isHtml5ParserEnabled', true);

        $filePath = 'sertifikat/' . $kode . '.pdf';
        Storage::disk('public')->put($filePath, $pdf->output());

        SertifikatModel::create([
            'pendaftaran_id'   => $pendaftaran->id_pendaftaran,
            'kode_sertifikat'  => $kode,
            'nomor_sertifikat' => $nomorSertifikat,
            'tgl_terbit'       => $tglTerbit,
            'diterbitkan_oleh' => $request->diterbitkan_oleh,
            'file'             => $filePath,
        ]);

        return redirect()->route('admin.sertifikat.index')
            ->with('success', "Sertifikat {$kode} berhasil diterbitkan.");
    }

    public function uploadTandaTangan(Request $request, PelatihanModel $pelatihan)
    {
        $this->authorizeRole(['admin']);

        $request->validate([
            'tanda_tangan' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        if ($pelatihan->tanda_tangan) {
            Storage::disk('public')->delete($pelatihan->tanda_tangan);
        }

        $path = $request->file('tanda_tangan')->store('tanda-tangan-sertifikat', 'public');
        $pelatihan->update(['tanda_tangan' => $path]);

        return back()->with('success', 'Tanda tangan berhasil diupload.');
    }

    /* ═══ Default posisi (dalam %, berdasarkan layout mm lama: 297x210mm) ═══ */
    private function defaultPosisi(): array
    {
        return [
            'nama_peserta'     => ['x' => 50,    'y' => 45.24, 'align' => 'center'],
            'nama_pelatihan'   => ['x' => 50,    'y' => 57.14, 'align' => 'center'],
            'nomor_sertifikat' => ['x' => 50,   'y' => 66.67, 'align' => 'center'],
            'tgl_terbit'       => ['x' => 20.2,  'y' => 85.71, 'align' => 'left'],
            'diterbitkan_oleh' => ['x' => 79.8,  'y' => 85.71, 'align' => 'right'],
            'kode'             => ['x' => 50,    'y' => 89.52, 'align' => 'center'],
            'tanda_tangan'     => ['x' => 50,   'y' => 75.00, 'align' => 'center'],
        ];
    }

    private function resolvePosisi(PelatihanModel $pelatihan): array
    {
        $default = $this->defaultPosisi();
        $custom  = $pelatihan->posisi_sertifikat ?? [];

        $resolved = [];
        foreach ($default as $field => $pos) {
            $resolved[$field] = array_merge($pos, $custom[$field] ?? []);
        }
        return $resolved;
    }

    /* ═══ Ambil data posisi + template untuk editor ═══ */
    public function getPosisi(PelatihanModel $pelatihan)
    {
        $this->authorizeRole(['admin']);

        return response()->json([
            'template_url' => $pelatihan->template_sertifikat
                ? Storage::url($pelatihan->template_sertifikat)
                : null,
            'posisi' => $this->resolvePosisi($pelatihan),
        ]);
    }

    /* ═══ Simpan posisi dari editor ═══ */
    public function savePosisi(Request $request, PelatihanModel $pelatihan)
    {
        $this->authorizeRole(['admin']);

        $request->validate([
            'posisi'           => 'required|array',
            'posisi.*.x'       => 'required|numeric|min:0|max:100',
            'posisi.*.y'       => 'required|numeric|min:0|max:100',
            'posisi.*.align'   => 'required|in:left,center,right',
        ]);

        $pelatihan->update(['posisi_sertifikat' => $request->posisi]);

        return response()->json(['success' => true]);
    }

    /* ═══════════════════════════════════════════════
     |  ADMIN – Generate Massal
     ═══════════════════════════════════════════════ */

    public function generateMassal(Request $request)
    {
        $this->authorizeRole(['admin']);

        $request->validate([
            'diterbitkan_oleh'    => 'required|string|max:100',
            'pendaftaran_ids'     => 'required|array|min:1',
            'pendaftaran_ids.*'   => 'integer',
        ]);

        $kandidat = KualifikasiSertifikasiModel::where('memenuhi_syarat', true)
            ->whereIn('pendaftaran_id', $request->pendaftaran_ids)
            ->whereDoesntHave('pendaftaran.sertifikat')
            ->with(['pendaftaran.peserta', 'pendaftaran.pelatihan.instruktur'])
            ->get();

        $jumlah    = 0;
        $tglTerbit = Carbon::now();
        $templateCache = [];

        foreach ($kandidat as $kual) {
            $pendaftaran = $kual->pendaftaran;
            $pelatihan   = $pendaftaran->pelatihan;

            if (!array_key_exists($pelatihan->id_pelatihan, $templateCache)) {
                $templateCache[$pelatihan->id_pelatihan] = [
                    'template' => $pelatihan->template_sertifikat
                        ? $this->toBase64($pelatihan->template_sertifikat)
                        : null,
                    'ttd' => $pelatihan->tanda_tangan
                        ? $this->toBase64($pelatihan->tanda_tangan)
                        : null,
                ];
            }

            do {
                $kode = 'CERT-' . strtoupper($pelatihan->kode_pelatihan)
                    . '-' . strtoupper(Str::random(6));
            } while (SertifikatModel::where('kode_sertifikat', $kode)->exists());

            $nomorSertifikat = $this->generateNomorSertifikat($pelatihan, $tglTerbit, $jumlah);

            $pdf = Pdf::loadView('admin.sertifikat.template', [
                'pendaftaran'      => $pendaftaran,
                'peserta'          => $pendaftaran->peserta,
                'pelatihan'        => $pelatihan,
                'instruktur'       => $pelatihan->instruktur,
                'kode'             => $kode,
                'nomor_sertifikat' => $nomorSertifikat,
                'tgl_terbit'       => $tglTerbit,
                'diterbitkan_oleh' => $request->diterbitkan_oleh,
                'persen_hadir'     => $kual->persen_hadir,
                'templateBase64'   => $templateCache[$pelatihan->id_pelatihan]['template'],
                'ttdBase64'        => $templateCache[$pelatihan->id_pelatihan]['ttd'],
                'posisi'           => $this->resolvePosisi($pendaftaran->pelatihan),
            ])
            ->setPaper('a4', 'landscape')
            ->setOption('dpi', 150)
            ->setOption('isHtml5ParserEnabled', true);

            $filePath = 'sertifikat/' . $kode . '.pdf';
            Storage::disk('public')->put($filePath, $pdf->output());

            SertifikatModel::create([
                'pendaftaran_id'   => $pendaftaran->id_pendaftaran,
                'kode_sertifikat'  => $kode,
                'nomor_sertifikat' => $nomorSertifikat,
                'tgl_terbit'       => $tglTerbit,
                'diterbitkan_oleh' => $request->diterbitkan_oleh,
                'file'             => $filePath,
            ]);

            $jumlah++;
        }

        return redirect()->route('admin.sertifikat.index')
            ->with('success', "{$jumlah} sertifikat berhasil diterbitkan.");
    }

    public function previewMassal(Request $request)
    {
        $this->authorizeRole(['admin']);

        $request->validate([
            'pendaftaran_id' => 'required|integer',
        ]);

        $pendaftaran = PendaftaranModel::with(['peserta', 'pelatihan.instruktur', 'kualifikasiSertifikasi', 'sertifikat'])
            ->findOrFail($request->pendaftaran_id);

        $pelatihan = $pendaftaran->pelatihan;

        return response()->json([
            'template_url' => $pelatihan->template_sertifikat
                ? Storage::url($pelatihan->template_sertifikat)
                : null,
            'nama_peserta'  => trim(($pendaftaran->first_name ?? '') . ' ' . ($pendaftaran->last_name ?? '')),
            'pelatihan'     => $pelatihan->nama_pelatihan,
            'instruktur'    => $pelatihan->instruktur->nama ?? '-',
            'persen_hadir'  => $pendaftaran->kualifikasiSertifikasi->persen_hadir ?? null,
            'kode'          => $pendaftaran->sertifikat->kode_sertifikat ?? 'akan dibuat saat diterbitkan',
            'download_url'  => $pendaftaran->sertifikat
                ? Storage::url($pendaftaran->sertifikat->file)
                : null,
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  ADMIN – Hapus Sertifikat
     ═══════════════════════════════════════════════ */

    public function destroy(SertifikatModel $sertifikat)
    {
        $this->authorizeRole(['admin']);

        if (Storage::disk('public')->exists($sertifikat->file)) {
            Storage::disk('public')->delete($sertifikat->file);
        }

        $sertifikat->delete();

        return redirect()->route('admin.sertifikat.index')
            ->with('success', 'Sertifikat berhasil dihapus.');
    }

    /* ═══════════════════════════════════════════════
     |  INSTRUKTUR – Riwayat Sertifikat
     ═══════════════════════════════════════════════ */

    public function riwayatInstruktur(Request $request)
    {
        $this->authorizeRole(['instruktur']);

        $instruktur = Auth::user();

        $sertifikat = SertifikatModel::with(['pendaftaran.peserta', 'pendaftaran.pelatihan'])
            ->whereHas('pendaftaran.pelatihan', fn($q) =>
                $q->where('instruktur_id', $instruktur->id_user)
            )
            ->latest('tgl_terbit')
            ->paginate(15)
            ->withQueryString();

        return view('instruktur.sertifikat.index', compact('sertifikat'));
    }

    /* ═══════════════════════════════════════════════
     |  PESERTA – Sertifikat Saya + Download
     ═══════════════════════════════════════════════ */

    public function sertifSaya()
    {
        $this->authorizeRole(['peserta']);

        $peserta = Auth::user();

        $sertifikat = SertifikatModel::with(['pendaftaran.pelatihan'])
            ->whereHas('pendaftaran', fn($q) =>
                $q->where('peserta_id', $peserta->id_user)
            )
            ->latest('tgl_terbit')
            ->get();

        return view('peserta.sertifikat.index', compact('sertifikat'));
    }

    /**
     * [PESERTA] Download PDF sertifikat milik sendiri.
     */
    public function download(SertifikatModel $sertifikat)
    {
        $this->authorizeRole(['peserta']);

        $sertifikat->load('pendaftaran');

        if ($sertifikat->pendaftaran->peserta_id !== Auth::user()->id_user) {
            abort(403, 'Sertifikat ini bukan milik Anda.');
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        if (!$disk->exists($sertifikat->file)) {
            abort(404, 'File sertifikat tidak ditemukan. Hubungi admin.');
        }

        return $disk->download(
            $sertifikat->file,
            $sertifikat->kode_sertifikat . '.pdf'
        );
    }

    /* ─── Helper ─── */

    private const KODE_INSTITUSI = 'EXP';

    private function toRoman(int $month): string
    {
        return ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][$month - 1];
    }

    private function generateNomorSertifikat(PelatihanModel $pelatihan, Carbon $tglTerbit, int $offset = 0): string
    {
        // Hitung sertifikat yang sudah ada untuk pelatihan ini di bulan & tahun yang sama
        $existing = SertifikatModel::whereHas('pendaftaran', fn($q) =>
            $q->where('pelatihan_id', $pelatihan->id_pelatihan)
        )
        ->whereYear('tgl_terbit', $tglTerbit->year)
        ->whereMonth('tgl_terbit', $tglTerbit->month)
        ->count();

        $nomor     = str_pad($existing + 1 + $offset, 3, '0', STR_PAD_LEFT);
        $kodePel   = strtoupper(explode('-', $pelatihan->kode_pelatihan)[0]); // STR-001 → STR
        $roman     = $this->toRoman($tglTerbit->month);

        return "{$nomor}/" . self::KODE_INSTITUSI . "/{$kodePel}/{$roman}/{$tglTerbit->year}";
    }
    private function toBase64(string $storagePath): ?string
    {
        $fullPath = storage_path('app/public/' . $storagePath);
        if (!file_exists($fullPath)) return null;

        $ext  = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mime = $ext === 'png' ? 'image/png' : 'image/jpeg';
        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($fullPath));
    }

    private function authorizeRole(array $roles): void
    {
        if (!in_array(Auth::user()->role, $roles)) {
            abort(403, 'Akses ditolak.');
        }
    }
}
