<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CentralDeviceAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $deviceUuid = $request->header('X-SIAP-Device');

        if (!$token || !$deviceUuid) {
            return response()->json(['message' => 'Device credentials are required.'], 401);
        }

        $device = Device::where('uuid', $deviceUuid)
            ->where('status', 'active')
            ->first();

        if (!$device || !$device->device_token_hash || !hash_equals($device->device_token_hash, hash('sha256', $token))) {
            return response()->json(['message' => 'Invalid or revoked device credentials.'], 401);
        }

        $request->attributes->set('siap_device', $device);
        return $next($request);
    }
}