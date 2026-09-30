<?php

use App\Http\Controllers\Api\CentralLicenseApiController;
use App\Http\Controllers\Api\CentralSyncApiController;
use App\Http\Controllers\Api\CentralUpdateApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/client/config', [CentralLicenseApiController::class, 'config'])->name('api.client.config');
    Route::post('/license/verify', [CentralLicenseApiController::class, 'verify'])->name('api.license.verify');

    Route::middleware('device.auth')->group(function () {
        Route::post('/sync/push', [CentralSyncApiController::class, 'push'])->name('api.sync.push');
        Route::get('/sync/pull', [CentralSyncApiController::class, 'pull'])->name('api.sync.pull');
    });

    Route::get('/updates/check', [CentralUpdateApiController::class, 'check'])->name('api.updates.check');
    Route::get('/updates/download/{version}', [CentralUpdateApiController::class, 'download'])->name('api.updates.download');
});
