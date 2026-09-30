<?php

use App\Http\Controllers\AidController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CitizenController;
use App\Http\Controllers\ConflictController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiagnosticController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\FirstRunController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\VillageController;
use Illuminate\Support\Facades\Route;

// System runtime version contract
Route::get('/system/version', [\App\Http\Controllers\SystemController::class, 'version'])->name('system.version');

// First Run Setup Wizard
Route::prefix('setup')->name('setup.')->group(function () {
    Route::get('/', [FirstRunController::class, 'index'])->name('index');
    Route::post('/save', [FirstRunController::class, 'store'])->name('store');
});

// Authentication
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth', 'first_run'])->group(function () {
    // Dashboard & Live Stats
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Village Profile & Settings
    Route::prefix('village')->name('village.')->group(function () {
        Route::get('/profile', [VillageController::class, 'index'])->name('profile');
        Route::post('/update', [VillageController::class, 'update'])->name('update');
    });

    // Citizens (Penduduk)
    Route::prefix('citizens')->name('citizens.')->group(function () {
        Route::get('/', [CitizenController::class, 'index'])->name('index');
        Route::get('/create', [CitizenController::class, 'create'])->name('create');
        Route::post('/store', [CitizenController::class, 'store'])->name('store');
        Route::post('/import', [CitizenController::class, 'import'])->name('import');
        Route::get('/export/csv', [CitizenController::class, 'exportCsv'])->name('export.csv');
        Route::get('/{uuid}', [CitizenController::class, 'show'])->name('show');
        Route::get('/{uuid}/edit', [CitizenController::class, 'edit'])->name('edit');
        Route::put('/{uuid}', [CitizenController::class, 'update'])->name('update');
        Route::delete('/{uuid}', [CitizenController::class, 'destroy'])->name('destroy');
    });

    // Families (KK)
    Route::prefix('families')->name('families.')->group(function () {
        Route::get('/', [FamilyController::class, 'index'])->name('index');
        Route::get('/create', [FamilyController::class, 'create'])->name('create');
        Route::post('/store', [FamilyController::class, 'store'])->name('store');
        Route::get('/{uuid}', [FamilyController::class, 'show'])->name('show');
        Route::get('/{uuid}/edit', [FamilyController::class, 'edit'])->name('edit');
        Route::put('/{uuid}', [FamilyController::class, 'update'])->name('update');
        Route::delete('/{uuid}', [FamilyController::class, 'destroy'])->name('destroy');
        Route::post('/{uuid}/members', [FamilyController::class, 'addMember'])->name('members.add');
        Route::delete('/members/{memberUuid}', [FamilyController::class, 'removeMember'])->name('members.remove');
    });

    // Employees & QR Attendance
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/store', [EmployeeController::class, 'store'])->name('store');
        Route::post('/{uuid}/rotate-qr', [EmployeeController::class, 'rotateQr'])->name('rotate-qr');
        Route::get('/{uuid}', [EmployeeController::class, 'show'])->name('show');
        Route::get('/{uuid}/qr-card', [EmployeeController::class, 'qrCard'])->name('qr-card');
        Route::put('/{uuid}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{uuid}', [EmployeeController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/scanner', [AttendanceController::class, 'scanner'])->name('scanner');
        Route::post('/scan-submit', [AttendanceController::class, 'scanSubmit'])->name('scan.submit');
        Route::post('/manual', [AttendanceController::class, 'manualStore'])->name('manual');
        Route::post('/correction', [AttendanceController::class, 'requestCorrection'])->name('correction');
        Route::post('/correction/{uuid}/resolve', [AttendanceController::class, 'resolveCorrection'])->name('correction.resolve');
    });

    Route::get('/verify/letter/{token}', [LetterController::class, 'verify'])->name('letters.verify');

    // Letters & Services
    Route::prefix('letters')->name('letters.')->group(function () {
        Route::get('/', [LetterController::class, 'index'])->name('index');
        Route::get('/create', [LetterController::class, 'create'])->name('create');
        Route::post('/store', [LetterController::class, 'store'])->name('store');
        Route::get('/{uuid}', [LetterController::class, 'show'])->name('show');
        Route::get('/{uuid}/print', [LetterController::class, 'printLetter'])->name('print');
        Route::post('/{uuid}/approve', [LetterController::class, 'approveLetter'])->name('approve');
        Route::delete('/{uuid}', [LetterController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('index');
        Route::post('/store', [ServiceController::class, 'store'])->name('store');
        Route::put('/{uuid}/status', [ServiceController::class, 'updateStatus'])->name('status');
    });

    // Finance (APBDes, Transaksi, SPJ)
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/', [FinanceController::class, 'index'])->name('index');
        Route::get('/create', [FinanceController::class, 'create'])->name('create');
        Route::post('/store', [FinanceController::class, 'store'])->name('store');
        Route::get('/accounts', [FinanceController::class, 'accounts'])->name('accounts');
        Route::post('/accounts', [FinanceController::class, 'storeAccount'])->name('accounts.store');
        Route::get('/budgets', [FinanceController::class, 'budgets'])->name('budgets');
        Route::post('/budgets', [FinanceController::class, 'storeBudget'])->name('budgets.store');
        Route::get('/spj', [FinanceController::class, 'spjReport'])->name('spj');
    });

    // Assets & Inventaris
    Route::prefix('assets')->name('assets.')->group(function () {
        Route::get('/', [AssetController::class, 'index'])->name('index');
        Route::get('/create', [AssetController::class, 'create'])->name('create');
        Route::post('/store', [AssetController::class, 'store'])->name('store');
        Route::get('/{uuid}', [AssetController::class, 'show'])->name('show');
        Route::post('/{uuid}/mutation', [AssetController::class, 'storeMutation'])->name('mutation');
        Route::delete('/{uuid}', [AssetController::class, 'destroy'])->name('destroy');
    });

    // Social Aid (Bantuan Sosial)
    Route::prefix('aid')->name('aid.')->group(function () {
        Route::get('/', [AidController::class, 'index'])->name('index');
        Route::post('/program', [AidController::class, 'storeProgram'])->name('program.store');
        Route::post('/recipient', [AidController::class, 'storeRecipient'])->name('recipient.store');
        Route::post('/recipient/{uuid}/distribute', [AidController::class, 'markDistributed'])->name('recipient.distribute');
    });

    // Reports Multi-Modul
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/citizens', [ReportController::class, 'citizensReport'])->name('citizens');
        Route::get('/finance', [ReportController::class, 'financeReport'])->name('finance');
        Route::get('/attendance', [ReportController::class, 'attendanceReport'])->name('attendance');
        Route::get('/assets', [ReportController::class, 'assetsReport'])->name('assets');
    });

    // Sync Center & Conflict Center
    Route::prefix('sync')->name('sync.')->group(function () {
        Route::get('/', [SyncController::class, 'index'])->name('index');
        Route::post('/push', [SyncController::class, 'triggerPush'])->name('push');
        Route::post('/pull', [SyncController::class, 'triggerPull'])->name('pull');
        Route::post('/full', [SyncController::class, 'triggerFullSync'])->name('full');
        Route::get('/conflicts', [ConflictController::class, 'index'])->name('conflicts');
        Route::post('/conflicts/{uuid}/resolve', [ConflictController::class, 'resolve'])->name('conflicts.resolve');
    });

    // Backup & Restore
    Route::prefix('backup')->name('backup.')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::post('/create', [BackupController::class, 'createBackup'])->name('create');
        Route::post('/restore', [BackupController::class, 'restoreBackup'])->name('restore');
        Route::get('/download/{id}', [BackupController::class, 'downloadBackup'])->name('download');
    });

    // System Settings & Updates
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/test-central', [SettingController::class, 'testCentralConnection'])->name('settings.test-central');
        Route::get('/updates', [UpdateController::class, 'index'])->name('updates');
        Route::get('/updates/check-online', [UpdateController::class, 'checkOnline'])->name('updates.check');
        Route::post('/updates/patch', [UpdateController::class, 'applyPatch'])->name('updates.patch');
        Route::get('/diagnostics', [DiagnosticController::class, 'index'])->name('diagnostics');
        Route::get('/audit-logs', [DiagnosticController::class, 'auditLogs'])->name('audit');
    });
});

