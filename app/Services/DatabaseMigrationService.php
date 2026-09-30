<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use RuntimeException;

class DatabaseMigrationService
{
    public function migrate(): array
    {
        $exitCode = Artisan::call('migrate', [
            '--force' => true,
        ]);

        $output = Artisan::output();

        if ($exitCode !== 0) {
            throw new RuntimeException("Database migration gagal.\n" . $output);
        }

        return [
            'success' => true,
            'output' => $output,
        ];
    }
}
