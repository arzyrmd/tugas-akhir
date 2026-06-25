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
        Schema::create('delivery_batches', function (Blueprint $table) {
        $table->id();
        $table->date('scheduled_date');
        $table->foreignId('delivery_area_id')->constrained();
        $table->string('driver_name')->nullable();
        $table->enum('status', ['DIJADWALKAN', 'DALAM_PERJALANAN', 'SELESAI'])->default('DIJADWALKAN');
        $table->text('notes')->nullable();
        $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_batches');
    }
};
