<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ensure 'role' exists in 'users'
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('waiter');
            });
        }

        // 2. Ensure missing columns exist in 'orders'
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'table_id')) {
                $table->unsignedBigInteger('table_id')->nullable()->index();
            }
            if (!Schema::hasColumn('orders', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->index();
            }
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->default('cash');
            }
            if (!Schema::hasColumn('orders', 'special_instructions')) {
                $table->text('special_instructions')->nullable();
            }
            if (!Schema::hasColumn('orders', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('orders', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('orders', 'tip_amount')) {
                $table->decimal('tip_amount', 10, 2)->default(0.00);
            }
        });

        // 3. Ensure missing columns exist in 'order_items'
        if (!Schema::hasColumn('order_items', 'modifiers')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->string('modifiers')->nullable();
            });
        }
    }

    public function down(): void
    {
        // No rollback needed as this is a production recovery script
    }
};
