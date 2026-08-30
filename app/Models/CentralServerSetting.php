<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CentralServerSetting extends Model
{
    protected $table = 'central_server_settings';
    protected $guarded = ['id'];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, $value, ?string $description = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => (string)$value, 'description' => $description]
        );
    }
}
