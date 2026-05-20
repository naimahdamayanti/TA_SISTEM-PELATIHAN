<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestMiddleware
{
    /**
     * Jika user sudah login, jangan biarkan akses halaman guest
     * (login, register, forgot password).
     * Redirect langsung ke dashboard sesuai role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session('token')) {
            return match (session('role')) {
                'admin'      => redirect()->route('admin.dashboard'),
                'instruktur' => redirect()->route('instruktur.dashboard'),
                'peserta'    => redirect()->route('peserta.dashboard'),
                default      => redirect()->route('login'),
            };
        }

        return $next($request);
    }
}
