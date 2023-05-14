<?php

namespace App\Services;

use App\Models\Settings;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    public static function getSetting($key, $default = null)
    {
        return Cache::rememberForever('setting_' . $key, function () use ($key, $default) {
            $setting = Settings::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }
    public static function setSetting($key, $value)
    {
        Settings::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::put('setting_' . $key, $value);
    }
}
