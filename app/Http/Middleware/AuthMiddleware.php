<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Cek apakah user sudah login (ada token di session).
     * Jika belum → redirect ke halaman login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! session('token')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
