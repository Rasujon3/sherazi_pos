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
        Schema::table('products', function (Blueprint $table) {
            $table->index('sold_count'); // ORDER BY sold_count DESC
            $table->index('name'); // LIKE search (partial help)
            $table->index('category_id'); // already FK but make explicit
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('status'); // WHERE status = ?
            $table->index('customer_id'); // JOIN/WHERE on customer
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('product_id'); // eager load join
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
