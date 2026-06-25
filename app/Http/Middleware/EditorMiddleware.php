<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EditorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!in_array(Auth::user()?->rol, ['admin', 'editor'], true)) {
            abort(403, 'No tiene permisos para modificar informacion.');
        }

        return $next($request);
    }
}
