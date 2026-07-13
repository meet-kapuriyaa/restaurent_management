<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'title',
        'url',
        'parent_id',
        'icon_class',
        'is_visible',
        'order_weight',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'parent_id' => 'integer',
        'order_weight' => 'integer',
    ];

    protected static function booted()
    {
        static::deleting(function ($module) {
            $module->children()->delete();
        });
    }

    /**
     * Parent module relationship.
     */
    public function parent()
    {
        return $this->belongsTo(Module::class, 'parent_id');
    }

    /**
     * Child modules relationship.
     */
    public function children()
    {
        return $this->hasMany(Module::class, 'parent_id')->orderBy('order_weight');
    }

    /**
     * Scope visible modules only.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }
}
