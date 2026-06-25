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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->enum('type', ['in', 'out'])->comment('in: stok masuk, out: stok keluar');
            $table->integer('quantity');
            $table->integer('before_stock')->comment('stok sebelum perubahan');
            $table->integer('after_stock')->comment('stok setelah perubahan');
            $table->string('reference_type')->nullable()->comment('model reference seperti Order, StockAdjustment, dll');
            $table->unsignedBigInteger('reference_id')->nullable()->comment('ID dari model reference');
            $table->text('notes')->nullable()->comment('catatan tambahan');
            $table->timestamps();

            // Index untuk mempercepat query
            $table->index(['product_id', 'type']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
