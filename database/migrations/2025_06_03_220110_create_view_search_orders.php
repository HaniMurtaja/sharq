<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateViewSearchOrders extends Migration
{
    public function up()
    {
        DB::statement("
            CREATE OR REPLACE VIEW view_search_orders AS
            SELECT
                o.id,
                o.client_order_id,
                o.client_order_id_string,
                o.value,
                o.city,
                o.status,
                o.payment_type,
                o.customer_name,
                o.customer_phone,
                o.created_at,
                o.ingr_shop_id,
                s.first_name AS shop_first_name,
                s.last_name AS shop_last_name,
                o.ingr_branch_id,
                b.name AS branch_name,
                b.phone AS branch_phone,
                o.driver_id,
                d.first_name AS driver_first_name,
                d.last_name AS driver_last_name,
                d.phone AS driver_phone
            FROM orders o
            LEFT JOIN client_branches b ON b.id = o.ingr_branch_id
            LEFT JOIN users s ON s.id = o.ingr_shop_id
            LEFT JOIN users d ON d.id = o.driver_id
            WHERE DATE(o.created_at) IN (CURDATE(), CURDATE() - INTERVAL 1 DAY)
        ");
    }

    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS view_search_orders");
    }
}
