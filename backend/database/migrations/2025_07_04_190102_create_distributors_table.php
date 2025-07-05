<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('distributors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            // Business Information
            $table->string('company_name');
            $table->string('registered_name')->nullable();
            $table->string('rc_number');
            $table->string('email');
            $table->string('business_address');
            $table->string('office_phone')->nullable();
            $table->string('website')->nullable();
            $table->string('company_type');

            // Contact Person
            $table->string('contact_full_name');
            $table->string('contact_position');
            $table->string('contact_mobile');
            $table->string('id_number');
            $table->string('means_of_id');

            // Distribution Capacity
            $table->integer('years_in_business')->nullable();
            $table->string('current_product_lines')->nullable();
            $table->string('monthly_capacity')->nullable();
            $table->string('regions_covered')->nullable();
            $table->integer('number_of_sales_staff')->nullable();
            $table->boolean('has_warehouse')->default(false);
            $table->string('preferred_region');
            $table->boolean('has_vehicles')->default(false);
            $table->string('vehicle_details')->nullable();

            // Distribution Strategy
            $table->json('product_categories')->nullable(); // automotive, industrial, etc.
            $table->boolean('willing_to_train')->default(false);
            $table->boolean('has_technical_knowledge')->default(false);
            $table->string('distribution_start_time')->nullable(); // e.g. Immediately

            // States of Interest
            $table->json('preferred_states')->nullable();
            $table->string('promo_participation')->nullable(); // Yes, No, Depends

            // Banking
            $table->string('bank_name');
            $table->string('account_name');
            $table->string('account_number');
            $table->string('bvn')->nullable();
            $table->text('partnerships')->nullable();

            // Declaration
            $table->string('declarant_name');
            $table->date('declaration_date');

            // Uploads
            $table->string('cac_certificate')->nullable();
            $table->string('form_co7')->nullable();
            $table->string('memart')->nullable();
            $table->string('utility_bill')->nullable();
            $table->string('tin_certificate')->nullable();
            $table->string('id_of_contact')->nullable();
            $table->string('referee_letter')->nullable();
            $table->string('signature')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributors');
    }
};
