<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;    
use App\Models\UserModel;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
       $request->validate([
        'nama' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:users',
        'email' => 'required|email|max:100|unique:users',
        'password' => 'required|string|min:6',
        'role' => 'required|in:instruktur,peserta'
    ]);

    UserModel::create([
        'username' => $request->username,
        'email' => $request->email,
        'nama' => $request->nama,
        'password' => bcrypt($request->password),
        'role' => $request->role 
    ]);

    return redirect()->route('login')->with('success', 'Registrasi berhasil!');

    }
}
