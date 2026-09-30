<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Village;
use App\Services\Audit\AuditService;
use App\Services\DeviceIdentityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $village = Village::first();
        $device = Device::first();

        return view('auth.login', compact('village', 'device'));
    }

    public function login(Request $request, DeviceIdentityService $identity)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'username' => 'Username atau kata sandi tidak cocok dengan data kami.',
            ])->withInput($request->only('username'));
        }

        $request->session()->regenerate();

        $user = Auth::user();
        $user->update(['last_login_at' => now()]);

        $device = Device::query()->where('uuid', $identity->uuid())->first();

        session([
            'device_uuid' => $device?->uuid ?: $identity->uuid(),
            'village_uuid' => $user->village_id,
        ]);

        AuditService::log(
            'LOGIN',
            'users',
            (string) $user->uuid,
            null,
            ['username' => $user->username]
        );

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Selamat datang kembali, ' . $user->name);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            AuditService::log('LOGOUT', 'users', (string) $user->uuid);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil keluar dari sistem.');
    }
}
