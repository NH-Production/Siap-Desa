<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Support\Str;

class DeviceIdentityService
{
    public function uuid(): string
    {
        $configured = env('SIAP_DEVICE_UUID');
        if ($configured && Str::isUuid($configured)) return $configured;

        $path = env('SIAP_DATA_PATH', storage_path('app'));
        $file = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'device.uuid';

        if (is_file($file)) {
            $uuid = trim((string) file_get_contents($file));
            if (Str::isUuid($uuid)) return $uuid;
        }

        $uuid = (string) Str::uuid();
        if (!is_dir(dirname($file))) @mkdir(dirname($file), 0775, true);
        @file_put_contents($file, $uuid, LOCK_EX);
        return $uuid;
    }

    public function ensureRegistered(?string $villageUuid = null): Device
    {
        return Device::query()->firstOrCreate(
            ['uuid' => $this->uuid()],
            [
                'village_uuid' => $villageUuid ?: env('SIAP_VILLAGE_UUID'),
                'device_name' => gethostname() ?: 'SIAP-DESA-PC',
                'app_version' => config('siapdesa.version', '1.0.0'),
                'schema_version' => config('siapdesa.schema_version', '1.0.0'),
                'status' => 'active',
            ]
        );
    }
}
