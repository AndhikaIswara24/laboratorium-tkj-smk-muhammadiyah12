<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $roles = null)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        if ($roles === null) {
            return $next($request);
        }

        // Split roles if separated by pipe
        $allowedRoles = explode('|', $roles);
        
        if (!in_array(auth()->user()->role, $allowedRoles)) {
            abort(403, 'Akses ditolak. Hanya role: ' . implode(', ', $allowedRoles) . ' yang diizinkan.');
        }

        return $next($request);
    }
}