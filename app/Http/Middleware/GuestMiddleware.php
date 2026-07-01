<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestMiddleware
{
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
