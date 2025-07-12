<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Distributor>
 */
class DistributorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'company_name' => fake()->company,
            'registered_name' => fake()->companySuffix,
            'rc_number' => fake()->numerify('RC######'),
            'email' => fake()->companyEmail,
            'business_address' => fake()->address,
            'office_phone' => fake()->phoneNumber,
            'website' => fake()->url,
            'company_type' => fake()->randomElement(['Private', 'Public']),
            'contact_full_name' => fake()->name,
            'contact_position' => fake()->jobTitle,
            'contact_mobile' => fake()->phoneNumber,
            'id_number' => fake()->numerify('ID######'),
            'means_of_id' => fake()->randomElement(['NIN', 'Voter ID', 'Driver’s License']),
            'years_in_business' => fake()->numberBetween(1, 20),
            'current_product_lines' => fake()->words(3, true),
            'monthly_capacity' => fake()->randomElement(['100 units', '500 units']),
            'regions_covered' => fake()->state,
            'number_of_sales_staff' => fake()->numberBetween(1, 10),
            'has_warehouse' => fake()->boolean,
            'preferred_region' => fake()->state,
            'has_vehicles' => fake()->boolean,
            'vehicle_details' => fake()->sentence,
            'product_categories' => json_encode(fake()->words(3)),
            'willing_to_train' => fake()->boolean,
            'has_technical_knowledge' => fake()->boolean,
            'distribution_start_time' => fake()->randomElement(['Immediately', 'Next Month']),
            'preferred_states' => json_encode([fake()->state, fake()->state]),
            'promo_participation' => fake()->randomElement(['Yes', 'No', 'Depends']),
            'bank_name' => fake()->randomElement(['GTBank', 'Access Bank']),
            'account_name' => fake()->name,
            'account_number' => fake()->bankAccountNumber,
            'bvn' => fake()->numerify('##########'),
            'partnerships' => fake()->sentence,
            'declarant_name' => fake()->name,
            'declaration_date' => now(),
            'cac_certificate' => null,
            'form_co7' => null,
            'memart' => null,
            'utility_bill' => null,
            'tin_certificate' => null,
            'id_of_contact' => null,
            'referee_letter' => null,
            'signature' => null,
            'status' => 'pending',
            'approved_at' => null,
        ];

    }
}
