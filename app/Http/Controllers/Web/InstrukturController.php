<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use App\Models\PelatihanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class InstrukturController extends Controller
{
    /* ═══════════════════════════════════════════════
     |  ADMIN – Kelola Instruktur
     ═══════════════════════════════════════════════ */

    /**
     * [ADMIN] Daftar semua instruktur beserta jumlah pelatihan yang diampu.
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = UserModel::where('role', 'instruktur')
            ->withCount('pelatihan')
            ->with(['pelatihan.pendaftaran']);;

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%');
            });
        }

        $instruktur = $query->latest()->paginate(10)->withQueryString();
        $pelatihan = PelatihanModel::orderBy('nama_pelatihan')->get();

        return view('admin.instruktur.index', compact('instruktur', 'pelatihan'));
    }

    /**
     * [ADMIN] Form tambah instruktur baru.
     */
    public function create()
    {
        $this->authorizeAdmin();

        return view('admin.instruktur.create');
    }

    /**
     * [ADMIN] Simpan instruktur baru.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|max:100|unique:users,email',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'no_hp'    => 'nullable|string|max:20',
        ]);

        UserModel::create([
            'nama'     => $validated['nama'],
            'email'    => $validated['email'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'no_hp'    => $validated['no_hp'] ?? null,
            'role'     => 'instruktur',
        ]);

        return redirect()->route('admin.instruktur.index')
            ->with('success', 'Instruktur baru berhasil ditambahkan.');
    }

    /**
     * [ADMIN] Form edit instruktur.
     */
    public function edit(UserModel $instruktur)
    {
        $this->authorizeAdmin();
        $this->pastikanRoleInstruktur($instruktur);

        $pelatihan = PelatihanModel::where('instruktur_id', $instruktur->id_user)->get();

        return view('admin.instruktur.edit', compact('instruktur', 'pelatihan'));
    }

    /**
     * [ADMIN] Perbarui data instruktur.
     */
    public function update(Request $request, UserModel $instruktur)
    {
        $this->authorizeAdmin();
        $this->pastikanRoleInstruktur($instruktur);

        $validated = $request->validate([
            'nama'     => 'required|string|max:100',
            'email'    => ['required', 'email', 'max:100',
                Rule::unique('users', 'email')->ignore($instruktur->id_user, 'id_user'),
            ],
            'username' => ['required', 'string', 'max:50',
                Rule::unique('users', 'username')->ignore($instruktur->id_user, 'id_user'),
            ],
            'no_hp'    => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'nama'     => $validated['nama'],
            'email'    => $validated['email'],
            'username' => $validated['username'],
            'no_hp'    => $validated['no_hp'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $instruktur->update($data);

        return redirect()->route('admin.instruktur.index')
            ->with('success', 'Data instruktur berhasil diperbarui.');
    }

    /**
     * [ADMIN] Hapus instruktur. Data pelatihan yang diampu akan ikut terhapus (CASCADE).
     */
    public function destroy(UserModel $instruktur)
    {
        $this->authorizeAdmin();
        $this->pastikanRoleInstruktur($instruktur);

        // Cegah instruktur menghapus dirinya sendiri
        if ($instruktur->id_user === Auth::user()->id_user) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $instruktur->delete();

        return redirect()->route('admin.instruktur.index')
            ->with('success', 'Instruktur berhasil dihapus.');
    }

    /**
     * [ADMIN] Tugaskan instruktur ke pelatihan (m-tugaskan di mockup).
     */
    public function tugaskan(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'instruktur_id' => 'required|exists:users,id_user',
            'pelatihan_id'  => 'required|exists:pelatihan,id_pelatihan',
        ]);

        $instruktur = UserModel::where('id_user', $validated['instruktur_id'])
            ->where('role', 'instruktur')
            ->firstOrFail();

        $pelatihan = PelatihanModel::findOrFail($validated['pelatihan_id']);
        $pelatihan->update(['instruktur_id' => $instruktur->id_user]);

        return back()->with('success', "Instruktur {$instruktur->nama} berhasil ditugaskan ke pelatihan {$pelatihan->nama_pelatihan}.");
    }

    /* ─── Helper ─── */

    private function authorizeAdmin(): void
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }
    }

    private function pastikanRoleInstruktur(UserModel $user): void
    {
        if ($user->role !== 'instruktur') {
            abort(404, 'Instruktur tidak ditemukan.');
        }
    }
}