// Central Cloud Server Management Panel Routes
Route::prefix('central')->name('central.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Central\CentralDashboardController::class, 'index'])->name('dashboard');
    Route::prefix('villages')->name('villages.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Central\CentralVillageController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Central\CentralVillageController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Central\CentralVillageController::class, 'store'])->name('store');
    });
    Route::prefix('licenses')->name('licenses.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Central\CentralLicenseController::class, 'index'])->name('index');
        Route::post('/store', [\App\Http\Controllers\Central\CentralLicenseController::class, 'store'])->name('store');
        Route::post('/{uuid}/toggle', [\App\Http\Controllers\Central\CentralLicenseController::class, 'toggleStatus'])->name('toggle');
    });
    Route::prefix('releases')->name('releases.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Central\CentralReleaseController::class, 'index'])->name('index');
        Route::post('/store', [\App\Http\Controllers\Central\CentralReleaseController::class, 'store'])->name('store');
    });
        Route::get('/telemetry', [\App\Http\Controllers\Central\CentralTelemetryController::class, 'index'])->name('telemetry');
    Route::get('/telemetry/live', [\App\Http\Controllers\Central\CentralTelemetryController::class, 'liveData'])->name('telemetry.live');
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Central\CentralServerSettingController::class, 'index'])->name('index');
                Route::post('/update', [\App\Http\Controllers\Central\CentralServerSettingController::class, 'update'])->name('update');
        Route::post('/test-supabase', [\App\Http\Controllers\Central\CentralServerSettingController::class, 'testSupabase'])->name('test-supabase');
    });
});



