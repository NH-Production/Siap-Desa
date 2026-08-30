<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use App\Models\FinanceAccount;
use App\Models\LetterType;
use App\Models\Service;
use App\Models\SystemVersion;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // System Version
        SystemVersion::firstOrCreate(
            ['app_version' => '1.0.0'],
            [
                'schema_version' => 1,
                'sync_protocol_version' => 1,
                'remarks' => 'Baseline Initial Release 1.0.0',
            ]
        );

        // Asset Categories
        $categories = [
            ['code' => 'KDG-01', 'name' => 'Tanah Kas Desa', 'description' => 'Tanah kas desa, bengkok, dan pekarangan'],
            ['code' => 'KDG-02', 'name' => 'Peralatan & Mesin', 'description' => 'Komputer, printer, genset, kendaraan dinas'],
            ['code' => 'KDG-03', 'name' => 'Gedung & Bangunan', 'description' => 'Kantor desa, balai pertemuan, poskesdes'],
            ['code' => 'KDG-04', 'name' => 'Jalan, Irigasi & Jaringan', 'description' => 'Jalan paving, saluran drainase, pipa air'],
            ['code' => 'KDG-05', 'name' => 'Aset Tetap Lainnya', 'description' => 'Buku perpustakaan desa, barang seni/budaya'],
        ];
        foreach ($categories as $cat) {
            AssetCategory::firstOrCreate(['code' => $cat['code']], $cat);
        }

        // Letter Types
        $letterTypes = [
            [
                'code' => 'SKTM',
                'name' => 'Surat Keterangan Tidak Mampu',
                'template_view' => 'letters.templates.sktm',
                'number_format' => '400/{NO}/SKTM/{BULAN_ROMAWI}/{TAHUN}',
                'fields_schema' => [
                    ['name' => 'keperluan', 'label' => 'Keperluan Surat', 'type' => 'text', 'required' => true],
                    ['name' => 'penghasilan_rata_rata', 'label' => 'Penghasilan Rata-rata/Bulan', 'type' => 'number', 'required' => false],
                ]
            ],
            [
                'code' => 'SKDOM',
                'name' => 'Surat Keterangan Domisili',
                'template_view' => 'letters.templates.skdom',
                'number_format' => '470/{NO}/SKD/{BULAN_ROMAWI}/{TAHUN}',
                'fields_schema' => [
                    ['name' => 'keperluan', 'label' => 'Keperluan Surat', 'type' => 'text', 'required' => true],
                    ['name' => 'sejak_tahun', 'label' => 'Tinggal Sejak Tahun', 'type' => 'text', 'required' => true],
                ]
            ],
            [
                'code' => 'SKU',
                'name' => 'Surat Keterangan Usaha',
                'template_view' => 'letters.templates.sku',
                'number_format' => '500/{NO}/SKU/{BULAN_ROMAWI}/{TAHUN}',
                'fields_schema' => [
                    ['name' => 'nama_usaha', 'label' => 'Nama Usaha', 'type' => 'text', 'required' => true],
                    ['name' => 'jenis_usaha', 'label' => 'Jenis / Bidang Usaha', 'type' => 'text', 'required' => true],
                    ['name' => 'alamat_usaha', 'label' => 'Alamat Lokasi Usaha', 'type' => 'text', 'required' => true],
                    ['name' => 'lama_usaha', 'label' => 'Berjalan Sejak', 'type' => 'text', 'required' => true],
                ]
            ],
            [
                'code' => 'SKCK',
                'name' => 'Surat Pengantar SKCK',
                'template_view' => 'letters.templates.skck',
                'number_format' => '300/{NO}/SKCK/{BULAN_ROMAWI}/{TAHUN}',
                'fields_schema' => [
                    ['name' => 'keperluan', 'label' => 'Keperluan Pembuatan SKCK', 'type' => 'text', 'required' => true],
                ]
            ],
            [
                'code' => 'SKKEMATIAN',
                'name' => 'Surat Keterangan Kematian',
                'template_view' => 'letters.templates.kematian',
                'number_format' => '472/{NO}/SKM/{BULAN_ROMAWI}/{TAHUN}',
                'fields_schema' => [
                    ['name' => 'hari_meninggal', 'label' => 'Hari Meninggal', 'type' => 'text', 'required' => true],
                    ['name' => 'tanggal_meninggal', 'label' => 'Tanggal Meninggal', 'type' => 'date', 'required' => true],
                    ['name' => 'tempat_meninggal', 'label' => 'Tempat Meninggal', 'type' => 'text', 'required' => true],
                    ['name' => 'penyebab', 'label' => 'Penyebab Kematian', 'type' => 'text', 'required' => true],
                ]
            ],
            [
                'code' => 'SKKELAHIRAN',
                'name' => 'Surat Keterangan Kelahiran',
                'template_view' => 'letters.templates.kelahiran',
                'number_format' => '472/{NO}/SKL/{BULAN_ROMAWI}/{TAHUN}',
                'fields_schema' => [
                    ['name' => 'nama_anak', 'label' => 'Nama Lengkap Anak', 'type' => 'text', 'required' => true],
                    ['name' => 'jenis_kelamin_anak', 'label' => 'Jenis Kelamin Anak', 'type' => 'select', 'required' => true],
                    ['name' => 'tanggal_lahir_anak', 'label' => 'Tanggal Lahir', 'type' => 'date', 'required' => true],
                    ['name' => 'tempat_lahir_anak', 'label' => 'Tempat Lahir', 'type' => 'text', 'required' => true],
                    ['name' => 'nama_ayah', 'label' => 'Nama Ayah', 'type' => 'text', 'required' => true],
                    ['name' => 'nama_ibu', 'label' => 'Nama Ibu', 'type' => 'text', 'required' => true],
                ]
            ],
        ];

        foreach ($letterTypes as $lt) {
            LetterType::firstOrCreate(['code' => $lt['code']], $lt);
        }

        // Finance Accounts (Standar APBDes)
        $accounts = [
            // Pendapatan
            ['code' => '4.1', 'name' => 'Pendapatan Asli Desa (PADes)', 'type' => 'PENDAPATAN'],
            ['code' => '4.2', 'name' => 'Pendapatan Transfer (Dana Desa, ADD, dll)', 'type' => 'PENDAPATAN'],
            ['code' => '4.3', 'name' => 'Pendapatan Lain-lain', 'type' => 'PENDAPATAN'],
            // Belanja
            ['code' => '5.1', 'name' => 'Bidang Penyelenggaraan Pemerintahan Desa', 'type' => 'BELANJA'],
            ['code' => '5.2', 'name' => 'Bidang Pelaksanaan Pembangunan Desa', 'type' => 'BELANJA'],
            ['code' => '5.3', 'name' => 'Bidang Pembinaan Kemasyarakatan Desa', 'type' => 'BELANJA'],
            ['code' => '5.4', 'name' => 'Bidang Pemberdayaan Masyarakat Desa', 'type' => 'BELANJA'],
            ['code' => '5.5', 'name' => 'Bidang Penanggulangan Bencana & Darurat', 'type' => 'BELANJA'],
            // Pembiayaan
            ['code' => '6.1', 'name' => 'Penerimaan Pembiayaan (SILPA)', 'type' => 'PEMBIAYAAN'],
            ['code' => '6.2', 'name' => 'Pengeluaran Pembiayaan (Penyertaan Modal BUMDes)', 'type' => 'PEMBIAYAAN'],
            // Kas
            ['code' => '1.1.1', 'name' => 'Kas Tunai Bendahara Desa', 'type' => 'KAS'],
            ['code' => '1.1.2', 'name' => 'Rekening Kas Desa (Bank)', 'type' => 'KAS'],
        ];

        foreach ($accounts as $acc) {
            FinanceAccount::firstOrCreate(['code' => $acc['code']], $acc);
        }

        // Public Services
        $services = [
            ['code' => 'LAY-01', 'name' => 'Pelayanan Surat Keterangan Domisili', 'processing_days' => 1, 'requirements' => ['Fotokopi KTP', 'Fotokopi KK', 'Surat Pengantar RT/RW']],
            ['code' => 'LAY-02', 'name' => 'Pelayanan Surat Keterangan Usaha (SKU)', 'processing_days' => 1, 'requirements' => ['Fotokopi KTP', 'Fotokopi KK', 'Foto Tempat Usaha', 'Surat Pengantar RT/RW']],
            ['code' => 'LAY-03', 'name' => 'Pelayanan Surat Keterangan Tidak Mampu (SKTM)', 'processing_days' => 1, 'requirements' => ['Fotokopi KTP', 'Fotokopi KK', 'Surat Pernyataan Tidak Mampu', 'Surat Pengantar RT/RW']],
            ['code' => 'LAY-04', 'name' => 'Pelayanan Pengantar SKCK', 'processing_days' => 1, 'requirements' => ['Fotokopi KTP', 'Fotokopi KK', 'Surat Pengantar RT/RW', 'Pas Foto 4x6']],
        ];
        foreach ($services as $srv) {
            Service::firstOrCreate(['code' => $srv['code']], $srv);
        }
    }
}
