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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['pending', 'successful', 'failed', 'reversed'])->default('pending');
            $table->enum('refund_status', ['pending', 'successful', 'failed'])->nullable();
            $table->string('reason')->nullable();
            $table->string('payment_gateway'); // e.g., Paystack, Flutterwave
            $table->string('payment_method')->nullable(); // e.g., card, transfer
            $table->string('transaction_reference')->unique();
            $table->decimal('amount', 12, 2);
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->json('raw')->nullable();
            $table->json('refund_raw')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
