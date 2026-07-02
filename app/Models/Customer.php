<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'phone_number',
        'name',
        'total_spend',
        'total_visits',
    ];

    public static function recordOrderPayment(Order $order)
    {
        if (empty($order->contact_number) || empty($order->customer_name)) {
            return;
        }

        // Find or create customer
        $customer = self::firstOrCreate(
            ['phone_number' => $order->contact_number],
            ['name' => $order->customer_name]
        );

        $customer->total_visits += 1;
        $customer->total_spend += $order->total_amount;
        $customer->save();
    }
}
