<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/* ══════════════════════════════════════════════════════════════
 |  AkunController
 |  Mengelola akun pengguna (CRUD admin) dan profil sendiri
 ══════════════════════════════════════════════════════════════ */

class AkunController extends Controller
{
    /* ═══════════════════════════════════════════════
     |  GET /api/akun   [ADMIN]
     ═══════════════════════════════════════════════ */

    public function index(Request $request): JsonResponse
    {
        $this->onlyAdmin($request);

        $query = UserModel::select([
            'id_user', 'nama', 'email', 'username', 'no_hp',
            'foto_profil', 'role', 'created_at',
        ]);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $query->where(fn($q) =>
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%')
            );
        }

        $users = $query->latest()->paginate($request->input('per_page', 15));

        // Resolusi URL foto profil
        $users->getCollection()->transform(fn($u) => $this->resolusiFoto($u));

        $stats = [
            'total'      => UserModel::count(),
            'admin'      => UserModel::where('role', 'admin')->count(),
            'instruktur' => UserModel::where('role', 'instruktur')->count(),
            'peserta'    => UserModel::where('role', 'peserta')->count(),
        ];

        return response()->json([
            'success' => true,
            'data'    => $users,
            'stats'   => $stats,
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  GET /api/akun/{id}   [ADMIN]
     ═══════════════════════════════════════════════ */

    public function show(Request $request, int $id): JsonResponse
    {
        $this->onlyAdmin($request);

        $user = UserModel::select([
            'id_user', 'nama', 'email', 'username', 'no_hp',
            'foto_profil', 'role', 'created_at', 'updated_at',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $this->resolusiFoto($user),
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  POST /api/akun   [ADMIN]
     ═══════════════════════════════════════════════ */

    public function store(Request $request): JsonResponse
    {
        $this->onlyAdmin($request);

        $validated = $request->validate([
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|max:100|unique:users,email',
            'username' => 'required|string|max:50|unique:users,username|alpha_dash',
            'password' => ['required', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
            'no_hp'    => 'nullable|string|max:20',
            'role'     => 'required|in:admin,instruktur,peserta',
        ]);

        $user = UserModel::create([
            'nama'     => $validated['nama'],
            'email'    => $validated['email'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'no_hp'    => $validated['no_hp'] ?? null,
            'role'     => $validated['role'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Akun berhasil dibuat.',
            'data'    => $this->resolusiFoto($user),
        ], 201);
    }

    /* ═══════════════════════════════════════════════
     |  PUT /api/akun/{id}   [ADMIN]
     ═══════════════════════════════════════════════ */

    public function update(Request $request, int $id): JsonResponse
    {
        $this->onlyAdmin($request);

        $user = UserModel::findOrFail($id);

        $validated = $request->validate([
            'nama'     => 'sometimes|string|max:100',
            'email'    => ['sometimes', 'email', 'max:100',
                Rule::unique('users', 'email')->ignore($id, 'id_user'),
            ],
            'username' => ['sometimes', 'string', 'max:50', 'alpha_dash',
                Rule::unique('users', 'username')->ignore($id, 'id_user'),
            ],
            'no_hp'    => 'nullable|string|max:20',
            'role'     => 'sometimes|in:admin,instruktur,peserta',
            'password' => ['nullable', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Akun berhasil diperbarui.',
            'data'    => $this->resolusiFoto($user->fresh()),
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  DELETE /api/akun/{id}   [ADMIN]
     ═══════════════════════════════════════════════ */

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->onlyAdmin($request);

        if ($request->user()->id_user === $id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun sendiri.',
            ], 422);
        }

        $user = UserModel::findOrFail($id);

        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Akun berhasil dihapus.',
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  GET /api/profil   [SEMUA ROLE]
     ═══════════════════════════════════════════════ */

    public function profil(Request $request): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data'    => $this->resolusiFoto($user),
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  PUT /api/profil   [SEMUA ROLE]
     ═══════════════════════════════════════════════ */

    public function updateProfil(Request $request): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        $validated = $request->validate([
            'nama'  => 'sometimes|string|max:100',
            'email' => ['sometimes', 'email', 'max:100',
                Rule::unique('users', 'email')->ignore($user->id_user, 'id_user'),
            ],
            'no_hp' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'data'    => $this->resolusiFoto($user->fresh()),
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  POST /api/profil/foto   [SEMUA ROLE]
     ═══════════════════════════════════════════════ */

    public function updateFoto(Request $request): JsonResponse
    {
        $request->validate([
            'foto_profil' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        /** @var UserModel $user */
        $user = $request->user();

        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $path = $request->file('foto_profil')->store('foto-profil', 'public');
        $user->update(['foto_profil' => $path]);

        return response()->json([
            'success'     => true,
            'message'     => 'Foto profil berhasil diperbarui.',
            'foto_url'    => asset('storage/' . $path),
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  POST /api/profil/password   [SEMUA ROLE]
     ═══════════════════════════════════════════════ */

    public function gantiPassword(Request $request): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        $request->validate([
            'password_lama' => 'required|string',
            'password_baru' => ['required', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
        ]);

        if (!Hash::check($request->password_lama, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai.',
                'errors'  => ['password_lama' => ['Password lama tidak sesuai.']],
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password_baru)]);

        // Cabut semua token lain (paksa login ulang di perangkat lain)
        $user->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.',
        ]);
    }

    /* ─── Helper ─── */

    private function resolusiFoto(UserModel $user): UserModel
    {
        if ($user->foto_profil) {
            $user->foto_profil = asset('storage/' . $user->foto_profil);
        }
        return $user;
    }

    private function onlyAdmin(Request $request): void
    {
        if ($request->user()->role !== 'admin') {
            abort(response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403));
        }
    }
}
