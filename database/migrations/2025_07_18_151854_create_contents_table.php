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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g., "Hero Section", "About Us Block"
            $table->string('slug'); // e.g., "hero-section", "about-us-block"
            $table->string('type');  // e.g., "text", "image", "testimonial"
            $table->json('data');    // Stores the specific content data (heading, body, imageUrl, quote, etc.)
            $table->timestamps();    // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
