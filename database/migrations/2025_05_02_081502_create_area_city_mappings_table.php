<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area_city_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_area_id')->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Memastikan mapping kota ke area bersifat unik
            $table->unique(['delivery_area_id', 'city_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_city_mappings');
    }
};
