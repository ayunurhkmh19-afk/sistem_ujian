<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictBrainAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Deteksi apakah request ditujukan ke halaman laravel-brain
        if ($request->is('_laravel-brain') || $request->is('_laravel-brain/*')) {
            // Cek apakah user sudah terautentikasi
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            // Cek apakah role user adalah panitia
            if (Auth::user()->role !== 'panitia') {
                abort(403, 'ANDA TIDAK MEMILIKI AKSES KE HALAMAN INI.');
            }
        }

        return $next($request);
    }
}
