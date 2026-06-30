<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label',
        'description',
        'is_encrypted',
        'updated_by',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function get($key, $default = null)
    {
        // Ideally we fetch from the SettingsService which caches, but this is a fallback.
        $setting = self::where('key', $key)->first();
        if (!$setting) return $default;

        $value = $setting->is_encrypted ? \Illuminate\Support\Facades\Crypt::decryptString($setting->value) : $setting->value;

        if ($setting->type === 'boolean') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
        if ($setting->type === 'json') {
            return json_decode($value, true);
        }
        return $value;
    }

    public static function set($key, $value)
    {
        $setting = self::where('key', $key)->first();
        if ($setting) {
            $setting->update(['value' => $value]);
        }
    }

    public static function group($group)
    {
        return self::where('group', $group)->get();
    }
}
