<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');                      // Nama area (misalnya JABODETABEK)
            $table->text('description')->nullable();     // Deskripsi area
            $table->boolean('is_active')->default(true); // Status aktif/nonaktif
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_areas');
    }
};
