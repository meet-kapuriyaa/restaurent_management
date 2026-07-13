<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('daily_no')->default(1)->after('id');
        });

        // Retroactively populate daily_no chronologically
        $orders = \App\Models\Order::orderBy('created_at', 'asc')->get();
        $dailyCounts = [];
        foreach ($orders as $order) {
            $date = $order->created_at->toDateString();
            if (!isset($dailyCounts[$date])) {
                $dailyCounts[$date] = 0;
            }
            $dailyCounts[$date]++;
            $order->daily_no = $dailyCounts[$date];
            $order->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('daily_no');
        });
    }
};
