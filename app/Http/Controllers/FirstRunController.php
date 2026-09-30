<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Role;
use App\Models\User;
use App\Models\Village;
use App\Services\DeviceIdentityService;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FirstRunController extends Controller
{
    public function index()
    {
        if (Village::count() > 0 && User::count() > 0) {
            return redirect()->route('dashboard');
        }

        return view('setup.wizard');
    }

    public function store(Request $request, DeviceIdentityService $identity)
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

        (new RolePermissionSeeder())->run();
        (new MasterDataSeeder())->run();

        $village = Village::create([
            'code' => $validated['village_code'],
            'name' => $validated['village_name'],
            'district' => $validated['district'],
            'regency' => $validated['regency'],
            'province' => $validated['province'],
            'head_name' => $validated['head_name'],
            'letter_number_format' => '{KODE}/{NO}/DS/{BULAN_ROMAWI}/{TAHUN}',
        ]);

        Device::updateOrCreate(
            ['uuid' => $identity->uuid()],
            [
                'village_uuid' => $village->uuid,
                'name' => $validated['device_name'],
                'device_code' => 'DEV-' . strtoupper(substr(str_replace('-', '', $identity->uuid()), 0, 12)),
                'status' => 'active',
                'registered_at' => now(),
                'last_seen_at' => now(),
                'app_version' => config('siapdesa.version', '1.0.0'),
                'schema_version' => (int) config('siapdesa.schema_version', 1),
                'sync_protocol_version' => (int) config('siapdesa.sync_protocol_version', 1),
            ]
        );

        $superAdminRole = Role::where('name', 'superadmin')->firstOrFail();

        $user = User::create([
            'village_id' => $village->uuid,
            'name' => $validated['admin_name'],
            'username' => $validated['admin_username'],
            'email' => 'admin@' . str_replace(' ', '-', strtolower($validated['village_name'])) . '.local',
            'password_hash' => Hash::make($validated['admin_password']),
            'status' => 'active'
        ]);

        $user->roles()->sync([$superAdminRole->id]);

        return redirect()->route('login')->with(
            'success',
            'Konfigurasi awal desa berhasil. Silakan masuk dengan akun Administrator yang baru dibuat.'
        );
    }
}
