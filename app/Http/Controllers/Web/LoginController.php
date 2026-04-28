<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;


class LoginController extends Controller
{
     public function showLoginForm()
    {
        // Jika user sudah login, redirect ke dashboard
        if (session()->has('id_user') && session()->has('role')) {
            return redirect()->route('dashboard');
        }

        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = UserModel::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'login_error' => 'Email atau password salah'
            ])->withInput($request->only('email'));
        }

        // Simpan session
        session()->flush();
        session([
            'id_user' => $user->id,
            'email' => $user->email,
            'nama' => $user->nama,
            'role' => $user->role,
            'logged_in' => true
        ]);

        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Login berhasil!');
    }

    public function logout()
    {
        // Hapus semua session
        session()->flush();
        return redirect()->route('login')->with('success', 'Logout berhasil!');
    }
}

