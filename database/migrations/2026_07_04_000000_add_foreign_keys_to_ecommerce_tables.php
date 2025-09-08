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

        // categories.parent_id → categories.id
        Schema::table('categories', function (Blueprint $table) {
            $table->foreign('parent_id')
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
        // payments.user_id → users.id
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('order_id')
                  ->references('id')->on('orders')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });

        // coupon_users.coupon_id → coupons.id
        // coupon_users.user_id → users.id
        Schema::table('coupon_users', function (Blueprint $table) {
            $table->foreign('coupon_id')
                  ->references('id')->on('coupons')
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

        Schema::create('posts', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('post_category_id')
                  ->references('id')->on('post_categories')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('distributors', fn (Blueprint $table) => $table->dropForeign(['user_id']));
        Schema::table('products', fn (Blueprint $table) => $table->dropForeign(['category_id']));
        Schema::table('distributor_product_prices', fn (Blueprint $table) => $table->dropForeign(['product_id']));
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['product_id']);
        });
        Schema::table('payments', fn (Blueprint $table) => $table->dropForeign(['order_id']));
        Schema::table('coupon_users', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::table('shipping_addresses', fn (Blueprint $table) => $table->dropForeign(['user_id']));
        Schema::table('carts', fn (Blueprint $table) => $table->dropForeign(['user_id']));
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['cart_id']);
            $table->dropForeign(['product_id']);
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['post_category_id']);
        });

        Schema::table('discounts', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });
    }
};
