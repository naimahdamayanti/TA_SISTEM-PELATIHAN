<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\UserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    /* ═══════════════════════════════════════════════
     |  POST /api/login
     ═══════════════════════════════════════════════ */

    /**
     * Login dan kembalikan Sanctum token.
     * Bisa digunakan oleh admin, instruktur, dan peserta.
     *
     * @bodyParam email    string required  Email terdaftar.
     * @bodyParam password string required  Password akun.
     *
     * @response 200 {
     *   "success": true,
     *   "token": "1|abcdefg...",
     *   "token_type": "Bearer",
     *   "user": { "id_user": 1, "nama": "Administrator", "role": "admin" }
     * }
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = UserModel::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password tidak sesuai.',
            ], 401);
        }

        // Hapus token lama lalu buat yang baru
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success'    => true,
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => $this->userResource($user),
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  POST /api/register
     ═══════════════════════════════════════════════ */

    /**
     * Registrasi akun peserta baru.
     * Role selalu 'peserta' — admin/instruktur dibuat dari panel admin.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|max:100|unique:users,email',
            'username' => 'required|string|max:50|unique:users,username|alpha_dash',
            'password' => 'required|string|min:8|confirmed',
            'no_hp'    => 'nullable|string|max:20',
        ]);

        $user = UserModel::create([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'no_hp'    => $request->no_hp,
            'role'     => 'peserta',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success'    => true,
            'message'    => 'Akun berhasil dibuat.',
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => $this->userResource($user),
        ], 201);
    }

    /* ═══════════════════════════════════════════════
     |  POST /api/logout
     ═══════════════════════════════════════════════ */

    /**
     * Logout — cabut token yang sedang digunakan.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout.',
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  GET /api/me
     ═══════════════════════════════════════════════ */

    /**
     * Data profil pengguna yang sedang login.
     */
    public function me(Request $request): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data'    => $this->userResource($user),
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  POST /api/forgot-password
     ═══════════════════════════════════════════════ */

    /**
     * Kirim email reset password.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = UserModel::where('email', $request->email)->first();

        // Selalu kembalikan 200 untuk cegah email enumeration
        if (!$user) {
            return response()->json([
                'success' => true,
                'message' => 'Jika email terdaftar, tautan reset telah dikirim.',
            ]);
        }

        $token  = Str::random(64);
        $expiry = Carbon::now()->addMinutes(60);

        $user->update([
            'token_reset' => $token,
            'token_exp'   => $expiry,
        ]);

        Mail::to($user->email)->send(new ResetPasswordMail($token, $user->nama));

        return response()->json([
            'success' => true,
            'message' => 'Tautan reset password telah dikirim ke email Anda.',
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  POST /api/reset-password
     ═══════════════════════════════════════════════ */

    /**
     * Proses reset password menggunakan token dari email.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = UserModel::where('token_reset', $request->token)
            ->where('token_exp', '>', Carbon::now())
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau sudah kedaluwarsa.',
            ], 422);
        }

        $user->update([
            'password'    => Hash::make($request->password),
            'token_reset' => null,
            'token_exp'   => null,
        ]);

        // Cabut semua token lama agar login ulang wajib
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset. Silakan login kembali.',
        ]);
    }

    /* ─── Private Helper ─── */

    private function userResource(UserModel $user): array
    {
        return [
            'id_user'     => $user->id_user,
            'nama'        => $user->nama,
            'email'       => $user->email,
            'username'    => $user->username,
            'no_hp'       => $user->no_hp,
            'foto_profil' => $user->foto_profil
                ? asset('storage/' . $user->foto_profil)
                : null,
            'role'        => $user->role,
            'created_at'  => $user->created_at?->toDateTimeString(),
        ];
    }
}
