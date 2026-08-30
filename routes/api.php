<?php

use App\Http\Controllers\Api\CentralLicenseApiController;
use App\Http\Controllers\Api\CentralSyncApiController;
use App\Http\Controllers\Api\CentralUpdateApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // 1. License & Client Handshake
    Route::post('/license/verify', [CentralLicenseApiController::class, 'verify'])->name('api.license.verify');

    // 2. Delta Synchronization Ingestion
    Route::post('/sync/push', [CentralSyncApiController::class, 'push'])->name('api.sync.push');
    Route::get('/sync/pull', [CentralSyncApiController::class, 'pull'])->name('api.sync.pull');

    // 3. OTA Update & Release Distribution
    Route::get('/updates/check', [CentralUpdateApiController::class, 'check'])->name('api.updates.check');
    Route::get('/updates/download/{version}', [CentralUpdateApiController::class, 'download'])->name('api.updates.download');
});
