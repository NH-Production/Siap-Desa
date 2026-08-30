<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Village;
use App\Models\Device;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Citizens
            ['name' => 'citizens.view', 'display_name' => 'Lihat Penduduk', 'module' => 'citizens'],
            ['name' => 'citizens.create', 'display_name' => 'Tambah Penduduk', 'module' => 'citizens'],
            ['name' => 'citizens.update', 'display_name' => 'Ubah Penduduk', 'module' => 'citizens'],
            ['name' => 'citizens.delete', 'display_name' => 'Hapus Penduduk', 'module' => 'citizens'],
            
            // Families
            ['name' => 'families.view', 'display_name' => 'Lihat Kartu Keluarga', 'module' => 'families'],
            ['name' => 'families.manage', 'display_name' => 'Kelola Kartu Keluarga', 'module' => 'families'],
            
            // Employees & Attendance
            ['name' => 'employees.view', 'display_name' => 'Lihat Pegawai', 'module' => 'employees'],
            ['name' => 'employees.manage', 'display_name' => 'Kelola Pegawai', 'module' => 'employees'],
            ['name' => 'attendance.view', 'display_name' => 'Lihat Absensi', 'module' => 'attendance'],
            ['name' => 'attendance.create', 'display_name' => 'Catat Absensi', 'module' => 'attendance'],
            ['name' => 'attendance.verify', 'display_name' => 'Verifikasi Absensi', 'module' => 'attendance'],
            
            // Letters & Services
            ['name' => 'letters.view', 'display_name' => 'Lihat Surat', 'module' => 'letters'],
            ['name' => 'letters.create', 'display_name' => 'Buat Surat', 'module' => 'letters'],
            ['name' => 'letters.approve', 'display_name' => 'Setujui Surat', 'module' => 'letters'],
            ['name' => 'letters.print', 'display_name' => 'Cetak Surat', 'module' => 'letters'],
            ['name' => 'services.manage', 'display_name' => 'Kelola Pelayanan', 'module' => 'services'],
            
            // Finance
            ['name' => 'finance.view', 'display_name' => 'Lihat Keuangan', 'module' => 'finance'],
            ['name' => 'finance.create', 'display_name' => 'Input Transaksi Keuangan', 'module' => 'finance'],
            ['name' => 'finance.approve', 'display_name' => 'Setujui Keuangan', 'module' => 'finance'],
            
            // Assets
            ['name' => 'assets.view', 'display_name' => 'Lihat Aset', 'module' => 'assets'],
            ['name' => 'assets.manage', 'display_name' => 'Kelola Aset', 'module' => 'assets'],
            
            // Aid
            ['name' => 'aid.view', 'display_name' => 'Lihat Bantuan Sosial', 'module' => 'aid'],
            ['name' => 'aid.manage', 'display_name' => 'Kelola Bantuan Sosial', 'module' => 'aid'],
            
            // Reports & System
            ['name' => 'reports.view', 'display_name' => 'Lihat Laporan', 'module' => 'reports'],
            ['name' => 'sync.push', 'display_name' => 'Kirim Data Sync', 'module' => 'sync'],
            ['name' => 'sync.pull', 'display_name' => 'Tarik Data Sync', 'module' => 'sync'],
            ['name' => 'sync.manage', 'display_name' => 'Kelola Sinkronisasi & Konflik', 'module' => 'sync'],
            ['name' => 'users.manage', 'display_name' => 'Kelola Pengguna', 'module' => 'settings'],
            ['name' => 'settings.manage', 'display_name' => 'Kelola Pengaturan', 'module' => 'settings'],
            ['name' => 'backup.manage', 'display_name' => 'Kelola Backup & Restore', 'module' => 'settings'],
        ];

        $permissionModels = [];
        foreach ($permissions as $perm) {
            $permissionModels[$perm['name']] = Permission::firstOrCreate(
                ['name' => $perm['name']],
                [
                    'display_name' => $perm['display_name'],
                    'module' => $perm['module'],
                ]
            );
        }

        // Roles
        $roles = [
            'superadmin' => [
                'display_name' => 'Super Admin',
                'description' => 'Akses penuh ke seluruh sistem dan konfigurasi lokal.',
                'permissions' => array_keys($permissions),
            ],
            'admin_desa' => [
                'display_name' => 'Admin Desa',
                'description' => 'Administrator tingkat desa untuk seluruh modul operasional.',
                'permissions' => array_keys($permissions),
            ],
            'operator' => [
                'display_name' => 'Operator',
                'description' => 'Operator input kependudukan, surat, dan layanan.',
                'permissions' => [
                    'citizens.view', 'citizens.create', 'citizens.update',
                    'families.view', 'families.manage',
                    'attendance.view', 'attendance.create',
                    'letters.view', 'letters.create', 'letters.print',
                    'services.manage', 'reports.view', 'sync.push', 'sync.pull'
                ],
            ],
            'pegawai' => [
                'display_name' => 'Pegawai',
                'description' => 'Pegawai untuk absensi dan pengajuan surat.',
                'permissions' => ['attendance.create', 'attendance.view', 'letters.view', 'letters.create'],
            ],
            'keuangan' => [
                'display_name' => 'Keuangan',
                'description' => 'Staf / Kaur keuangan untuk anggaran dan kas APBDes.',
                'permissions' => ['finance.view', 'finance.create', 'reports.view', 'sync.push', 'sync.pull'],
            ],
            'kepala_desa' => [
                'display_name' => 'Kepala Desa',
                'description' => 'Pimpinan desa untuk verifikasi, approval surat dan keuangan.',
                'permissions' => [
                    'citizens.view', 'families.view', 'employees.view', 'attendance.view', 'attendance.verify',
                    'letters.view', 'letters.approve', 'letters.print',
                    'finance.view', 'finance.approve', 'assets.view', 'aid.view', 'reports.view'
                ],
            ],
        ];

        foreach ($roles as $roleName => $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                    'is_system' => true,
                ]
            );

            $permIds = [];
            foreach ($roleData['permissions'] as $pName) {
                if (isset($permissionModels[$pName])) {
                    $permIds[] = $permissionModels[$pName]->id;
                }
            }
            $role->permissions()->sync($permIds);
        }
    }
}
