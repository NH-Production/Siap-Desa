<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Village;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InitSetupCommand extends Command
{
    protected $signature = 'siap:setup-init {--config-file=}';
    protected $description = 'Inisialisasi pengaturan profil desa, akun super admin, dan lisensi SAAS dari installer setup wizard';

    public function handle(): int
    {
        $configFile = $this->option('config-file') ?? 'C:/ProgramData/SIAP Desa/config/setup_input.json';

        if (!File::exists($configFile)) {
            $this->warn("Config file tidak ditemukan di: {$configFile}. Menggunakan default data.");
            return 0;
        }

        $data = json_decode(File::get($configFile), true);
        if (!$data) {
            $this->error("Format JSON pada config file tidak valid.");
            return 1;
        }

        $this->info("Menerapkan konfigurasi dari Setup Wizard...");

        // 1. Update Profil Desa
        $village = Village::first() ?? new Village(['uuid' => (string)Str::uuid()]);
        $village->name = $data['village_name'] ?? 'Sindangresmi';
        $village->code = $data['village_code'] ?? '3203162002';
        $village->district = $data['district'] ?? 'Takokak';
        $village->regency = $data['regency'] ?? 'Kabupaten Cianjur';
        $village->province = $data['province'] ?? 'Jawa Barat';
        $village->head_name = $data['head_name'] ?? 'IMAS, S.IP., NL.P';
        $village->address = $data['address'] ?? 'Jl. Raya Takokak No. 12, Desa Sindangresmi';
        $village->postal_code = $data['postal_code'] ?? '43265';
        $village->save();

        // 2. Update System Settings & SAAS License
        SystemSetting::set('village_name', $village->name);
        SystemSetting::set('village_code', $village->code);
        SystemSetting::set('is_configured', '1');
        SystemSetting::set('saas_license_key', $data['license_key'] ?? 'SIAP-SAAS-3203-1620-02-2026');
        SystemSetting::set('saas_status', $data['license_status'] ?? 'ACTIVE_ENTERPRISE');
        SystemSetting::set('central_api_url', $data['central_api_url'] ?? 'https://api.siapdesa.id/api/v1');

        // 3. Update Super Admin Account
        $adminUser = User::where('username', $data['admin_username'] ?? 'admin')->first() ?? User::where('username', 'admin')->first();
        if (!$adminUser) {
            $adminUser = new User();
            $adminUser->uuid = (string)Str::uuid();
        }
        $adminUser->name = $data['admin_fullname'] ?? 'Administrator Desa';
        $adminUser->username = $data['admin_username'] ?? 'admin';
        $adminUser->email = $data['admin_email'] ?? 'admin@sindangresmi-takokak.desa.id';
        if (!empty($data['admin_password'])) {
            $adminUser->password = Hash::make($data['admin_password']);
        }
        $adminUser->is_active = true;
        $adminUser->save();

        $adminRole = Role::where('name', 'SUPER_ADMIN')->first();
        if ($adminRole && !$adminUser->roles()->where('roles.id', $adminRole->id)->exists()) {
            $adminUser->roles()->attach($adminRole->id);
        }

        // 4. Update Device
        $device = Device::first();
        if ($device) {
            $device->village_id = $village->uuid;
            $device->device_code = $data['device_code'] ?? 'PC-ADMIN-01';
            $device->save();
        }

        $this->info("Konfigurasi Desa, Super Admin, dan Lisensi SAAS berhasil diterapkan 100%!");
        return 0;
    }
}
