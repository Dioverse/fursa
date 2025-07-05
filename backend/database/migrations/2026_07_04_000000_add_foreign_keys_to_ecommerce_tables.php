<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // distributors.user_id → users.id
        Schema::table('distributors', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });

        // products.category_id → categories.id
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('category_id')
                  ->references('id')->on('categories')
                  ->nullOnDelete();
        });

        // distributor_product_prices.product_id → products.id
        Schema::table('distributor_product_prices', function (Blueprint $table) {
            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->onDelete('cascade');
        });

        // orders.user_id → users.id
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')->on('users');
        });

        // order_items.order_id → orders.id
        // order_items.product_id → products.id
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('order_id')
                  ->references('id')->on('orders')
                  ->onDelete('cascade');

            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->onDelete('cascade');
        });

        // payments.order_id → orders.id
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('order_id')
                  ->references('id')->on('orders')
                  ->onDelete('cascade');
        });

        // discount_user.discount_id → discounts.id
        // discount_user.user_id → users.id
        Schema::table('discount_user', function (Blueprint $table) {
            $table->foreign('discount_id')
                  ->references('id')->on('discounts')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });

        // shipping_addresses.user_id → users.id
        Schema::table('shipping_addresses', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('distributors', fn (Blueprint $table) => $table->dropForeign(['user_id']));
        Schema::table('products', fn (Blueprint $table) => $table->dropForeign(['category_id']));
        Schema::table('distributor_product_prices', fn (Blueprint $table) => $table->dropForeign(['product_id']));
        Schema::table('orders', fn (Blueprint $table) => $table->dropForeign(['user_id']));
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['product_id']);
        });
        Schema::table('payments', fn (Blueprint $table) => $table->dropForeign(['order_id']));
        Schema::table('discount_user', function (Blueprint $table) {
            $table->dropForeign(['discount_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::table('shipping_addresses', fn (Blueprint $table) => $table->dropForeign(['user_id']));
    }
};
