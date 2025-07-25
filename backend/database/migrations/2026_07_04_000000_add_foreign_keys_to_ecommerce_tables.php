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
        // orders.shipping_address_id → shipping_addresses.id
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')->on('users');

            $table->foreign('shipping_address_id')
                  ->references('id')->on('shipping_addresses');
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
        // payments.user_id → users.id
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('order_id')
                  ->references('id')->on('orders')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });

        // discount_users.discount_id → discounts.id
        // discount_users.user_id → users.id
        Schema::table('discount_users', function (Blueprint $table) {
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

        Schema::table('carts', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        // cart_items.product_id → products.id
        // cart_items.user_id → users.id
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreign('cart_id')
                ->references('id')
                ->on('carts')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('distributors', fn (Blueprint $table) => $table->dropForeign(['user_id']));
        Schema::table('products', fn (Blueprint $table) => $table->dropForeign(['category_id']));
        Schema::table('distributor_product_prices', fn (Blueprint $table) => $table->dropForeign(['product_id']));
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['shipping_address_id']);
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['product_id']);
        });
        Schema::table('payments', fn (Blueprint $table) => $table->dropForeign(['order_id']));
        Schema::table('discount_users', function (Blueprint $table) {
            $table->dropForeign(['discount_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::table('shipping_addresses', fn (Blueprint $table) => $table->dropForeign(['user_id']));
        Schema::table('carts', fn (Blueprint $table) => $table->dropForeign(['user_id']));
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['cart_id']);
            $table->dropForeign(['product_id']);
        });
    }
};
