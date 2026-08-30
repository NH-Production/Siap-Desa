<?php

namespace Database\Seeders;

use App\Models\CentralDevice;
use App\Models\CentralLicense;
use App\Models\CentralRelease;
use App\Models\CentralServerSetting;
use App\Models\CentralSyncLog;
use App\Models\CentralVillage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CentralServerSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Official Supabase Project Settings
        CentralServerSetting::set('supabase_url', 'https://yrzuksjgmsumtllbokpc.supabase.co', 'Official Supabase Cloud REST URL');
        CentralServerSetting::set('supabase_key', 'sb_publishable_UeCe6pqUTtRhBgLjpT1djA_anoXjXve', 'Supabase API Publishable Key');
        CentralServerSetting::set('supabase_publishable_key', 'sb_publishable_UeCe6pqUTtRhBgLjpT1djA_anoXjXve', 'Supabase API Publishable Key');
        CentralServerSetting::set('supabase_jwks_url', 'https://yrzuksjgmsumtllbokpc.supabase.co/auth/v1/.well-known/jwks.json', 'Supabase JWKS URL');
        CentralServerSetting::set('central_api_key', 'sb_publishable_UeCe6pqUTtRhBgLjpT1djA_anoXjXve', 'Central API Access Key');
        CentralServerSetting::set('sync_interval_seconds', '30', 'Global Client Sync Interval (detik)');
        CentralServerSetting::set('realtime_sync_enabled', '1', 'Aktifkan Realtime Push Sync');
        CentralServerSetting::set('github_repo', 'NH-Production/Siap-Desa', 'GitHub Repository Identifier');

        // 2. Desa Sindangresmi (Tenant 1)
        $v1 = CentralVillage::updateOrCreate(
            ['code' => '3203162002'],
            [
                'client_id' => 'CLNT-3203162002-SINDANGRESMI',
                'name' => 'Sindangresmi',
                'district' => 'Takokak',
                'regency' => 'Kabupaten Cianjur',
                'province' => 'Jawa Barat',
                'head_name' => 'IMAS, S.IP., NL.P',
                'phone' => '0812-3456-7890',
                'email' => 'pemdes@sindangresmi-takokak.desa.id',
                'status' => 'ACTIVE',
                'address' => 'Jl. Raya Takokak No. 12, Desa Sindangresmi',
            ]
        );

        $lic1 = CentralLicense::updateOrCreate(
            ['license_key' => 'SIAP-SAAS-3203-1620-02-2026'],
            [
                'central_village_id' => $v1->id,
                'locked_client_id' => $v1->client_id,
                'tier' => 'ENTERPRISE',
                'max_devices' => 10,
                'issued_date' => '2026-01-01',
                'expiry_date' => '2027-01-01',
                'status' => 'ACTIVE',
                'notes' => 'Lisensi resmi Pemerintah Desa Sindangresmi (Supabase Cloud Connected)',
            ]
        );

        CentralDevice::updateOrCreate(
            ['central_license_id' => $lic1->id, 'device_code' => 'PC-ADMIN-01'],
            [
                'device_name' => 'Komputer Pelayanan Utama Desa Sindangresmi',
                'app_version' => '1.0.0',
                'last_seen_at' => now(),
                'status' => 'ACTIVE',
            ]
        );

        // 3. Desa Sukamaju (Tenant 2)
        $v2 = CentralVillage::updateOrCreate(
            ['code' => '3203162001'],
            [
                'client_id' => 'CLNT-3203162001-SUKAMAJU',
                'name' => 'Sukamaju Sejahtera',
                'district' => 'Takokak',
                'regency' => 'Kabupaten Cianjur',
                'province' => 'Jawa Barat',
                'head_name' => 'H. Rahmat Hidayat, S.Sos',
                'phone' => '0813-9876-5432',
                'email' => 'pemdes@sukamaju.desa.id',
                'status' => 'ACTIVE',
                'address' => 'Jl. Desa Sukamaju No. 01',
            ]
        );

        CentralLicense::updateOrCreate(
            ['license_key' => 'SIAP-SAAS-3203-STD-7712-2026'],
            [
                'central_village_id' => $v2->id,
                'locked_client_id' => $v2->client_id,
                'tier' => 'STANDARD',
                'max_devices' => 3,
                'issued_date' => '2026-02-01',
                'expiry_date' => '2027-02-01',
                'status' => 'ACTIVE',
                'notes' => 'Lisensi Standard Desa Sukamaju',
            ]
        );

        // 4. Central Release v1.0.1
        CentralRelease::updateOrCreate(
            ['version' => '1.0.1'],
            [
                'schema_version' => 2,
                'title' => 'Pembaruan Modul Server Cloud Supabase, Live Camera Scanner, dan Icon Desktop',
                'changelog' => "- Integrasi Supabase Cloud PostgreSQL REST API (yrzuksjgmsumtllbokpc).\n- Penambahan Live Camera QR Code scanner dengan audio bip.\n- Central Cloud Server Panel & Dynamic Client ID Locking.\n- Icon resmi Windows .ICO.",
                'file_name' => 'SIAP_Desa_Patch_v1.0.1.zip',
                'file_path' => 'downloads/patches/SIAP_Desa_Patch_v1.0.1.zip',
                'file_size' => 135311,
                'is_mandatory' => false,
                'is_published' => true,
                'release_date' => '2026-08-30',
            ]
        );

        // 5. Sample Telemetry Log
        CentralSyncLog::create([
            'village_code' => '3203162002',
            'device_code' => 'PC-ADMIN-01',
            'direction' => 'PUSH',
            'records_count' => 12,
            'status' => 'SUCCESS',
            'latency_ms' => 45,
            'details' => 'Handshake & delta push to Supabase Cloud yrzuksjgmsumtllbokpc',
        ]);
    }
}
