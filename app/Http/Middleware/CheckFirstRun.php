<?php

namespace App\Http\Middleware;

use App\Models\Village;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFirstRun
{
    public function handle(Request $request, Closure $next): Response
    {
        // If there are no villages or no admin user configured, redirect to setup wizard
        $villageCount = Village::count();
        $userCount = User::count();

        if ($villageCount === 0 || $userCount === 0) {
            if (!$request->is('setup*')) {
                return redirect()->route('setup.index');
            }
        } else {
            if ($request->is('setup*')) {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
