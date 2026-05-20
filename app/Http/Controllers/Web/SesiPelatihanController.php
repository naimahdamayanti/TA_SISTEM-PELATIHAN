<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PelatihanModel;
use App\Models\SesiPelatihanModel;
use App\Models\LogbookModel;
use App\Models\PendaftaranModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SesiPelatihanController extends Controller
{
    /* ═══════════════════════════════════════════════
     |  ADMIN – Kelola Sesi Pelatihan
     ═══════════════════════════════════════════════ */

    /**
     * [ADMIN] Daftar sesi dari satu pelatihan.
     */
    public function index(PelatihanModel $pelatihan)
    {
        $this->authorizeRole(['admin']);

        $sesi = SesiPelatihanModel::where('pelatihan_id', $pelatihan->id_pelatihan)
            ->withCount('logbook')  // agar $s->logbook_count tersedia di view
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->get();

        $pesertaDiterima = PendaftaranModel::where('pelatihan_id', $pelatihan->id_pelatihan)
            ->where('status', 'diterima')
            ->count();

        return view('admin.sesi.index', compact('pelatihan', 'sesi', 'pesertaDiterima'));
    }

    /**
     * [ADMIN] Form tambah sesi baru.
     */
    public function create(PelatihanModel $pelatihan)
    {
        $this->authorizeRole(['admin']);

        return view('admin.sesi.create', compact('pelatihan'));
    }

    /**
     * [ADMIN] Simpan sesi baru.
     */
    public function store(Request $request, PelatihanModel $pelatihan)
    {
        $this->authorizeRole(['admin']);

        $validated = $request->validate([
            'judul_sesi'    => 'nullable|string|max:100',
            'tanggal'       => 'required|date',
            'waktu_mulai'   => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'lokasi'        => 'required|string|max:255',
        ]);

        $validated['pelatihan_id'] = $pelatihan->id_pelatihan;

        SesiPelatihanModel::create($validated);

        return redirect()->route('admin.sesi.index', $pelatihan->id_pelatihan)
            ->with('success', 'Sesi pelatihan berhasil ditambahkan.');
    }

    /**
     * [ADMIN] Form edit sesi.
     */
    public function edit(PelatihanModel $pelatihan, SesiPelatihanModel $sesi)
    {
        $this->authorizeRole(['admin']);
        $this->pastikanMilikPelatihan($sesi, $pelatihan);
        return view('admin.sesi.edit', compact('pelatihan', 'sesi'));
    }

    /**
     * [ADMIN] Perbarui data sesi.
     */
    public function update(Request $request, PelatihanModel $pelatihan, SesiPelatihanModel $sesi)
    {
        $this->authorizeRole(['admin']);
        $this->pastikanMilikPelatihan($sesi, $pelatihan);

        $validated = $request->validate([
            'judul_sesi'    => 'nullable|string|max:100',
            'tanggal'       => 'required|date',
            'waktu_mulai'   => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'lokasi'        => 'required|string|max:255',
        ]);

        $sesi->update($validated);

        return redirect()->route('admin.sesi.index', $pelatihan->id_pelatihan)
            ->with('success', 'Data sesi berhasil diperbarui.');
    }

    /**
     * [ADMIN] Hapus sesi.
     */
    public function destroy(PelatihanModel $pelatihan, SesiPelatihanModel $sesi)
    {
        $this->authorizeRole(['admin']);
        $this->pastikanMilikPelatihan($sesi, $pelatihan);

        $sesi->delete();

        return redirect()->route('admin.sesi.index', $pelatihan->id_pelatihan)
            ->with('success', 'Sesi berhasil dihapus.');
    }

    /* ═══════════════════════════════════════════════
     |  INSTRUKTUR – Jadwal Sesi
     ═══════════════════════════════════════════════ */

    /**
     * [INSTRUKTUR] Jadwal sesi pelatihan yang diampu.
     */
    public function jadwalSesi(Request $request)
    {
        $this->authorizeRole(['instruktur']);

        $instruktur = Auth::user();

        $query = SesiPelatihanModel::whereHas('pelatihan', fn($q) =>
            $q->where('instruktur_id', $instruktur->id_user)
        )->with('pelatihan')->orderBy('tanggal')->orderBy('waktu_mulai');

        // Filter berdasarkan pelatihan tertentu
        if ($request->filled('pelatihan_id')) {
            $query->where('pelatihan_id', $request->pelatihan_id);
        }

        $sesi = $query->paginate(15)->withQueryString();

        // Daftar pelatihan instruktur ini untuk filter dropdown
        $pelatihan = PelatihanModel::where('instruktur_id', $instruktur->id_user)->get();

        return view('instruktur.sesi.jadwal', compact('sesi', 'pelatihan'));
    }

    /**
     * [INSTRUKTUR] Detail satu sesi: daftar peserta + status logbook.
     */
    public function detailSesi(SesiPelatihanModel $sesi)
    {
        $this->authorizeRole(['instruktur']);

        // Pastikan sesi ini milik instruktur login
        if ($sesi->pelatihan->instruktur_id !== Auth::user()->id_user) {
            abort(403, 'Sesi ini bukan bagian dari pelatihan Anda.');
        }

        $sesi->load('pelatihan');

        // Peserta yang terdaftar (diterima) di pelatihan ini
        $pesertaTerdaftar = PendaftaranModel::with('peserta')
            ->where('pelatihan_id', $sesi->pelatihan_id)
            ->where('status', 'diterima')
            ->get();

        // Logbook yang sudah diisi untuk sesi ini
        $logbookTerisi = LogbookModel::where('sesi_id', $sesi->id_sesi)
            ->pluck('status', 'peserta_id')
            ->toArray();

        return view('instruktur.sesi.detail', compact('sesi', 'pesertaTerdaftar', 'logbookTerisi'));
    }

    /* ═══════════════════════════════════════════════
     |  PESERTA – Status Kehadiran
     ═══════════════════════════════════════════════ */

    /**
     * [PESERTA] Status kehadiran peserta di seluruh sesi pelatihan yang diikuti.
     */
    public function statusKehadiran(Request $request)
    {
        $this->authorizeRole(['peserta']);

        $peserta = Auth::user();

        // Ambil pelatihan yang diikuti peserta (pendaftaran diterima)
        $pendaftaranDiterima = PendaftaranModel::where('peserta_id', $peserta->id_user)
            ->where('status', 'diterima')
            ->with(['pelatihan.sesiPelatihan'])
            ->get();

        // Logbook peserta ini
        $logbook = LogbookModel::where('peserta_id', $peserta->id_user)
            ->get()
            ->keyBy('sesi_id');

        // Susun data per pelatihan
        $dataKehadiran = $pendaftaranDiterima->map(function ($daftar) use ($logbook) {
            $sesiList = $daftar->pelatihan->sesiPelatihan->map(function ($sesi) use ($logbook) {
                return [
                    'sesi'   => $sesi,
                    'status' => $logbook->get($sesi->id_sesi)?->status ?? 'belum dicatat',
                ];
            });

            $hadir   = $sesiList->where('status', 'hadir')->count();
            $total   = $sesiList->count();
            $persen  = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;

            return [
                'pelatihan' => $daftar->pelatihan,
                'sesi'      => $sesiList,
                'hadir'     => $hadir,
                'total'     => $total,
                'persen'    => $persen,
            ];
        });

        return view('peserta.kehadiran.index', compact('dataKehadiran'));
    }

    /* ─── Helper ─── */

    private function authorizeRole(array $roles): void
    {
        if (!in_array(Auth::user()->role, $roles)) {
            abort(403, 'Akses ditolak.');
        }
    }

    private function pastikanMilikPelatihan(SesiPelatihanModel $sesi, PelatihanModel $pelatihan): void
    {
        if ($sesi->pelatihan_id !== $pelatihan->id_pelatihan) {
            abort(404, 'Sesi tidak ditemukan pada pelatihan ini.');
        }
    }
}
