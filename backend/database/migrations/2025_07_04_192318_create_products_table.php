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
            $table->unsignedBigInteger('category_id')->nullable();

            $table->string('name');
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->decimal('distributor_price', 10, 2);
            $table->string('category')->nullable();
            $table->string('image')->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_threshold')->nullable();
            $table->json('tags')->nullable();
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
