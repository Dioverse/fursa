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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->json('shipping_address');

            $table->string('order_id')->unique();
            $table->decimal('total_amount', 12, 2);
            $table->string('trans_ref')->unique();
            $table->decimal('shipping_cost', 8, 2);
            $table->decimal('tax', 3, 2)->unsigned()->default(0);
            $table->string('delivery_days');

            $table->enum('status', [
                'pending',
                'confirmed',
                'processing',
                'shipping',
                'shipped',
                'out for delivery',
                'delivered',
                'cancelled',
                'failed',
                'expired'
            ])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
