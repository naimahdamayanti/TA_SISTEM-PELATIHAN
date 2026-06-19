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
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = UserModel::where('role', 'instruktur')
            ->withCount('pelatihan')
            ->with(['pelatihan.pendaftaran'])
            ->orderByRaw("CASE
                WHEN status_verifikasi = 'menunggu' THEN 0
                WHEN status_verifikasi IS NULL       THEN 1
                WHEN status_verifikasi = 'terverifikasi' THEN 1
                ELSE 2
            END");

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama',     'like', '%' . $request->search . '%')
                  ->orWhere('email',    'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%');
            });
        }

        $instruktur = $query->latest()->paginate(10)->withQueryString();
        $pelatihan  = PelatihanModel::orderBy('nama_pelatihan')->get();

        return view('admin.instruktur.index', compact('instruktur', 'pelatihan'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        return view('admin.instruktur.create');
    }

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
            'nama'              => $validated['nama'],
            'email'             => $validated['email'],
            'username'          => $validated['username'],
            'password'          => Hash::make($validated['password']),
            'no_hp'             => $validated['no_hp'] ?? null,
            'role'              => 'instruktur',
            'status_verifikasi' => 'terverifikasi',
        ]);
        
        return redirect()->route('admin.instruktur.index')
            ->with('success', 'Instruktur baru berhasil ditambahkan.');
    }

    public function edit(UserModel $instruktur)
    {
        $this->authorizeAdmin();
        $this->pastikanRoleInstruktur($instruktur);

        $pelatihan = PelatihanModel::where('instruktur_id', $instruktur->id_user)->get();

        return view('admin.instruktur.edit', compact('instruktur', 'pelatihan'));
    }

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

    public function destroy(UserModel $instruktur)
    {
        $this->authorizeAdmin();
        $this->pastikanRoleInstruktur($instruktur);

        if ($instruktur->id_user === Auth::user()->id_user) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $instruktur->delete();

        return redirect()->route('admin.instruktur.index')
            ->with('success', 'Instruktur berhasil dihapus.');
    }

    public function verifikasi(Request $request, UserModel $user)
    {
        $this->authorizeAdmin();
        $this->pastikanRoleInstruktur($user);

        $request->validate([
            'status_verifikasi'  => 'required|in:terverifikasi,ditolak',
            'catatan_verifikasi' => 'nullable|string|max:500',
        ]);

        $user->update([
            'status_verifikasi'  => $request->status_verifikasi,
            'catatan_verifikasi' => $request->catatan_verifikasi,
        ]);

        $pesan = $request->status_verifikasi === 'terverifikasi'
            ? "Instruktur {$user->nama} berhasil diverifikasi."
            : "Instruktur {$user->nama} ditolak.";

        return redirect()->route('admin.instruktur.index')->with('success', $pesan);
    }

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

        if ($instruktur->status_verifikasi !== 'terverifikasi') {
            return back()->with('error',
                "Instruktur {$instruktur->nama} belum terverifikasi dan tidak dapat ditugaskan."
            );
        }

        $pelatihan = PelatihanModel::findOrFail($validated['pelatihan_id']);
        $pelatihan->update(['instruktur_id' => $instruktur->id_user]);

        return back()->with('success',
            "Instruktur {$instruktur->nama} berhasil ditugaskan ke pelatihan {$pelatihan->nama_pelatihan}."
        );
    }

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