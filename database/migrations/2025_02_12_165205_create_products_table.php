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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 12, 2);
            $table->string('image');
            $table->json('gallery')->nullable();

            $table->boolean('is_active')->default(true);
            $table->integer('stock')->default(0);
            $table->integer('weight')->default(1000)->comment('dalam gram');
            $table->integer('length')->nullable()->comment('dalam cm');
            $table->integer('width')->nullable()->comment('dalam cm');
            $table->integer('height')->nullable()->comment('dalam cm');
            $table->string('material')->nullable()->comment('Bahan material produk');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
