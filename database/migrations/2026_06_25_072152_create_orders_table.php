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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->nullable()->constrained('tables')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name');
            $table->string('contact_number');
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->string('status')->default('pending'); // pending, preparing, ready, completed
            $table->string('payment_status')->default('unpaid'); // unpaid, paid
            $table->string('payment_method')->default('cash'); // cash, card, upi
            $table->text('special_instructions')->nullable();
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('tip_amount', 10, 2)->default(0.00);
            $table->timestamps();

            // Indexes for query performance
            $table->index('status');
            $table->index('payment_status');
            $table->index('contact_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
