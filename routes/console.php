<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('siap:health', function () {
    $this->info('SIAP Desa System Health: OK');
})->purpose('Check local system health status');
