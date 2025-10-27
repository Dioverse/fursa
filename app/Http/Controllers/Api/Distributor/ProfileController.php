<?php
namespace App\Http\Controllers\Api\Distributor;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show()
    {
        // Get the currently authenticated user
        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Load distributor details if user is a distributor
        if ($user->role === 'distributor') {
            $user->load('distributor'); // assuming a relation named distributorDetails
        }

        return response()->json([
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        try {
            // Initialize defaults
            $userData        = [];
            $distributorData = [];

            // Only validate user basic fields if all are present
            // if (! in_array("", $request->only(['first_name', 'last_name', 'phone']))) {
            //     $userData = $this->validateBasicFields($request, $user);
            // }

            // Check user distributor state and validate accordingly
            if ($user->isDistributorApprov()) {
                $distributorData = $this->validateDistributorApprovedFields($request);
            } elseif ($user->isDistributorReject()) {
                $distributorData = $this->validateDistributorFields($request);
            }

            // Handle file uploads if the user is a distributor (and not approved)
            if ($user->isDistributorReject()) {
                // Validate file fields (all nullable)
                return $request->validate([
                    'cac_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                    'form_co7'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                    'memart'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                    'utility_bill'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                    'tin_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                    'id_of_contact'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                    'referee_letter'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                    'signature'       => 'nullable|file|mimes:jpg,jpeg,png|max:1024',
                ]);

                // Process only files that exist in request
                $distributorData = $this->handleFileUploads($distributorData, $request, $user->id);
            }

            // Use DB transaction to safely persist updates
            DB::transaction(function () use ($user, $userData, $distributorData) {
                // if (! empty($userData)) {
                //     $user->update($userData);
                // }

                if (! empty($distributorData)) {
                    $user->distributor()->updateOrCreate(
                        ['user_id' => $user->id],
                        $distributorData
                    );
                }
            });

            return response()->json([
                'message' => 'Profile updated successfully',
                'user'    => $user->load('distributor'),
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unable to update.',
                'error'   => $e->getMessage(),
            ], 400);
        }
    }


    /**
     * Validate basic user fields.
     */
    protected function validateBasicFields(Request $request, User $user)
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            // 'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'      => ['required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
        ]);
    }

    /**
     * Validate distributor-specific fields.
     */
    protected function validateDistributorFields(Request $request)
    {
        return $request->validate([
            // Company Information
            'company_name' => 'required|string|max:255',
            'registered_name' => 'nullable|string|max:255',
            'rc_number' => 'required|string|max:100',
            'business_address' => 'required|string|max:500',
            'office_phone' => 'required|string|max:20',
            'website' => 'nullable|url|max:255',
            'company_type' => 'required|string|max:100',
            
            // Contact Person
            'contact_full_name' => 'required|string|max:255',
            'contact_position' => 'required|string|max:100',
            'contact_mobile' => 'required|string|max:20',
            'id_number' => 'required|string|max:100',
            'means_of_id' => 'required|string|max:100',
            
            // Distribution Capacity
            'years_in_business' => 'required|integer|min:0|max:200',
            'current_product_lines' => 'required|string|max:500',
            'monthly_capacity' => 'required|string|max:255',
            'regions_covered' => 'required|string|max:255',
            'number_of_sales_staff' => 'required|integer|min:0|max:10000',
            'has_warehouse' => 'required|boolean',
            'preferred_region' => 'required|string|max:255',
            'has_vehicles' => 'required|boolean',
            'vehicle_details' => 'required_if:has_vehicles,1|string|max:500',
            
            // Distribution Strategy
            'product_categories' => 'required|array',
            'product_categories.*' => 'required|string|max:120',
            'willing_to_train' => 'required|string|max:50',
            'has_technical_knowledge' => 'required|boolean',
            'distribution_start_time' => 'required|string|max:100',
            
            // States of Interest
            'preferred_states' => 'required|array',
            'preferred_states.*' => 'required|string|max:100',
            'promo_participation' => 'required|in:Yes,No,Depends',
            
            // Banking
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:20',
            'bvn' => 'required|string|size:11',
            'partnerships' => 'nullable|string',
            
            // Declaration
            'declarant_name' => 'required|string|max:255',
            'declaration_date' => 'required|date',
        ]);
    }

    protected function validateDistributorApprovedFields(Request $request)
    {
        return $request->validate([
            // // Company Information
            // 'company_name' => 'required|string|max:255',
            // 'registered_name' => 'nullable|string|max:255',
            // 'rc_number' => 'required|string|max:100',
            // 'business_address' => 'required|string|max:500',
            // 'office_phone' => 'required|string|max:20',
            // 'website' => 'required|url|max:255',
            // 'company_type' => 'required|string|max:100',

            // // Contact Person
            // 'contact_full_name' => 'required|string|max:255',
            // 'contact_position' => 'required|string|max:100',
            // 'contact_mobile' => 'required|string|max:20',
            // 'id_number' => 'required|string|max:100',
            // 'means_of_id' => 'required|string|max:100',

            // // Distribution Capacity
            // 'years_in_business' => 'required|integer|min:0|max:200',
            // 'current_product_lines' => 'required|string|max:500',
            // 'monthly_capacity' => 'required|string|max:255',
            // 'regions_covered' => 'required|string|max:255',
            // 'number_of_sales_staff' => 'required|integer|min:0|max:10000',
            // 'has_warehouse' => 'required|boolean',
            // 'preferred_region' => 'required|string|max:255',
            // 'has_vehicles' => 'required|boolean',
            // 'vehicle_details' => 'required|string|max:500',

            // // Distribution Strategy
            // 'product_categories' => 'required|array',
            // 'product_categories.*' => 'required|string|max:100',
            // 'willing_to_train' => 'required|boolean',
            // 'has_technical_knowledge' => 'required|boolean',
            // 'distribution_start_time' => 'required|string|max:100',

            // // States of Interest
            // 'preferred_states' => 'required|array',
            // 'preferred_states.*' => 'required|string|max:100',
            // 'promo_participation' => 'required|in:Yes,No,Depends',

            // Banking
            'bank_name'      => 'required|string|max:255',
            'account_name'   => 'required|string|max:255',
            'account_number' => 'required|string|max:20',
            'bvn'            => 'required|string|size:11',
            // 'partnerships' => 'required|string',

            // // Declaration
            // 'declarant_name' => 'required|string|max:255',
            // 'declaration_date' => 'required|date',
        ]);
    }

    public function updateDocuments(Request $request)
    {
        $user = Auth::user();

        if (! $user->isDistributor() || $user->isDistributorApprov()) {
            return response()->json([
                'message' => 'Unable to update.',
            ], 400);
        }

        // Validate file fields
        $validated = $request->validate([
            'cac_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'form_co7'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'memart'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'utility_bill'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'tin_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'id_of_contact'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'referee_letter'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'signature'       => 'nullable|file|mimes:jpg,jpeg,png|max:1024',
        ]);

        // Handle file uploads
        $distributorData = $this->handleFileUploads($validated, $request, $user->id);

        // Update distributor table
        $user->distributor()->updateOrCreate(['user_id' => $user->id], $distributorData);

        return response()->json([
            'message' => 'Distributor documents updated successfully',
            'user'    => $user->load('distributor'),
        ]);
    }

    private function handleFileUploads(array $distributorData, Request $request, int $userId): array
    {
        $fileFields = [
            'cac_certificate', 'form_co7', 'memart', 'utility_bill',
            'tin_certificate', 'id_of_contact', 'referee_letter', 'signature',
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file      = $request->file($field);
                $extension = $file->getClientOriginalExtension();
                $filename  = $field . '.' . $extension;

                // Store file in organized path
                $path = $file->storeAs("distributors/{$userId}", $filename, 'public');

                $distributorData[$field] = $path;
            }
        }

        return $distributorData;
    }
}
