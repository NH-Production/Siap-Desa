<?php

namespace App\Services;

use App\Models\SystemVersion;
use RuntimeException;

class AppVersionService
{
    public function current(): array
    {
        $row = SystemVersion::query()->latest('id')->first();

        return [
            'app_version' => $row?->app_version ?: config('siapdesa.version', '1.0.0'),
            'schema_version' => (int) ($row?->schema_version ?: $this->schemaNumber()),
            'sync_protocol_version' => (int) ($row?->sync_protocol_version ?: 1),
        ];
    }

    public function assertMinimum(string $minimumVersion): void
    {
        if (version_compare($this->current()['app_version'], $minimumVersion, '<')) {
            throw new RuntimeException(
                "SIAP-DESA membutuhkan versi aplikasi minimal {$minimumVersion}."
            );
        }
    }

    public function register(string $appVersion, int $schemaVersion, int $syncProtocolVersion, ?string $remarks = null): SystemVersion
    {
        return SystemVersion::create([
            'app_version' => $appVersion,
            'schema_version' => $schemaVersion,
            'sync_protocol_version' => $syncProtocolVersion,
            'remarks' => $remarks,
        ]);
    }

    private function schemaNumber(): int
    {
        return (int) env('DB_SCHEMA_VERSION', 1);
    }
}
