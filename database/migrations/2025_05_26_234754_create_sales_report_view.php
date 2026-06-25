<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $ordersHasDeletedAt = DB::getSchemaBuilder()->hasColumn('orders', 'deleted_at');
        $customHasDeletedAt = DB::getSchemaBuilder()->hasColumn('custom_product_requests', 'deleted_at');

        $ordersWhere = $ordersHasDeletedAt ? 'WHERE o.deleted_at IS NULL' : '';
        $customWhere = $customHasDeletedAt ? 'WHERE cpr.deleted_at IS NULL' : '';

        DB::statement("DROP VIEW IF EXISTS sales_report_view");

        DB::statement("
            CREATE VIEW sales_report_view AS
            SELECT
                CONCAT('ORDER_', o.id) COLLATE utf8mb4_unicode_ci as unique_id,
                o.id as original_id,
                o.payment_code COLLATE utf8mb4_unicode_ci as payment_code,
                o.full_name COLLATE utf8mb4_unicode_ci as customer_name,
                o.status COLLATE utf8mb4_unicode_ci as status,
                o.total as total_amount,
                o.order_created_at,
                o.payment_date,
                o.updated_at,
                'REGULER' COLLATE utf8mb4_unicode_ci as order_type,
                NULL as user_id,
                NULL as title,
                NULL as description,
                NULL as quoted_price,
                NULL as dp_payment_date,
                NULL as full_payment_date,
                NULL as work_completed_at,
                o.created_at,
                CASE
                    WHEN o.status IN ('PEMBAYARAN BERHASIL', 'DIKEMAS', 'SIAP DIKIRIM', 'DIKIRIM', 'SELESAI')
                    THEN 'LUNAS'
                    ELSE 'BELUM DIBAYAR'
                END COLLATE utf8mb4_unicode_ci as payment_status,
                DATE_FORMAT(o.order_created_at, '%M %Y') COLLATE utf8mb4_unicode_ci as periode,
                CONCAT('ORDER-', LPAD(o.id, 6, '0')) COLLATE utf8mb4_unicode_ci as order_number
            FROM orders o
            {$ordersWhere}

            UNION ALL

            SELECT
                CONCAT('CUSTOM_', cpr.id) COLLATE utf8mb4_unicode_ci as unique_id,
                cpr.id as original_id,
                NULL as payment_code,
                u.name COLLATE utf8mb4_unicode_ci as customer_name,
                cpr.status COLLATE utf8mb4_unicode_ci as status,
                cpr.quoted_price as total_amount,
                cpr.created_at as order_created_at,
                COALESCE(cpr.full_payment_date, cpr.dp_payment_date) as payment_date,
                cpr.updated_at,
                'CUSTOM' COLLATE utf8mb4_unicode_ci as order_type,
                cpr.user_id,
                cpr.title COLLATE utf8mb4_unicode_ci as title,
                cpr.description COLLATE utf8mb4_unicode_ci as description,
                cpr.quoted_price,
                cpr.dp_payment_date,
                cpr.full_payment_date,
                cpr.work_completed_at,
                cpr.created_at,
                CASE
                    WHEN cpr.dp_payment_date IS NOT NULL AND cpr.full_payment_date IS NOT NULL THEN 'LUNAS'
                    WHEN cpr.dp_payment_date IS NOT NULL THEN 'DP DIBAYAR'
                    WHEN cpr.status IN ('MENUNGGU_DP', 'MENUNGGU_PELUNASAN') THEN 'BELUM DIBAYAR'
                    ELSE 'N/A'
                END COLLATE utf8mb4_unicode_ci as payment_status,
                DATE_FORMAT(cpr.created_at, '%M %Y') COLLATE utf8mb4_unicode_ci as periode,
                CONCAT('CUSTOM-', LPAD(cpr.id, 6, '0')) COLLATE utf8mb4_unicode_ci as order_number
            FROM custom_product_requests cpr
            LEFT JOIN users u ON cpr.user_id = u.id
            {$customWhere}
        ");
    }

    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS sales_report_view");
    }
};
