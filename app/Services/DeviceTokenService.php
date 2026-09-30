<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Support\Str;

class DeviceTokenService
{
    public function issue(Device $device): string
    {
        $token = Str::random(80);
        $device->forceFill([
            'device_token_hash' => hash('sha256', $token),
            'last_seen_at' => now(),
        ])->save();

        return $token;
    }

    public function rotate(Device $device): string
    {
        return $this->issue($device);
    }
}