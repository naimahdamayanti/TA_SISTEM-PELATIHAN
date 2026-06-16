<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PelatihanModel;
use App\Models\KategoriModel;
use App\Models\SesiPelatihanModel;
use App\Models\PendaftaranModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PelatihanController extends Controller
{
    /* ═══════════════════════════════════════════════
     |  ADMIN – Kelola Pelatihan
     ═══════════════════════════════════════════════ */

    /**
     * [ADMIN] Tampilkan daftar semua pelatihan beserta instruktur & jumlah peserta.
     */
    public function index(Request $request)
    {
        $this->authorizeRole(['admin']);

        $query = PelatihanModel::with('instruktur', 'kategori')
            ->withCount(['pendaftaran' => fn($q) => $q->where('status', 'diterima')]);

        // Filter opsional
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_pelatihan', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_pelatihan', 'like', '%' . $request->search . '%');
            });
        }

        $pelatihan  = $query->latest()->paginate(10)->withQueryString();
        $instruktur = UserModel::where('role', 'instruktur')->get();
        $kategori   = KategoriModel::aktif()->get();

        return view('admin.pelatihan.index', compact('pelatihan', 'instruktur', 'kategori'));
    }

    /**
     * [ADMIN] Form tambah pelatihan baru.
     */
    public function create()
    {
        $this->authorizeRole(['admin']);

        $instruktur = UserModel::where('role', 'instruktur')->get();
        $kategori   = KategoriModel::aktif()->get();
        return view('admin.pelatihan.create', compact('instruktur', 'kategori'));
    }

    /**
     * [ADMIN] Simpan pelatihan baru ke database.
     */
    public function store(Request $request)
    {
        $this->authorizeRole(['admin']);

        $validated = $request->validate([
            'instruktur_id'  => 'required|exists:users,id_user',
            'nama_pelatihan' => 'required|string|max:100',
            'kode_pelatihan' => 'required|string|max:15|unique:pelatihan,kode_pelatihan',
            'kategori_id'    => 'required|exists:kategori,id_kategori',
            'deskripsi'      => 'required|string',
            'kuota'          => 'required|integer|min:1|max:500',
            'tgl_mulai'      => 'nullable|date',
            'tgl_selesai'    => 'nullable|date|after_or_equal:tgl_mulai',
            'status'         => 'required|in:tersedia,sedang berlangsung,selesai',
        ]);

        PelatihanModel::create($validated);

        return redirect()->route('admin.pelatihan.index')
            ->with('success', 'Pelatihan berhasil ditambahkan.');
    }

    /**
     * [ADMIN] Form edit pelatihan.
     */
    public function edit(PelatihanModel $pelatihan)
    {
        $this->authorizeRole(['admin']);

        $instruktur = UserModel::where('role', 'instruktur')->get();
        $kategori   = KategoriModel::aktif()->get();
        return view('admin.pelatihan.edit', compact('pelatihan', 'instruktur', 'kategori'));
    }

    /**
     * [ADMIN] Perbarui data pelatihan.
     */
    public function update(Request $request, PelatihanModel $pelatihan)
    {
        $this->authorizeRole(['admin']);

        $validated = $request->validate([
            'instruktur_id'  => 'required|exists:users,id_user',
            'nama_pelatihan' => 'required|string|max:100',
            'kode_pelatihan' => ['required', 'string', 'max:15',
                Rule::unique('pelatihan', 'kode_pelatihan')->ignore($pelatihan->id_pelatihan, 'id_pelatihan'),
            ],
            'kategori_id'    => 'required|exists:kategori,id_kategori',
            'deskripsi'      => 'required|string',
            'kuota'          => 'required|integer|min:1|max:500',
            'tgl_mulai'      => 'nullable|date',
            'tgl_selesai'    => 'nullable|date|after_or_equal:tgl_mulai',
            'status'         => 'required|in:tersedia,sedang berlangsung,selesai',
        ]);

        $pelatihan->update($validated);

        return redirect()->route('admin.pelatihan.index')
            ->with('success', 'Data pelatihan berhasil diperbarui.');
    }

    /**
     * [ADMIN] Hapus pelatihan (CASCADE ke sesi, pendaftaran, dst.).
     */
    public function destroy(PelatihanModel $pelatihan)
    {
        $this->authorizeRole(['admin']);

        $pelatihan->delete();

        return redirect()->route('admin.pelatihan.index')
            ->with('success', 'Pelatihan berhasil dihapus.');
    }

    /* ═══════════════════════════════════════════════
     |  INSTRUKTUR – Pelatihan Saya
     ═══════════════════════════════════════════════ */

    /**
     * [INSTRUKTUR] Daftar pelatihan yang diampu instruktur login.
     */
    public function pelatihanSaya(Request $request)
    {
        $this->authorizeRole(['instruktur']);

        $instruktur = Auth::user();

        $query = PelatihanModel::where('instruktur_id', $instruktur->id_user)
            ->withCount(['pendaftaran' => fn($q) => $q->where('status', 'diterima'),
            'sesiPelatihan',
            ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pelatihan = $query->latest()->paginate(10)->withQueryString();

        return view('instruktur.pelatihan.index', compact('pelatihan'));
    }
    /**
     * [INSTRUKTUR] Detail pelatihan: daftar peserta diterima.
     */
    public function detailPelatihanSaya(PelatihanModel $pelatihan)
    {
        $this->authorizeRole(['instruktur']);
        $this->pastikanMilikInstruktur($pelatihan);

        $peserta = PendaftaranModel::with('peserta')
            ->where('pelatihan_id', $pelatihan->id_pelatihan)
            ->where('status', 'diterima')
            ->get();

        $sesi = SesiPelatihanModel::where('pelatihan_id', $pelatihan->id_pelatihan)
            ->orderBy('tanggal')
            ->get();

        return view('instruktur.pelatihan.detail', compact('pelatihan', 'peserta', 'sesi'));
    }

    /* ═══════════════════════════════════════════════
     |  PESERTA – Katalog Pelatihan
     ═══════════════════════════════════════════════ */

    /**
     * [PESERTA] Katalog pelatihan tersedia beserta tombol daftar.
     */
    public function katalog(Request $request)
    {
        $this->authorizeRole(['peserta']);

        $peserta = Auth::user();

        // ID pelatihan yang sudah didaftarkan peserta ini
        $sudahDaftar = PendaftaranModel::where('peserta_id', $peserta->id_user)
            ->pluck('pelatihan_id')
            ->toArray();

        $query = PelatihanModel::with('instruktur', 'kategori')
            ->withCount(['pendaftaran' => fn($q) => $q->where('status', 'diterima')]);

        if ($request->filled('search')) {
            $query->where('nama_pelatihan', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        // Hanya tampilkan yang tersedia
        $query->where('status', 'tersedia');

        $pelatihan = $query->latest()->paginate(9)->withQueryString();
        $kategori  = KategoriModel::aktif()->get();

        return view('peserta.pelatihan.index', compact('pelatihan', 'kategori', 'sudahDaftar'));
    }

    /**
     * [PESERTA] Detail pelatihan + form pendaftaran.
     */
    public function detailKatalog(PelatihanModel $pelatihan)
    {
        $this->authorizeRole(['peserta']);

        $pelatihan->load('instruktur');
        $sesi = SesiPelatihanModel::where('pelatihan_id', $pelatihan->id_pelatihan)
            ->orderBy('tanggal')
            ->get();

        $peserta = Auth::user();
        $sudahDaftar = PendaftaranModel::where('peserta_id', $peserta->id_user)
            ->where('pelatihan_id', $pelatihan->id_pelatihan)
            ->exists();

        return view('peserta.pelatihan.detail', compact('pelatihan', 'sesi', 'sudahDaftar'));
    }

    /* ─── Helper ─── */

    private function authorizeRole(array $roles): void
    {
        if (!in_array(Auth::user()->role, $roles)) {
            abort(403, 'Akses ditolak.');
        }
    }

    private function pastikanMilikInstruktur(PelatihanModel $pelatihan): void
    {
        if ($pelatihan->instruktur_id !== Auth::id()) {
            abort(403, 'Pelatihan ini bukan milik Anda.');
        }
    }
}
