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
    public function index(Request $request)
    {
        $this->authorizeRole(['instruktur']);

        $instruktur = Auth::user();

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

    public function simpan(Request $request, PendaftaranModel $pendaftaran)
    {
        $this->authorizeRole(['instruktur']);

        if ($pendaftaran->pelatihan->instruktur_id !== Auth::user()->id_user) {
            abort(403, 'Pendaftaran ini bukan pada pelatihan Anda.');
        }

        $validated = $request->validate([
            'persen_hadir' => 'required|numeric|min:0|max:100',
            'catatan'      => 'nullable|string',
        ]);

        KualifikasiSertifikasiModel::updateOrCreate(
            ['pendaftaran_id' => $pendaftaran->id_pendaftaran],
            [
                'instruktur_id'   => Auth::user()->id_user,
                'persen_hadir'    => $validated['persen_hadir'],
                'memenuhi_syarat' => $validated['persen_hadir'] >= KualifikasiSertifikasiModel::BATAS_KEHADIRAN ? 'lulus' : 'tidak lulus',
                'catatan'         => $validated['catatan'] ?? null,
            ]
        );

        return back()->with('success', 'Penilaian kelayakan berhasil disimpan.');
    }

    public function simpanMassal(Request $request, PelatihanModel $pelatihan)
    {
        $this->authorizeRole(['instruktur']);

        if ($pelatihan->instruktur_id !== Auth::user()->id_user) {
            abort(403);
        }

        $request->validate([
            'kelayakan'                  => 'required|array',
            'kelayakan.*.pendaftaran_id' => 'required|exists:pendaftaran,id_pendaftaran',
            'kelayakan.*.catatan'        => 'nullable|string',
        ]);

        $instrukturId = Auth::user()->id_user;

        DB::transaction(function () use ($request, $pelatihan, $instrukturId) {
            $totalSesi = SesiPelatihanModel::where('pelatihan_id', $pelatihan->id_pelatihan)->count();

            foreach ($request->input('kelayakan') as $row) {
                $pendaftaran = PendaftaranModel::findOrFail($row['pendaftaran_id']);

                $hadirCount = LogbookModel::where('peserta_id', $pendaftaran->peserta_id)
                    ->whereHas('sesiPelatihan', fn($q) => $q->where('pelatihan_id', $pelatihan->id_pelatihan))
                    ->where('status', 'hadir')
                    ->count();

                $persen = $totalSesi > 0 ? round(($hadirCount / $totalSesi) * 100, 2) : 0;

                KualifikasiSertifikasiModel::updateOrCreate(
                    ['pendaftaran_id' => $row['pendaftaran_id']],
                    [
                        'instruktur_id'   => $instrukturId,
                        'persen_hadir'    => $persen,
                        'memenuhi_syarat' => $persen >= KualifikasiSertifikasiModel::BATAS_KEHADIRAN ? 'lulus' : 'tidak lulus',
                        'catatan'         => $row['catatan'] ?? null,
                    ]
                );
            }
        });

        return redirect(route('instruktur.kelayakan.index') . '?pelatihan_id=' . $pelatihan->id_pelatihan)
            ->with('success', 'Penilaian kelayakan semua peserta berhasil disimpan.');
    }

    public function riwayatSertifInstruktur(Request $request)
    {
        $this->authorizeRole(['instruktur']);

        $instruktur = Auth::user();

        $query = KualifikasiSertifikasiModel::with(['pendaftaran.peserta', 'pendaftaran.pelatihan'])
            ->where('instruktur_id', $instruktur->id_user);

        if ($request->filled('memenuhi_syarat')) {
            $nilai = $request->input('memenuhi_syarat') === '1' ? 'lulus' : 'tidak lulus';
            $query->where('memenuhi_syarat', $nilai);
        }
        if ($request->filled('pelatihan_id')) {
            $query->whereHas('pendaftaran', fn($q) => $q->where('pelatihan_id', $request->pelatihan_id));
        }

        $kualifikasi = $query->latest('tgl_penilaian')->paginate(15)->withQueryString();
        $pelatihan   = PelatihanModel::where('instruktur_id', $instruktur->id_user)->get();

        return view('instruktur.kelayakan.riwayat', compact('kualifikasi', 'pelatihan'));
    }


    public function adminIndex(Request $request)
    {
        $this->authorizeRole(['admin']);

        $query = KualifikasiSertifikasiModel::with(['pendaftaran.peserta', 'pendaftaran.pelatihan', 'instruktur']);

        if ($request->filled('memenuhi_syarat')) {
            $nilai = $request->input('memenuhi_syarat') === '1' ? 'lulus' : 'tidak lulus';
            $query->where('memenuhi_syarat', $nilai);
        }
    
        $kualifikasi = $query->latest('tgl_penilaian')->paginate(20)->withQueryString();

        return view('admin.kualifikasi.index', compact('kualifikasi'));
    }

    public function statusKelayakanPeserta()
    {
        $this->authorizeRole(['peserta']);

        $peserta = Auth::user();

        $kualifikasi = KualifikasiSertifikasiModel::with(['pendaftaran.pelatihan'])
            ->whereHas('pendaftaran', fn($q) => $q->where('peserta_id', $peserta->id_user))
            ->get();

        return view('peserta.kelayakan.index', compact('kualifikasi'));
    }

    private function getPesertaDenganKelayakan(PelatihanModel $pelatihan, int $instrukturId): \Illuminate\Support\Collection
    {
        $pesertaList = PendaftaranModel::with(['peserta', 'kualifikasiSertifikasi'])
            ->where('pelatihan_id', $pelatihan->id_pelatihan)
            ->where('status', 'diterima')
            ->get();

        $totalSesi = SesiPelatihanModel::where('pelatihan_id', $pelatihan->id_pelatihan)->count();

        return $pesertaList->map(function ($daftar) use ($totalSesi, $pelatihan) {
            $hadir = LogbookModel::where('peserta_id', $daftar->peserta_id)
                ->whereHas('sesiPelatihan', fn($q) => $q->where('pelatihan_id', $pelatihan->id_pelatihan))
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
