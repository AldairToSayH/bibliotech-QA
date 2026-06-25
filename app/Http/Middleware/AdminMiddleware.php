<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()?->rol !== 'admin') {
            abort(403, 'Solo un administrador puede acceder a esta seccion.');
        }

        return $next($request);
    }
}
