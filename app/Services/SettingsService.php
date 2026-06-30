<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class SettingsService
{
    private const CACHE_KEY = 'app_settings';

    /**
     * Get all settings from cache or database.
     */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $settings = Setting::all();
            $mapped = [];
            foreach ($settings as $setting) {
                $value = $setting->is_encrypted && $setting->value
                    ? Crypt::decryptString($setting->value)
                    : $setting->value;

                if ($setting->type === 'boolean') {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                } elseif ($setting->type === 'json') {
                    $value = json_decode($value, true);
                }

                $mapped[$setting->key] = $value;
            }
            return $mapped;
        });
    }

    /**
     * Get a specific setting value.
     */
    public function get(string $key, $default = null)
    {
        $settings = $this->all();
        return $settings[$key] ?? $default;
    }

    /**
     * Update a specific setting.
     */
    public function set(string $key, $value, ?int $userId = null): void
    {
        $setting = Setting::where('key', $key)->first();
        if ($setting) {
            $saveValue = $value;
            if ($setting->is_encrypted && $value) {
                $saveValue = Crypt::encryptString($value);
            }
            if ($setting->type === 'boolean') {
                $saveValue = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
            } elseif ($setting->type === 'json' && is_array($value)) {
                $saveValue = json_encode($value);
            }

            $setting->update([
                'value' => $saveValue,
                'updated_by' => $userId,
            ]);

            $this->flushCache();
        }
    }

    /**
     * Clear the settings cache.
     */
    public function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
