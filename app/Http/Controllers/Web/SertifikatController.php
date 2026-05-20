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

        // ── Guard: peserta wajib memenuhi syarat ──
        if (!$pendaftaran->kualifikasiSertifikasi?->memenuhi_syarat) {
            return back()->with('error', 'Peserta belum memenuhi syarat sertifikasi.');
        }

        // ── Guard: cegah penerbitan ganda ──
        if (SertifikatModel::where('pendaftaran_id', $pendaftaran->id_pendaftaran)->exists()) {
            return back()->with('info', 'Sertifikat untuk peserta ini sudah pernah diterbitkan.');
        }

        $request->validate([
            'diterbitkan_oleh' => 'required|string|max:100',
        ]);

        // ── Buat kode unik ──
        do {
            $kode = 'CERT-' . strtoupper($pendaftaran->pelatihan->kode_pelatihan)
                  . '-' . strtoupper(Str::random(6));
        } while (SertifikatModel::where('kode_sertifikat', $kode)->exists());

        $tglTerbit = Carbon::now();

        // ── Render PDF dari view Blade ──
        // Buat file: resources/views/sertifikat/template.blade.php
        // (contoh template ada di bawah file ini)
        $pdf = Pdf::loadView('sertifikat.template', [
            'pendaftaran'     => $pendaftaran,
            'peserta'         => $pendaftaran->peserta,
            'pelatihan'       => $pendaftaran->pelatihan,
            'instruktur'      => $pendaftaran->pelatihan->instruktur,
            'kode'            => $kode,
            'tgl_terbit'      => $tglTerbit,
            'diterbitkan_oleh'=> $request->diterbitkan_oleh,
            'persen_hadir'    => $pendaftaran->kualifikasiSertifikasi->persen_hadir,
        ])
        ->setPaper('a4', 'landscape')   // ukuran & orientasi kertas
        ->setOption('dpi', 150)
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true);   // izinkan gambar dari URL/base64

        // ── Simpan PDF ke disk 'public' ──
        // Path fisik: storage/app/public/sertifikat/{kode}.pdf
        // URL akses : /storage/sertifikat/{kode}.pdf  (setelah php artisan storage:link)
        $filePath = 'sertifikat/' . $kode . '.pdf';
        Storage::disk('public')->put($filePath, $pdf->output());

        // ── Simpan record ke database ──
        SertifikatModel::create([
            'pendaftaran_id'  => $pendaftaran->id_pendaftaran,
            'kode_sertifikat' => $kode,
            'tgl_terbit'      => $tglTerbit,
            'diterbitkan_oleh'=> $request->diterbitkan_oleh,
            'file'            => $filePath,
        ]);

        return redirect()->route('admin.sertifikat.index')
            ->with('success', "Sertifikat {$kode} berhasil diterbitkan.");
    }

    /* ═══════════════════════════════════════════════
     |  ADMIN – Generate Massal
     ═══════════════════════════════════════════════ */

    public function generateMassal(Request $request, PelatihanModel $pelatihan)
    {
        $this->authorizeRole(['admin']);

        $request->validate([
            'diterbitkan_oleh' => 'required|string|max:100',
        ]);

        $kandidat = KualifikasiSertifikasiModel::where('memenuhi_syarat', true)
            ->whereHas('pendaftaran', fn($q) => $q->where('pelatihan_id', $pelatihan->id_pelatihan))
            ->whereDoesntHave('pendaftaran.sertifikat')
            ->with(['pendaftaran.peserta', 'pendaftaran.pelatihan.instruktur'])
            ->get();

        $jumlah    = 0;
        $tglTerbit = Carbon::now();

        foreach ($kandidat as $kual) {
            $pendaftaran = $kual->pendaftaran;

            do {
                $kode = 'CERT-' . strtoupper($pelatihan->kode_pelatihan)
                      . '-' . strtoupper(Str::random(6));
            } while (SertifikatModel::where('kode_sertifikat', $kode)->exists());

            // Render & simpan PDF
            $pdf = Pdf::loadView('sertifikat.template', [
                'pendaftaran'     => $pendaftaran,
                'peserta'         => $pendaftaran->peserta,
                'pelatihan'       => $pelatihan,
                'instruktur'      => $pelatihan->instruktur,
                'kode'            => $kode,
                'tgl_terbit'      => $tglTerbit,
                'diterbitkan_oleh'=> $request->diterbitkan_oleh,
                'persen_hadir'    => $kual->persen_hadir,
            ])
            ->setPaper('a4', 'landscape')
            ->setOption('dpi', 150);

            $filePath = 'sertifikat/' . $kode . '.pdf';
            Storage::disk('public')->put($filePath, $pdf->output());

            SertifikatModel::create([
                'pendaftaran_id'  => $pendaftaran->id_pendaftaran,
                'kode_sertifikat' => $kode,
                'tgl_terbit'      => $tglTerbit,
                'diterbitkan_oleh'=> $request->diterbitkan_oleh,
                'file'            => $filePath,
            ]);

            $jumlah++;
        }

        return redirect()->route('admin.sertifikat.index')
            ->with('success', "{$jumlah} sertifikat berhasil diterbitkan.");
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

    private function authorizeRole(array $roles): void
    {
        if (!in_array(Auth::user()->role, $roles)) {
            abort(403, 'Akses ditolak.');
        }
    }
}
