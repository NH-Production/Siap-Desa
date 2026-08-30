<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Citizen;
use App\Models\Device;
use App\Models\Employee;
use App\Models\Family;
use App\Models\FamilyMember;
use App\Models\FinanceAccount;
use App\Models\FinanceBudget;
use App\Models\FinanceTransaction;
use App\Models\Letter;
use App\Models\LetterType;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Village;
use App\Services\QrCode\QrService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SampleVillageSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Profil Resmi Desa Sindangresmi, Kec. Takokak, Kab. Cianjur
        $village = Village::updateOrCreate(
            ['code' => '3203162002'],
            [
                'name' => 'Sindangresmi',
                'district' => 'Takokak',
                'regency' => 'Kabupaten Cianjur',
                'province' => 'Jawa Barat',
                'postal_code' => '43265',
                'address' => 'Jl. Raya Takokak No. 12, Desa Sindangresmi, Kec. Takokak, Kab. Cianjur',
                'phone' => '0812-3456-7890',
                'email' => 'pemdes@sindangresmi-takokak.desa.id',
                'website' => 'https://www.sindangresmi-takokak.desa.id',
                'head_name' => 'IMAS, S.IP., NL.P',
                'secretary_name' => 'AGUNG WAHYUNI, S.AP',
                'letter_number_format' => '{KODE}/{NO_URUT}/SR-TKK/{BULAN_ROMAWI}/{TAHUN}',
                'settings' => [
                    'head_title' => 'Kepala Desa',
                    'motto' => 'Sindangresmi Maju, Mandiri, dan Berkarakter'
                ]
            ]
        );

        SystemSetting::set('village_name', 'Sindangresmi');
        SystemSetting::set('village_code', '3203162002');
        SystemSetting::set('is_configured', '1');
        SystemSetting::set('saas_license_key', 'SIAP-SAAS-3203-1620-02-2026');
        SystemSetting::set('saas_status', 'ACTIVE_ENTERPRISE');

        // 2. Perangkat Terdaftar
        Device::updateOrCreate(
            ['device_code' => 'PC-ADMIN-01'],
            [
                'village_id' => $village->uuid,
                'name' => 'Komputer Utama Pelayanan Desa Sindangresmi',
                'status' => 'ACTIVE',
                'app_version' => '1.0.0',
                'schema_version' => 1,
                'sync_protocol_version' => 1,
            ]
        );

        // 3. Aparatur & Pegawai Desa Sindangresmi
        $officials = [
            ['name' => 'IMAS, S.IP., NL.P', 'position' => 'Kepala Desa', 'status' => 'KADES', 'dept' => 'Pimpinan Desa'],
            ['name' => 'AGUNG WAHYUNI, S.AP', 'position' => 'Sekretaris Desa', 'status' => 'SEKDES', 'dept' => 'Sekretariat'],
            ['name' => 'PIRMANSYAH, S.Pd', 'position' => 'Kaur Keuangan', 'status' => 'PERANGKAT_DESA', 'dept' => 'Urusan Keuangan'],
            ['name' => 'YOSEP SUHERMAN, S.Pi', 'position' => 'Kaur Perencanaan', 'status' => 'PERANGKAT_DESA', 'dept' => 'Urusan Perencanaan'],
            ['name' => 'TEDI GUNAWAN', 'position' => 'Kaur TU dan Umum', 'status' => 'PERANGKAT_DESA', 'dept' => 'Urusan Tata Usaha'],
            ['name' => 'IIS ROKAYAH', 'position' => 'Kasi Pemerintahan', 'status' => 'PERANGKAT_DESA', 'dept' => 'Seksi Pemerintahan'],
            ['name' => 'HIDAYAT', 'position' => 'Kasi Kesra', 'status' => 'PERANGKAT_DESA', 'dept' => 'Seksi Kesejahteraan'],
            ['name' => 'IWAN SUNARDI', 'position' => 'Kasi Pelayanan', 'status' => 'PERANGKAT_DESA', 'dept' => 'Seksi Pelayanan'],
            ['name' => 'UJANG SUPRIATMAN', 'position' => 'Kepala Dusun Cibeber', 'status' => 'PERANGKAT_DESA', 'dept' => 'Kewilayahan'],
            ['name' => 'SAMSUL BAHRI', 'position' => 'Kepala Dusun Cigombong', 'status' => 'PERANGKAT_DESA', 'dept' => 'Kewilayahan'],
        ];

        foreach ($officials as $idx => $off) {
            $token = 'QR-EMP-' . strtoupper(Str::slug($off['name'], '')) . '-' . ($idx + 100);
            Employee::updateOrCreate(
                ['name' => $off['name']],
                [
                    'village_id' => $village->uuid,
                    'nip' => '320316' . str_pad($idx + 1, 6, '0', STR_PAD_LEFT),
                    'position' => $off['position'],
                    'department' => $off['dept'],
                    'employment_status' => $off['status'],
                    'qr_token' => $token,
                    'join_date' => '2021-01-01',
                    'is_active' => true,
                ]
            );
        }

        // 4. Sample Penduduk & KK Desa Sindangresmi
        $c1 = Citizen::updateOrCreate(
            ['nik' => '3203160101900001'],
            [
                'village_id' => $village->uuid,
                'no_kk' => '3203160101900001',
                'name' => 'DADANG SURYANA',
                'gender' => 'LAKI_LAKI',
                'birth_place' => 'Cianjur',
                'birth_date' => '1990-01-01',
                'religion' => 'ISLAM',
                'marital_status' => 'KAWIN',
                'occupation' => 'PETANI/PEKEBUN',
                'education' => 'SLTA/SEDERAJAT',
                'address' => 'Kp. Cibeber RT 001 RW 002, Desa Sindangresmi',
                'rt' => '001',
                'rw' => '002',
                'status' => 'TETAP',
            ]
        );

        $c2 = Citizen::updateOrCreate(
            ['nik' => '3203164102920002'],
            [
                'village_id' => $village->uuid,
                'no_kk' => '3203160101900001',
                'name' => 'SITI MARYAM',
                'gender' => 'PEREMPUAN',
                'birth_place' => 'Cianjur',
                'birth_date' => '1992-02-15',
                'religion' => 'ISLAM',
                'marital_status' => 'KAWIN',
                'occupation' => 'MENGURUS RUMAH TANGGA',
                'education' => 'SLTP/SEDERAJAT',
                'address' => 'Kp. Cibeber RT 001 RW 002, Desa Sindangresmi',
                'rt' => '001',
                'rw' => '002',
                'status' => 'TETAP',
            ]
        );

        $fam = Family::updateOrCreate(
            ['no_kk' => '3203160101900001'],
            [
                'village_id' => $village->uuid,
                'head_name' => 'DADANG SURYANA',
                'head_nik' => '3203160101900001',
                'address' => 'Kp. Cibeber RT 001 RW 002, Desa Sindangresmi',
                'rt' => '001',
                'rw' => '002',
                'issue_date' => '2015-06-10',
            ]
        );

        FamilyMember::updateOrCreate(
            ['family_id' => $fam->id, 'citizen_id' => $c1->id],
            ['relation_status' => 'KEPALA_KELUARGA']
        );
        FamilyMember::updateOrCreate(
            ['family_id' => $fam->id, 'citizen_id' => $c2->id],
            ['relation_status' => 'ISTRI']
        );

        // 5. Sample Anggaran Kas APBDes Sindangresmi
        $accIncome = FinanceAccount::where('code', '4.1.1')->first();
        $accExpense = FinanceAccount::where('code', '5.1.1')->first();

        if ($accIncome) {
            FinanceBudget::updateOrCreate(
                ['account_id' => $accIncome->id, 'fiscal_year' => 2026],
                ['budget_amount' => 850000000, 'realized_amount' => 350000000]
            );
        }

        if ($accExpense) {
            FinanceBudget::updateOrCreate(
                ['account_id' => $accExpense->id, 'fiscal_year' => 2026],
                ['budget_amount' => 450000000, 'realized_amount' => 175000000]
            );
        }
    }
}
