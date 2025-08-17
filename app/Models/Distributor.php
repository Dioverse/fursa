<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Distributor extends Model
{
    /** @use HasFactory<\Database\Factories\DistributorFactory> */
    use HasFactory;
    
    protected $table = "distributors";

    protected $fillable = [
        'user_id',
        'company_name',
        'registered_name',
        'rc_number',
        'email',
        'business_address',
        'office_phone',
        'website',
        'company_type',
        'contact_full_name',
        'contact_position',
        'contact_mobile',
        'id_number',
        'means_of_id',
        'years_in_business',
        'current_product_lines',
        'monthly_capacity',
        'regions_covered',
        'number_of_sales_staff',
        'has_warehouse',
        'preferred_region',
        'has_vehicles',
        'vehicle_details',
        'product_categories',
        'willing_to_train',
        'has_technical_knowledge',
        'distribution_start_time',
        'preferred_states',
        'promo_participation',
        'bank_name',
        'account_name',
        'account_number',
        'bvn',
        'partnerships',
        'declarant_name',
        'declaration_date',
        'cac_certificate',
        'form_co7',
        'memart',
        'utility_bill',
        'tin_certificate',
        'id_of_contact',
        'referee_letter',
        'signature'
    ];
    
    protected $casts = [
        'product_categories' => 'array',
        'preferred_states' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
