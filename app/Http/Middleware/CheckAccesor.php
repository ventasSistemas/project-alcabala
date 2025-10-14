<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAccesor
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Si el usuario tiene un accesor vinculado
        if ($user && $user->accesor) {
            return $next($request);
        }

        abort(403, 'Acceso denegado');
    }
}
