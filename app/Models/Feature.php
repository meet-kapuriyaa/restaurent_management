<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $fillable = [
        'key',
        'display_name',
        'description',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    /**
     * Check if a feature is active.
     */
    public static function isActive(string $key): bool
    {
        $feature = self::where('key', $key)->first();
        return $feature ? (bool) $feature->is_enabled : true;
    }
}
