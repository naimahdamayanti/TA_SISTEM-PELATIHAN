<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\KualifikasiSertifikasiModel;
use App\Models\PendaftaranModel;
use App\Models\PelatihanModel;
use App\Models\LogbookModel;
use App\Models\SesiPelatihanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KualifikasiSertifikasiController extends Controller
{
    /* ═══════════════════════════════════════════════
     |  INSTRUKTUR – Nilai Kelayakan Peserta
     ═══════════════════════════════════════════════ */

    /**
     * [INSTRUKTUR] Daftar peserta yang siap dinilai kelayakannya (pelatihan selesai).
     */
    public function index(Request $request)
    {
        $this->authorizeRole(['instruktur']);

        $instruktur = Auth::user();

        // Pelatihan berstatus "selesai" yang diampu instruktur ini
        $pelatihan = PelatihanModel::where('instruktur_id', $instruktur->id_user)
            ->where('status', 'selesai')
            ->withCount(['pendaftaran' => fn($q) => $q->where('status', 'diterima')])
            ->get();

        if ($request->filled('pelatihan_id')) {
            $pelatihanDipilih = PelatihanModel::findOrFail($request->pelatihan_id);

            if ($pelatihanDipilih->instruktur_id !== $instruktur->id_user) {
                abort(403);
            }

            $pesertaList = $this->getPesertaDenganKelayakan($pelatihanDipilih, $instruktur->id_user);
        } else {
            $pesertaList      = collect();
            $pelatihanDipilih = null;
        }

        return view('instruktur.kelayakan.index', compact('pelatihan', 'pesertaList', 'pelatihanDipilih'));
    }

    /**
     * [INSTRUKTUR] Simpan atau perbarui penilaian kelayakan satu peserta.
     */
    public function simpan(Request $request, PendaftaranModel $pendaftaran)
    {
        $this->authorizeRole(['instruktur']);

        // Pastikan pelatihan ini diampu instruktur login
        if ($pendaftaran->pelatihan->instruktur_id !== Auth::user()->id_user) {
            abort(403, 'Pendaftaran ini bukan pada pelatihan Anda.');
        }

        $validated = $request->validate([
            'persen_hadir'   => 'required|numeric|min:0|max:100',
            'memenuhi_syarat'=> 'required|boolean',
            'catatan'        => 'nullable|string',
        ]);

        $validated['instruktur_id']  = Auth::user()->id_user;
        $validated['pendaftaran_id'] = $pendaftaran->id_pendaftaran;

        KualifikasiSertifikasiModel::updateOrCreate(
            ['pendaftaran_id' => $pendaftaran->id_pendaftaran],
            $validated
        );

        return back()->with('success', 'Penilaian kelayakan berhasil disimpan.');
    }

    /**
     * [INSTRUKTUR] Simpan penilaian kelayakan massal (seluruh peserta satu pelatihan sekaligus).
     * Persentase dihitung otomatis dari logbook.
     */
    public function simpanMassal(Request $request, PelatihanModel $pelatihan)
    {
        $this->authorizeRole(['instruktur']);

        if ($pelatihan->instruktur_id !== Auth::user()->id_user) {
            abort(403);
        }

        $request->validate([
            'kelayakan'                  => 'required|array',
            'kelayakan.*.pendaftaran_id' => 'required|exists:pendaftaran,id_pendaftaran',
            'kelayakan.*.memenuhi_syarat'=> 'required|boolean',
            'kelayakan.*.catatan'        => 'nullable|string',
        ]);

        $instrukturId = Auth::user()->id_user;

        DB::transaction(function () use ($request, $pelatihan, $instrukturId) {
            $totalSesi = SesiPelatihanModel::where('pelatihan_id', $pelatihan->id_pelatihan)->count();

            foreach ($request->input('kelayakan') as $row) {
                $pendaftaran = PendaftaranModel::findOrFail($row['pendaftaran_id']);

                // Hitung persen hadir dari logbook
                $hadirCount  = LogbookModel::where('peserta_id', $pendaftaran->peserta_id)
                    ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $pelatihan->id_pelatihan))
                    ->where('status', 'hadir')
                    ->count();

                $persen = $totalSesi > 0 ? round(($hadirCount / $totalSesi) * 100, 2) : 0;

                KualifikasiSertifikasiModel::updateOrCreate(
                    ['pendaftaran_id' => $row['pendaftaran_id']],
                    [
                        'instruktur_id'  => $instrukturId,
                        'persen_hadir'   => $persen,
                        'memenuhi_syarat'=> $row['memenuhi_syarat'],
                        'catatan'        => $row['catatan'] ?? null,
                    ]
                );
            }
        });

        return redirect()->route('instruktur.kelayakan.index', ['pelatihan_id' => $pelatihan->id_pelatihan])
            ->with('success', 'Penilaian kelayakan semua peserta berhasil disimpan.');
    }

    /**
     * [INSTRUKTUR] Riwayat sertifikat – daftar peserta yang memenuhi syarat.
     */
    public function riwayatSertifInstruktur(Request $request)
    {
        $this->authorizeRole(['instruktur']);

        $instruktur = Auth::user();

        $query = KualifikasiSertifikasiModel::with(['pendaftaran.peserta', 'pendaftaran.pelatihan'])
            ->where('instruktur_id', $instruktur->id_user);

        if ($request->filled('memenuhi_syarat')) {
            $query->where('memenuhi_syarat', $request->boolean('memenuhi_syarat'));
        }
        if ($request->filled('pelatihan_id')) {
            $query->whereHas('pendaftaran', fn($q) => $q->where('pelatihan_id', $request->pelatihan_id));
        }

        $kualifikasi = $query->latest('tgl_penilaian')->paginate(15)->withQueryString();
        $pelatihan   = PelatihanModel::where('instruktur_id', $instruktur->id_user)->get();

        return view('instruktur.kelayakan.riwayat', compact('kualifikasi', 'pelatihan'));
    }

    /* ═══════════════════════════════════════════════
     |  ADMIN – Lihat Semua Kualifikasi
     ═══════════════════════════════════════════════ */

    /**
     * [ADMIN] Semua data kualifikasi sertifikasi (read-only).
     */
    public function adminIndex(Request $request)
    {
        $this->authorizeRole(['admin']);

        $query = KualifikasiSertifikasiModel::with(['pendaftaran.peserta', 'pendaftaran.pelatihan', 'instruktur']);

        if ($request->filled('memenuhi_syarat')) {
            $query->where('memenuhi_syarat', $request->boolean('memenuhi_syarat'));
        }

        $kualifikasi = $query->latest('tgl_penilaian')->paginate(20)->withQueryString();

        return view('admin.kualifikasi.index', compact('kualifikasi'));
    }

    /* ═══════════════════════════════════════════════
     |  PESERTA – Lihat Status Kelayakan Sendiri
     ═══════════════════════════════════════════════ */

    /**
     * [PESERTA] Status kelayakan sertifikasi peserta yang sedang login.
     */
    public function statusKelayakanPeserta()
    {
        $this->authorizeRole(['peserta']);

        $peserta = Auth::user();

        $kualifikasi = KualifikasiSertifikasiModel::with(['pendaftaran.pelatihan'])
            ->whereHas('pendaftaran', fn($q) => $q->where('peserta_id', $peserta->id_user))
            ->get();

        return view('peserta.kelayakan.index', compact('kualifikasi'));
    }

    /* ─── Private Helper ─── */

    /**
     * Ambil daftar peserta beserta data kehadiran & kelayakan untuk satu pelatihan.
     */
    private function getPesertaDenganKelayakan(PelatihanModel $pelatihan, int $instrukturId): \Illuminate\Support\Collection
    {
        $pesertaList = PendaftaranModel::with(['peserta', 'kualifikasiSertifikasi'])
            ->where('pelatihan_id', $pelatihan->id_pelatihan)
            ->where('status', 'diterima')
            ->get();

        $totalSesi = SesiPelatihanModel::where('pelatihan_id', $pelatihan->id_pelatihan)->count();

        return $pesertaList->map(function ($daftar) use ($totalSesi, $pelatihan) {
            $hadir = LogbookModel::where('peserta_id', $daftar->peserta_id)
                ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $pelatihan->id_pelatihan))
                ->where('status', 'hadir')
                ->count();

            $persen = $totalSesi > 0 ? round(($hadir / $totalSesi) * 100, 2) : 0;

            return [
                'pendaftaran'  => $daftar,
                'peserta'      => $daftar->peserta,
                'persen_hadir' => $persen,
                'hadir'        => $hadir,
                'total_sesi'   => $totalSesi,
                'kualifikasi'  => $daftar->kualifikasiSertifikasi,
            ];
        });
    }

    private function authorizeRole(array $roles): void
    {
        if (!in_array(Auth::user()->role, $roles)) {
            abort(403, 'Akses ditolak.');
        }
    }
}
