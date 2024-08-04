<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HiddenTechnology
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Lanjutkan request ke aplikasi
        $response = $next($request);

        // Menghapus X-Powered-By header
        $response->headers->remove('X-Powered-By');

        // Menghapus Server header
        $response->headers->remove('Server');

        return $response;
    }
}
