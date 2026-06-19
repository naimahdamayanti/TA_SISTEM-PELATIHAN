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
    public function index(PelatihanModel $pelatihan)
    {
        $this->authorizeRole(['admin']);

        $sesi = SesiPelatihanModel::where('pelatihan_id', $pelatihan->id_pelatihan)
            ->withCount('logbook')  
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->get();

        $pesertaDiterima = PendaftaranModel::where('pelatihan_id', $pelatihan->id_pelatihan)
            ->where('status', 'diterima')
            ->count();

        return view('admin.sesi.index', compact('pelatihan', 'sesi', 'pesertaDiterima'));
    }

    public function create(PelatihanModel $pelatihan)
    {
        $this->authorizeRole(['admin']);

        return view('admin.sesi.create', compact('pelatihan'));
    }

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

    public function edit(PelatihanModel $pelatihan, SesiPelatihanModel $sesi)
    {
        $this->authorizeRole(['admin']);
        $this->pastikanMilikPelatihan($sesi, $pelatihan);
        return view('admin.sesi.edit', compact('pelatihan', 'sesi'));
    }

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

    public function destroy(PelatihanModel $pelatihan, SesiPelatihanModel $sesi)
    {
        $this->authorizeRole(['admin']);
        $this->pastikanMilikPelatihan($sesi, $pelatihan);

        $sesi->delete();

        return redirect()->route('admin.sesi.index', $pelatihan->id_pelatihan)
            ->with('success', 'Sesi berhasil dihapus.');
    }

    public function jadwalSesi(Request $request)
    {
        $this->authorizeRole(['instruktur']);

        $instruktur = Auth::user();

        $query = SesiPelatihanModel::whereHas('pelatihan', fn($q) =>
            $q->where('instruktur_id', $instruktur->id_user)
        )->with('pelatihan')->orderBy('tanggal')->orderBy('waktu_mulai');

        if ($request->filled('pelatihan_id')) {
            $query->where('pelatihan_id', $request->pelatihan_id);
        }

        $sesi = $query->paginate(15)->withQueryString();

        $pelatihan = PelatihanModel::where('instruktur_id', $instruktur->id_user)->get();

        return view('instruktur.sesi.jadwal', compact('sesi', 'pelatihan'));
    }

    public function detailSesi(SesiPelatihanModel $sesi)
    {
        $this->authorizeRole(['instruktur']);

        if ($sesi->pelatihan->instruktur_id !== Auth::user()->id_user) {
            abort(403, 'Sesi ini bukan bagian dari pelatihan Anda.');
        }

        $sesi->load('pelatihan');

        $pesertaTerdaftar = PendaftaranModel::with('peserta')
            ->where('pelatihan_id', $sesi->pelatihan_id)
            ->where('status', 'diterima')
            ->get();

        $logbookTerisi = LogbookModel::where('sesi_id', $sesi->id_sesi)
            ->pluck('status', 'peserta_id')
            ->toArray();

        return view('instruktur.sesi.detail', compact('sesi', 'pesertaTerdaftar', 'logbookTerisi'));
    }

    public function statusKehadiran(Request $request)
    {
        $this->authorizeRole(['peserta']);

        $peserta = Auth::user();

        $pendaftaranDiterima = PendaftaranModel::where('peserta_id', $peserta->id_user)
            ->where('status', 'diterima')
            ->with(['pelatihan.sesiPelatihan'])
            ->get();

        $logbook = LogbookModel::where('peserta_id', $peserta->id_user)
            ->get()
            ->keyBy('sesi_id');

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
