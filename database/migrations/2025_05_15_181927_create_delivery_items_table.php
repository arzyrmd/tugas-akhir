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
       Schema::create('delivery_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('delivery_batch_id')->constrained()->onDelete('cascade');
        $table->morphs('deliverable'); // Polymorphic relation untuk Orders dan CustomProductRequests
        $table->enum('status', ['BELUM_DIKIRIM', 'DIKIRIM', 'DITERIMA'])->default('BELUM_DIKIRIM');
        $table->timestamp('delivered_at')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_items');
    }
};
