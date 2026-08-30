<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Role;
use App\Models\User;
use App\Models\Village;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FirstRunController extends Controller
{
    public function index()
    {
        if (Village::count() > 0 && User::count() > 0) {
            return redirect()->route('dashboard');
        }
        return view('setup.wizard');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'village_name' => 'required|string|max:150',
            'village_code' => 'required|string|max:30',
            'district' => 'required|string|max:100',
            'regency' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'head_name' => 'required|string|max:150',
            'admin_name' => 'required|string|max:150',
            'admin_username' => 'required|string|min:4|max:50',
            'admin_password' => 'required|string|min:6|confirmed',
            'device_name' => 'required|string|max:100',
        ]);

        // Seed default master roles & data
        $seeder1 = new RolePermissionSeeder();
        $seeder1->run();
        $seeder2 = new MasterDataSeeder();
        $seeder2->run();

        // Create Village
        $village = Village::create([
            'code' => $validated['village_code'],
            'name' => $validated['village_name'],
            'district' => $validated['district'],
            'regency' => $validated['regency'],
            'province' => $validated['province'],
            'head_name' => $validated['head_name'],
            'letter_number_format' => '{KODE}/{NO}/DS/{BULAN_ROMAWI}/{TAHUN}',
        ]);

        // Create Device
        $device = Device::create([
            'village_id' => $village->uuid,
            'name' => $validated['device_name'],
            'device_code' => 'DEV-' . strtoupper(Str::random(8)),
            'status' => 'ACTIVE',
            'registered_at' => now(),
            'last_seen_at' => now(),
        ]);

        // Create Admin User
        $superAdminRole = Role::where('name', 'superadmin')->first();
        $user = User::create([
            'village_id' => $village->uuid,
            'name' => $validated['admin_name'],
            'username' => $validated['admin_username'],
            'email' => 'admin@' . Str::slug($validated['village_name']) . '.desa.id',
            'password' => Hash::make($validated['admin_password']),
            'status' => 'ACTIVE',
        ]);

        if ($superAdminRole) {
            $user->roles()->sync([$superAdminRole->id]);
        }

        return redirect()->route('login')->with('success', 'Konfigurasi awal desa berhasil! Silakan masuk dengan akun Administrator yang baru dibuat.');
    }
}
