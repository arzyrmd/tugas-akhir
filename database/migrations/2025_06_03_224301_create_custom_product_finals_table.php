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
        Schema::create('custom_product_finals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_product_request_id')
                  ->constrained('custom_product_requests')
                  ->onDelete('cascade');
            $table->string('image_path');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Index untuk performance
            $table->index('custom_product_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_product_finals');
    }
};
