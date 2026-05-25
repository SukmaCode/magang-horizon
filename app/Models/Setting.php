<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label'
    ];

    /**
     * Get a setting value by key, with caching.
     */
    public static function getValue(string $key, $default = null)
    {
        $settings = Cache::rememberForever('app_settings', function () {
            return self::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Clear the settings cache.
     */
    public static function clearCache()
    {
        Cache::forget('app_settings');
    }

    /**
     * The "booted" method of the model.
     * Automatically clear cache on save/delete.
     */
    protected static function booted()
    {
        static::saved(function ($setting) {
            self::clearCache();
        });

        static::deleted(function ($setting) {
            self::clearCache();
        });
    }
}
