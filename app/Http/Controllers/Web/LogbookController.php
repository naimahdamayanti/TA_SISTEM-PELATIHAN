<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LogbookModel;
use App\Models\PelatihanModel;
use App\Models\PendaftaranModel;
use App\Models\SesiPelatihanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogbookController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeRole(['instruktur']);
        $instruktur = Auth::user();

        $pelatihan = PelatihanModel::where('instruktur_id', $instruktur->id_user)->get();

        // Resolve pelatihan yang dipilih
        $pelatihanDipilih = $request->filled('pelatihan_id')
            ? PelatihanModel::find($request->pelatihan_id)
            : null;

        // Daftar sesi dari pelatihan yang dipilih
        $sesiList = $pelatihanDipilih
            ? SesiPelatihanModel::where('pelatihan_id', $pelatihanDipilih->id_pelatihan)
                ->orderBy('tanggal')->get()
            : collect();

        // Resolve sesi yang dipilih
        $sesiDipilih = $request->filled('sesi_id')
            ? $sesiList->firstWhere('id_sesi', $request->sesi_id)
            : null;

        // Peserta terdaftar & logbook yang sudah ada
        $peserta    = collect();
        $logbookAda = collect();

        if ($sesiDipilih) {
            $peserta = PendaftaranModel::with('peserta')
                ->where('pelatihan_id', $sesiDipilih->pelatihan_id)
                ->where('status', 'diterima')
                ->get();

            $logbookAda = LogbookModel::where('sesi_id', $sesiDipilih->id_sesi)
                ->get()->keyBy('peserta_id');
        }

        $sesiSudahDiisi = LogbookModel::where('instruktur_id', $instruktur->id_user)
            ->select('sesi_id', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('sesi_id')
            ->pluck('jumlah', 'sesi_id');

        return view('instruktur.logbook.index', compact(
            'pelatihan', 'pelatihanDipilih', 'sesiList',
            'sesiDipilih', 'peserta', 'logbookAda', 'sesiSudahDiisi'
        ));
    }

    public function inputKehadiran(SesiPelatihanModel $sesi)
    {
        $this->authorizeRole(['instruktur']);
        $this->pastikanSesiMilikInstruktur($sesi);

        $sesi->load('pelatihan');

        $peserta = PendaftaranModel::with('peserta')
            ->where('pelatihan_id', $sesi->pelatihan_id)
            ->where('status', 'diterima')
            ->get();

        $logbookAda = LogbookModel::where('sesi_id', $sesi->id_sesi)
            ->get()
            ->keyBy('peserta_id');

        return view('instruktur.logbook.input', compact('sesi', 'peserta', 'logbookAda'));
    }

    public function simpanKehadiran(Request $request, SesiPelatihanModel $sesi)
    {
        $this->authorizeRole(['instruktur']);
        $this->pastikanSesiMilikInstruktur($sesi);

        $request->validate([
            'kehadiran'              => 'required|array',
            'kehadiran.*.peserta_id' => 'required|exists:users,id_user',
            'kehadiran.*.status'     => 'required|in:hadir,izin,tidak hadir',
            'kehadiran.*.catatan'    => 'nullable|string|max:255',
        ]);

        $instrukturId = Auth::user()->id_user;

        DB::transaction(function () use ($request, $sesi, $instrukturId) {
            foreach ($request->input('kehadiran') as $row) {
                LogbookModel::updateOrCreate(
                    [
                        'sesi_id'    => $sesi->id_sesi,
                        'peserta_id' => $row['peserta_id'],
                    ],
                    [
                        'instruktur_id' => $instrukturId,
                        'status'        => $row['status'],
                        'catatan'       => $row['catatan'] ?? null,
                    ]
                );
            }
        });

        return redirect()->route('instruktur.logbook.index')
            ->with('success', 'Logbook kehadiran berhasil disimpan.');
    }

    public function rekapKehadiran(PelatihanModel $pelatihan)
    {
        $this->authorizeRole(['instruktur']);

        if ($pelatihan->instruktur_id !== Auth::user()->id_user) {
            abort(403, 'Pelatihan ini bukan milik Anda.');
        }

        $sesiList = SesiPelatihanModel::where('pelatihan_id', $pelatihan->id_pelatihan)
            ->orderBy('tanggal')
            ->get();

        $pesertaList = PendaftaranModel::with('peserta')
            ->where('pelatihan_id', $pelatihan->id_pelatihan)
            ->where('status', 'diterima')
            ->get();

        $logbookMatrix = LogbookModel::where('instruktur_id', Auth::user()->id_user)
            ->whereIn('sesi_id', $sesiList->pluck('id_sesi'))
            ->get()
            ->groupBy('peserta_id')
            ->map(fn($rows) => $rows->keyBy('sesi_id'));

        $totalSesi = $sesiList->count();
        $rekapPersen = [];
        foreach ($pesertaList as $daftar) {
            $hadirCount = 0;
            if (isset($logbookMatrix[$daftar->peserta_id])) {
                $hadirCount = $logbookMatrix[$daftar->peserta_id]
                    ->filter(fn($lb) => $lb->status === 'hadir')
                    ->count();
            }
            $rekapPersen[$daftar->peserta_id] = $totalSesi > 0
                ? round(($hadirCount / $totalSesi) * 100, 2)
                : 0;
        }

        return view('instruktur.logbook.rekap', compact(
            'pelatihan', 'sesiList', 'pesertaList', 'logbookMatrix', 'rekapPersen'
        ));
    }

    public function adminIndex(Request $request)
    {
        $this->authorizeRole(['admin']);

        $query = LogbookModel::with(['sesi.pelatihan', 'peserta', 'instruktur'])
            ->latest();

        if ($request->filled('pelatihan_id')) {
            $query->whereHas('sesi', fn($q) =>
                $q->where('pelatihan_id', $request->pelatihan_id)
            );
        }

        $logbook   = $query->paginate(20)->withQueryString();
        $pelatihan = PelatihanModel::orderBy('nama_pelatihan')->get();

        return view('admin.logbook.index', compact('logbook', 'pelatihan'));
    }

    private function authorizeRole(array $roles): void
    {
        if (!in_array(Auth::user()->role, $roles)) {
            abort(403, 'Akses ditolak.');
        }
    }

    private function pastikanSesiMilikInstruktur(SesiPelatihanModel $sesi): void
    {
        $sesi->loadMissing('pelatihan');
        if ($sesi->pelatihan->instruktur_id !== Auth::user()->id_user) {
            abort(403, 'Sesi ini bukan bagian dari pelatihan yang Anda ampu.');
        }
    }
}
