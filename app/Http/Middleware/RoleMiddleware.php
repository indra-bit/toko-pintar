<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Jika tidak login, langsung tolak
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        // Mendukung multiple roles dipisahkan dengan '|' atau ',' misal: 'admin|pemilik' atau 'admin,pemilik'
        $allowedRoles = array_map('trim', preg_split('/[\|,]/', $role));

        if (in_array(Auth::user()->role, $allowedRoles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized');
    }
}
