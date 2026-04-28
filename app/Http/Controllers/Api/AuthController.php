<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,instruktur,peserta'
        ]);

        $user = UserModel::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'api_token' => Str::random(60)
        ]);

        return response()->json([
            'message' => 'Register berhasil',
            'token' => $user->api_token,
            'user' => $user
        ]);
    }

    public function login(Request $request)
    {
        $user = UserModel::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }

        // generate token baru
        $user->api_token = Str::random(60);
        $user->save();

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $user->api_token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $user = UserModel::where('api_token', $request->bearerToken())->first();

        if ($user) {
            $user->api_token = null;
            $user->save();
        }

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }
}