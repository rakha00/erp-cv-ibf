<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingHelper
{
    public static function get(string $key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        switch ($setting->type) {
            case 'json':
                return $setting->value;
            case 'array':
                return collect($setting->value)->pluck('item')->toArray();
            case 'string':
            default:
                return $setting->value;
        }
    }
}
