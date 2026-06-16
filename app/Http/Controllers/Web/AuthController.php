<?php

namespace App\Http\Controllers\Web;

use App\Helpers\MailHelper;
use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /* ═══════════════════════════════════════════════
     |  LOGIN
     ═══════════════════════════════════════════════ */

    public function loginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = UserModel::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password tidak sesuai.']);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return $this->redirectByRole($user->role);
    }

    /* ═══════════════════════════════════════════════
     |  REGISTER (Peserta)
     ═══════════════════════════════════════════════ */

    public function registerForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|max:100|unique:users,email',
            'username' => 'required|string|max:50|unique:users,username|alpha_dash',
            'password' => 'required|string|min:6',
            'no_hp'    => 'nullable|string|max:20',
            'role'     => 'required|in:peserta,instruktur',
        ]);

        UserModel::create([
            'nama'     => $validated['nama'],
            'email'    => $validated['email'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'no_hp'    => $validated['no_hp'] ?? null,
            'role'     => $validated['role'],
        ]);

        return redirect()->route('login')
            ->with('success', 'Akun berhasil dibuat.');
    }

    /* ═══════════════════════════════════════════════
     |  LOGOUT
     ═══════════════════════════════════════════════ */

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome')
            ->with('success', 'Anda berhasil keluar dari sistem.');
    }

    /* ═══════════════════════════════════════════════
     |  FORGOT PASSWORD
     ═══════════════════════════════════════════════ */

    public function forgotForm()
    {
        return view('lupa-password');
    }

    /**
     * [PUBLIC] Kirim link reset password ke email.
     * Token disimpan ke kolom token_reset & token_exp di tabel users.
     */
    public function forgotSend(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = UserModel::where('email', $request->email)->first();

        // Respons sama meskipun email tidak ditemukan (hindari email enumeration)
        if (!$user) {
            return back()->with('success', 'Jika email terdaftar, link reset password telah dikirim.');
        }

        // Generate token & simpan ke kolom users
        $token  = Str::random(64);
        $expiry = Carbon::now()->addMinutes(60);

        $user->update([
            'token_reset' => $token,
            'token_exp'   => $expiry,
        ]);

        $resetLink = url('/reset-password/' . $token);

        $result = MailHelper::sendResetPasswordEmail($user->email, $resetLink);

        if (($result['status'] ?? 'error') !== 'success') {
            return back()->with('error', 'Gagal mengirim email. Silakan coba lagi atau hubungi admin.');
        }
        return back()->with('success', 'Link reset password telah dikirim ke email Anda.');
    }

    /**
     * [PUBLIC] Tampilkan form reset password (via token dari email).
     */
    public function resetForm(string $token)
    {
        $user = UserModel::where('token_reset', $token)
            ->where('token_exp', '>', Carbon::now())
            ->first();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Link reset password tidak valid atau sudah kedaluwarsa.');
        }

        return view('reset', compact('token'));
    }

    /**
     * [PUBLIC] Proses reset password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = UserModel::where('token_reset', $request->token)
            ->where('token_exp', '>', Carbon::now())
            ->first();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Link reset password tidak valid atau sudah kedaluwarsa.');
        }

        $user->update([
            'password'    => Hash::make($request->password),
            'token_reset' => null,
            'token_exp'   => null,
        ]);

        return redirect()->route('login')
            ->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }

    /* ═══════════════════════════════════════════════
     |  LANDING PAGE
     ═══════════════════════════════════════════════ */

    public function landing()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('welcome');
    }

    /* ─── Helper ─── */

    private function redirectByRole(string $role): \Illuminate\Http\RedirectResponse
    {
        return match ($role) {
            'admin'      => redirect()->route('admin.dashboard'),
            'instruktur' => redirect()->route('instruktur.dashboard'),
            'peserta'    => redirect()->route('peserta.dashboard'),
            default      => redirect()->route('landing'),
        };
    }
}