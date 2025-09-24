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
        Schema::create('shippings', function (Blueprint $table) {
            $table->id();
            $table->string('country');  // e.g. "Nigeria"
            $table->string('state');    // e.g. "Lagos"
            $table->string('province');      // e.g. "Ikorodu"
            $table->unsignedInteger('min_days');
            $table->unsignedInteger('max_days');
            $table->decimal('cost', 10, 2);
            $table->string('provider')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes(); // adds deleted_at column
            $table->timestamps();

            $table->unique(['country', 'state', 'province']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shippings');
        
    }
};
