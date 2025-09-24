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
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            // Link to order
            $table->unsignedBigInteger('order_id');
            
            // The status value (could be enum or string)
            $table->enum('status', [
                'pending',
                'confirmed',
                'processing',
                'shipping',
                'shipped',
                'out for delivery',
                'delivered',
                'cancelled',
                'failed'
            ]);
            
            // Optional: who triggered the change (user/admin/system)
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->string('change_role')->nullable(); // e.g., 'user', 'admin', 'system'
            
            // Optional note for context
            $table->text('note')->nullable();
            
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            // you can also link changed_by to users table if it only applies to users:
            $table->foreign('changed_by')->references('id')->on('users')->nullOnDelete();

            $table->unique(['status', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
    }
};
