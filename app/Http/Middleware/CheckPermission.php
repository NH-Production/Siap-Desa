<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        if ($user->hasRole('superadmin')) {
            return $next($request);
        }

        if (!$user->hasPermission($permission)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Anda tidak memiliki hak akses untuk tindakan ini.'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'Akses ditolak: Anda tidak memiliki izin (' . $permission . ').');
        }

        return $next($request);
    }
}
