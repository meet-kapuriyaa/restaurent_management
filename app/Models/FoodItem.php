<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodItem extends Model
{
    protected $fillable = [
        'name',
        'price',
        'description',
        'status',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
