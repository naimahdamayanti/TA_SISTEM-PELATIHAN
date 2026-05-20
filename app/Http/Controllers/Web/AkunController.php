<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AkunController extends Controller
{
    /* ═══════════════════════════════════════════════
     |  ADMIN – Kelola Akun Seluruh Pengguna
     ═══════════════════════════════════════════════ */

    /**
     * [ADMIN] Daftar seluruh akun pengguna di sistem.
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = UserModel::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'      => UserModel::count(),
            'admin'      => UserModel::where('role', 'admin')->count(),
            'instruktur' => UserModel::where('role', 'instruktur')->count(),
            'peserta'    => UserModel::where('role', 'peserta')->count(),
        ];

        return view('admin.akun.index', compact('users', 'stats'));
    }

    /**
     * [ADMIN] Form tambah akun baru.
     */
    public function create()
    {
        $this->authorizeAdmin();
        return view('admin.akun.create');
    }

    /**
     * [ADMIN] Simpan akun baru.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|max:100|unique:users,email',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => ['required', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
            'no_hp'    => 'nullable|string|max:20',
            'role'     => 'required|in:admin,instruktur,peserta',
        ]);

        UserModel::create([
            'nama'     => $validated['nama'],
            'email'    => $validated['email'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'no_hp'    => $validated['no_hp'] ?? null,
            'role'     => $validated['role'],
        ]);

        return redirect()->route('admin.akun.index')
            ->with('success', 'Akun baru berhasil dibuat.');
    }

    /**
     * [ADMIN] Form edit akun pengguna manapun.
     */
    public function edit(UserModel $user)
    {
        $this->authorizeAdmin();
        return view('admin.akun.edit', compact('user'));
    }

    /**
     * [ADMIN] Perbarui akun pengguna manapun.
     */
    public function update(Request $request, UserModel $user)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'nama'     => 'required|string|max:100',
            'email'    => ['required', 'email', 'max:100',
                Rule::unique('users', 'email')->ignore($user->id_user, 'id_user'),
            ],
            'username' => ['required', 'string', 'max:50',
                Rule::unique('users', 'username')->ignore($user->id_user, 'id_user'),
            ],
            'no_hp'    => 'nullable|string|max:20',
            'role'     => 'required|in:admin,instruktur,peserta',
            'password' => ['nullable', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
        ]);

        $data = [
            'nama'     => $validated['nama'],
            'email'    => $validated['email'],
            'username' => $validated['username'],
            'no_hp'    => $validated['no_hp'] ?? null,
            'role'     => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.akun.index')
            ->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * [ADMIN] Hapus akun pengguna.
     */
    public function destroy(UserModel $user)
    {
        $this->authorizeAdmin();

        if ($user->id_user === Auth::user()->id_user) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Hapus foto profil jika ada
        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $user->delete();

        return redirect()->route('admin.akun.index')
            ->with('success', 'Akun berhasil dihapus.');
    }

    /* ═══════════════════════════════════════════════
     |  SEMUA ROLE – Profil & Update Akun Sendiri
     ═══════════════════════════════════════════════ */

    /**
     * [ADMIN | INSTRUKTUR | PESERTA] Tampilkan profil pengguna yang sedang login.
     */
    public function profil()
    {
        $user = Auth::user();
        return view('shared.profil', compact('user'));
    }

    /**
     * [ADMIN | INSTRUKTUR | PESERTA] Perbarui data profil sendiri (nama, email, no_hp).
     */
    public function updateProfil(Request $request)
    {
        /** @var UserModel $user */
        $user = Auth::user();

        $validated = $request->validate([
            'nama'  => 'required|string|max:100',
            'email' => ['required', 'email', 'max:100',
                Rule::unique('users', 'email')->ignore($user->id_user, 'id_user'),
            ],
            'no_hp' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * [ADMIN | INSTRUKTUR | PESERTA] Perbarui foto profil.
     */
    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto_profil' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        /** @var UserModel $user */
        $user = Auth::user();

        // Hapus foto lama
        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $path = $request->file('foto_profil')->store('foto-profil', 'public');
        $user->update(['foto_profil' => $path]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    /**
     * [ADMIN | INSTRUKTUR | PESERTA] Ubah password sendiri.
     */
    public function gantiPassword(Request $request)
    {
        /** @var UserModel $user */
        $user = Auth::user();

        $request->validate([
            'password_lama'     => 'required|string',
            'password_baru'     => ['required', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
        ]);

        // Verifikasi password lama
        if (!Hash::check($request->password_lama, $user->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($request->password_baru)]);

        return back()->with('success', 'Password berhasil diubah.');
    }

    /* ─── Helper ─── */

    private function authorizeAdmin(): void
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }
    }
}
