<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Support\Str;

class DeviceIdentityService
{
    public function uuid(): string
    {
        $configured = env('SIAP_DEVICE_UUID');

        if ($configured && Str::isUuid($configured)) {
            return $configured;
        }

        $path = config('siapdesa.data_path', storage_path('app'));
        $file = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'device.uuid';

        if (is_file($file)) {
            $uuid = trim((string) file_get_contents($file));

            if (Str::isUuid($uuid)) {
                return $uuid;
            }
        }

        $uuid = (string) Str::uuid();

        if (!is_dir(dirname($file))) {
            @mkdir(dirname($file), 0775, true);
        }

        @file_put_contents($file, $uuid, LOCK_EX);

        return $uuid;
    }

    public function ensureRegistered(?string $villageUuid = null): Device
    {
        $uuid = $this->uuid();

        return Device::query()->updateOrCreate(
            ['uuid' => $uuid],
            [
                'village_id' => $villageUuid ?: config('siapdesa.village_uuid'),
                'name' => gethostname() ?: 'SIAP-DESA-PC',
                'device_code' => 'DEV-' . strtoupper(substr(str_replace('-', '', $uuid), 0, 12)),
                'app_version' => config('siapdesa.version', '1.0.0'),
                'schema_version' => (int) config('siapdesa.schema_version', 1),
                'sync_protocol_version' => (int) config('siapdesa.sync_protocol_version', 1),
                'status' => 'ACTIVE',
                'last_seen_at' => now(),
            ]
        );
    }
}
