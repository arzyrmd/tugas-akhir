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
        // Tabel utama untuk request produk custom
        Schema::create('custom_product_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->text('specifications')->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->date('desired_deadline')->nullable();
            $table->enum('status', [
                'MENUNGGU_REVIEW',
                'PENAWARAN_DIBERIKAN',
                'PENAWARAN_DITOLAK',
                'MENUNGGU_DP',
                'DALAM_PENGERJAAN',
                'MENUNGGU_PELUNASAN',
                'SIAP_DIKIRIM',
                'DIKIRIM',
                'SELESAI',
                'DIBATALKAN'
            ])->default('MENUNGGU_REVIEW');

            // Detail penawaran (diisi oleh admin)
            $table->decimal('quoted_price', 12, 2)->nullable();
            $table->decimal('down_payment', 12, 2)->nullable();
            $table->decimal('remaining_payment', 12, 2)->nullable();

            $table->date('estimated_completion')->nullable();
            $table->text('admin_notes')->nullable();

            // Informasi pembayaran
            $table->string('dp_payment_code')->nullable();
            $table->timestamp('dp_payment_date')->nullable();
            $table->string('full_payment_code')->nullable();
            $table->timestamp('full_payment_date')->nullable();

            // Informasi pengerjaan
            $table->date('work_started_at')->nullable();
            $table->date('work_completed_at')->nullable();
            $table->date('shipping_date')->nullable();
            $table->date('delivery_date')->nullable();

            $table->timestamps();
        });

        // Tabel untuk foto referensi yang diupload pelanggan
        Schema::create('custom_product_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_product_request_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Tabel untuk foto progress pengerjaan yang diupload admin
        Schema::create('custom_product_progresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_product_request_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->text('description');
            $table->timestamps();
        });

        // Tabel untuk informasi pengiriman
        Schema::create('custom_product_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_product_request_id')->constrained()->onDelete('cascade');
            $table->string('full_name');
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
            $table->string('payment_code')->nullable();
            $table->string('tracking_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Urutan drop table penting: hapus dulu tabel yang memiliki foreign key
        Schema::dropIfExists('custom_product_progresses');
        Schema::dropIfExists('custom_product_references');
        Schema::dropIfExists('custom_product_shipments');
        Schema::dropIfExists('custom_product_requests');
    }
};
