<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_name',
        'contact_number',
        'total_amount',
        'status',
        'payment_status',
        'table_id',
        'payment_method',
        'special_instructions',
        'tax_amount',
        'discount_amount',
        'tip_amount',
        'user_id',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
