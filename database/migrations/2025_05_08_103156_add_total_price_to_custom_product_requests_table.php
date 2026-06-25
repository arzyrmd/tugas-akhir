<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalPriceToCustomProductRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_product_requests', function (Blueprint $table) {
            // Tambahkan kolom total_price setelah remaining_payment
            // Gunakan tipe data yang sama dengan quoted_price (decimal 12,2)
            $table->decimal('total_price', 12, 2)->nullable()->after('remaining_payment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_product_requests', function (Blueprint $table) {
            $table->dropColumn('total_price');
        });
    }
}
