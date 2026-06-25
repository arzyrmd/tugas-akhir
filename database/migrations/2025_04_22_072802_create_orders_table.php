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
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->text('address');
            $table->foreignId('province_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->string('postal_code');
            $table->text('notes')->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 12, 2);
            $table->decimal('total', 12, 2);
            $table->string('status');
            $table->string('payment_method')->nullable();
            $table->string('payment_code')->nullable(); // Untuk menyimpan kode payment dari Midtrans
            $table->timestamp('order_created_at')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->timestamp('packing_date')->nullable();
            $table->timestamp('delivery_date')->nullable();
            $table->timestamp('completed_date')->nullable();
            $table->timestamps();
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
