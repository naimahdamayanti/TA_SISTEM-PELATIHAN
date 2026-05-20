<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranModel;
use App\Models\PelatihanModel;
use App\Models\UserModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PendaftaranController extends Controller
{
    /* ═══════════════════════════════════════════════
     |  ADMIN – Kelola Pendaftaran
     ═══════════════════════════════════════════════ */

    public function index(Request $request)
    {
        $this->authorizeRole(['admin']);

        $query = PendaftaranModel::with(['peserta', 'pelatihan'])->latest('tgl_daftar');

        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('pelatihan_id')) $query->where('pelatihan_id', $request->pelatihan_id);
        if ($request->filled('search')) {
            $query->where(fn($q) =>
                $q->where('first_name', 'like', '%'.$request->search.'%')
                  ->orWhere('last_name',  'like', '%'.$request->search.'%')
                  ->orWhere('email',      'like', '%'.$request->search.'%')
            );
        }

        $pendaftaran = $query->paginate(15)->withQueryString();
        $pelatihan   = PelatihanModel::orderBy('nama_pelatihan')->get();
        $stats = [
            'menunggu' => PendaftaranModel::where('status', 'menunggu')->count(),
            'diterima' => PendaftaranModel::where('status', 'diterima')->count(),
            'ditolak'  => PendaftaranModel::where('status', 'ditolak')->count(),
        ];

        return view('admin.pendaftaran.index', compact('pendaftaran', 'pelatihan', 'stats'));
    }

    public function show(PendaftaranModel $pendaftaran)
    {
        $this->authorizeRole(['admin']);
        $pendaftaran->load(['peserta', 'pelatihan.instruktur']);
        return view('admin.pendaftaran.show', compact('pendaftaran'));
    }

    public function terima(PendaftaranModel $pendaftaran)
    {
        $this->authorizeRole(['admin']);
        if ($pendaftaran->status !== 'menunggu')
            return back()->with('error', 'Hanya pendaftaran berstatus "menunggu" yang dapat diproses.');

        $diterima = PendaftaranModel::where('pelatihan_id', $pendaftaran->pelatihan_id)
            ->where('status', 'diterima')->count();

        if ($diterima >= $pendaftaran->pelatihan->kuota) {
            $pendaftaran->pelatihan->update(['status' => 'penuh']);
            return back()->with('error', 'Kuota pelatihan sudah penuh.');
        }

        $pendaftaran->update(['status' => 'diterima']);

        if (is_null($pendaftaran->peserta_id)) {
            $user = UserModel::where('email', $pendaftaran->email)->first();
            if ($user) $pendaftaran->update(['peserta_id' => $user->id_user]);
        }

        $diterimaBaru = PendaftaranModel::where('pelatihan_id', $pendaftaran->pelatihan_id)
            ->where('status', 'diterima')->count();
        if ($diterimaBaru >= $pendaftaran->pelatihan->kuota)
            $pendaftaran->pelatihan->update(['status' => 'penuh']);

        return back()->with('success', 'Pendaftaran berhasil diterima.');
    }

    public function tolak(Request $request, PendaftaranModel $pendaftaran)
    {
        $this->authorizeRole(['admin']);
        if ($pendaftaran->status !== 'menunggu')
            return back()->with('error', 'Hanya pendaftaran berstatus "menunggu" yang dapat ditolak.');
        $pendaftaran->update(['status' => 'ditolak']);
        return back()->with('success', 'Pendaftaran telah ditolak.');
    }

    public function destroy(PendaftaranModel $pendaftaran)
    {
        $this->authorizeRole(['admin']);
        $pendaftaran->delete();
        return redirect()->route('admin.pendaftaran.index')
            ->with('success', 'Data pendaftaran berhasil dihapus.');
    }

    /* ═══════════════════════════════════════════════
     |  PESERTA – Form & Riwayat Pendaftaran
     ═══════════════════════════════════════════════ */

    public function formPendaftaran(PelatihanModel $pelatihan)
    {
        $this->authorizeRole(['peserta']);

        if ($pelatihan->status !== 'tersedia')
            return redirect()->route('peserta.katalog')
                ->with('error', 'Pelatihan ini tidak lagi menerima pendaftaran.');

        $peserta     = Auth::user();
        $sudahDaftar = PendaftaranModel::where('peserta_id', $peserta->id_user)
            ->where('pelatihan_id', $pelatihan->id_pelatihan)->exists();

        if ($sudahDaftar)
            return redirect()->route('peserta.riwayat')
                ->with('info', 'Anda sudah pernah mendaftar ke pelatihan ini.');

        $pelatihan->load('instruktur');
        return view('peserta.pendaftaran.form', compact('pelatihan', 'peserta'));
    }

    public function kirimPendaftaran(Request $request, PelatihanModel $pelatihan)
    {
        $this->authorizeRole(['peserta']);

        if ($pelatihan->status !== 'tersedia')
            return back()->with('error', 'Pelatihan sudah tidak tersedia.');

        $peserta     = Auth::user();
        $sudahDaftar = PendaftaranModel::where('peserta_id', $peserta->id_user)
            ->where('pelatihan_id', $pelatihan->id_pelatihan)->exists();

        if ($sudahDaftar)
            return redirect()->route('peserta.riwayat')
                ->with('info', 'Anda sudah terdaftar di pelatihan ini.');

        $validated = $request->validate([
            'first_name'     => 'required|string|max:50',
            'last_name'      => 'nullable|string|max:50',
            'email'          => 'required|email|max:100',
            'no_hp'          => 'nullable|string|max:20',
            'perusahaan'     => 'nullable|string|max:100',
            'alamat'         => 'nullable|string',
            'pekerjaan'      => 'nullable|string|max:100',
            'tlp_perusahaan' => 'nullable|string|max:20',
            'pesan'          => 'nullable|string',
        ]);

        $validated['peserta_id']   = $peserta->id_user;
        $validated['pelatihan_id'] = $pelatihan->id_pelatihan;
        $validated['status']       = 'menunggu';
        $validated['tgl_daftar']   = Carbon::now();

        PendaftaranModel::create($validated);

        return redirect()->route('peserta.riwayat')
            ->with('success', 'Pendaftaran berhasil dikirim. Menunggu konfirmasi admin.');
    }

    public function riwayat()
    {
        $this->authorizeRole(['peserta']);

        $pendaftaran = PendaftaranModel::with(['pelatihan.instruktur', 'sertifikat'])
            ->where('peserta_id', Auth::user()->id_user)
            ->latest('tgl_daftar')
            ->paginate(10)
            ->withQueryString();

        return view('peserta.riwayat.index', compact('pendaftaran'));
    }

    /**
     * [PESERTA] Ambil data satu pendaftaran sebagai JSON untuk ditampilkan di modal.
     * Dipanggil via AJAX saat peserta klik tombol "Unduh PDF" di tabel riwayat.
     */
    public function detailPendaftaran(PendaftaranModel $pendaftaran)
    {
        $this->authorizeRole(['peserta']);

        // Pastikan pendaftaran milik peserta yang login
        if ($pendaftaran->peserta_id !== Auth::user()->id_user) {
            abort(403, 'Akses ditolak.');
        }

        $pendaftaran->load(['pelatihan.instruktur', 'sertifikat']);

        return response()->json([
            'id'             => $pendaftaran->id_pendaftaran ?? $pendaftaran->id,
            'id_label'       => 'PD-' . str_pad($pendaftaran->id_pendaftaran ?? $pendaftaran->id, 3, '0', STR_PAD_LEFT),
            'first_name'     => $pendaftaran->first_name,
            'last_name'      => $pendaftaran->last_name,
            'nama_lengkap'   => trim($pendaftaran->first_name . ' ' . $pendaftaran->last_name),
            'email'          => $pendaftaran->email,
            'no_hp'          => $pendaftaran->no_hp ?? '-',
            'alamat'         => $pendaftaran->alamat ?? '-',
            'pekerjaan'      => $pendaftaran->pekerjaan ?? '-',
            'perusahaan'     => $pendaftaran->perusahaan ?? '-',
            'tlp_perusahaan' => $pendaftaran->tlp_perusahaan ?? '-',
            'pesan'          => $pendaftaran->pesan ?? '-',
            'status'         => $pendaftaran->status,
            'tgl_daftar'     => $pendaftaran->tgl_daftar
                                    ? Carbon::parse($pendaftaran->tgl_daftar)->translatedFormat('j M Y')
                                    : '-',
            'pelatihan' => [
                'nama'       => $pendaftaran->pelatihan?->nama_pelatihan ?? '-',
                'kode'       => $pendaftaran->pelatihan?->kode_pelatihan ?? '-',
                'instruktur' => $pendaftaran->pelatihan?->instruktur?->nama_lengkap
                                ?? $pendaftaran->pelatihan?->instruktur?->nama
                                ?? '-',
                'periode'    => ($pendaftaran->pelatihan?->tgl_mulai && $pendaftaran->pelatihan?->tgl_selesai)
                                ? Carbon::parse($pendaftaran->pelatihan->tgl_mulai)->translatedFormat('j M Y')
                                  . ' – '
                                  . Carbon::parse($pendaftaran->pelatihan->tgl_selesai)->translatedFormat('j M Y')
                                : '-',
            ],
            'sertifikat' => $pendaftaran->sertifikat
                            ? $pendaftaran->sertifikat->kode_sertifikat
                            : null,
            'url_pdf' => route('peserta.pendaftaran.exportPDF', $pendaftaran->id_pendaftaran ?? $pendaftaran->id),
        ]);
    }

    /**
     * [PESERTA] Generate & download PDF formulir satu pendaftaran.
     * Dipanggil saat peserta klik "Unduh sebagai PDF" di dalam modal.
     */
    public function exportPDF(PendaftaranModel $pendaftaran)
    {
        $this->authorizeRole(['peserta']);

        if ($pendaftaran->peserta_id !== Auth::user()->id_user) {
            abort(403, 'Akses ditolak.');
        }

        $pendaftaran->load(['pelatihan.instruktur', 'sertifikat']);

        $pdf = Pdf::loadView('peserta.pendaftaran.export-pdf', [
            'pendaftaran' => $pendaftaran,
            'tgl_cetak'   => Carbon::now(),
        ])
        ->setPaper('a4', 'portrait')
        ->setOption('dpi', 150)
        ->setOption('isHtml5ParserEnabled', true);

        $namaFile = 'formulir-pendaftaran-'
                  . ($pendaftaran->id_pendaftaran ?? $pendaftaran->id)
                  . '.pdf';

        return $pdf->download($namaFile);
    }

    /* ─── Helper ─── */
    private function authorizeRole(array $roles): void
    {
        if (!in_array(Auth::user()->role, $roles)) {
            abort(403, 'Akses ditolak.');
        }
    }
}