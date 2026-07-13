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
        'daily_no',
    ];

    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->daily_no)) {
                $date = $order->created_at ? \Carbon\Carbon::parse($order->created_at)->toDateString() : now()->toDateString();
                $maxDailyNo = static::whereDate('created_at', $date)->max('daily_no');
                $order->daily_no = ($maxDailyNo ?? 0) + 1;
            }
        });

        static::deleting(function ($order) {
            $order->orderItems()->delete();
        });
    }

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
