<?php

namespace App\Http\Controllers\Api;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Distributor;
use Illuminate\Support\Str;
use App\Rules\PasswordCheck;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationSuccessMail;
use App\Mail\UserLoggedInNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\CartController;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Symfony\Component\Mailer\Exception\TransportException;

class AuthController extends Controller
{
    
    private function validateRegistration(Request $request, string $role): array
    {
        $userRules = $this->getUserValidationRules();
        $validationRules = $role === 'distributor' 
            ? array_merge($userRules, $this->getDistributorValidationRules()) 
            : $userRules;
        
        $validator = Validator::make($request->all(), $validationRules);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return $validator->validated();
    }
    
    private function getUserValidationRules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
    
    private function getDistributorValidationRules(): array
    {
        return [
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
            
            // File Uploads
            'cac_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'form_co7' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'memart' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'utility_bill' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'tin_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'id_of_contact' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'referee_letter' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'signature' => 'required|file|mimes:jpg,jpeg,png|max:1024',
        ];
    }
    
    private function createUser(array $validatedData, string $role): User
    {
        return User::create(attributes: [
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'password' => Hash::make($validatedData['password']),
            'role' => $role === 'distributor' ? 'distributor' : 'customer',
            'status' => $role === 'distributor' ? 'pending' : 'approved',
            'email_verified_at' => null,
        ]);
    }
    
    private function createDistributorProfile(User $user, array $validatedData, Request $request, $email = null): Distributor
    {
        // Prepare distributor data (exclude user fields)
        $distributorData = collect(value: $validatedData)
            ->except(['first_name', 'last_name', 'email', 'phone', 'password', 'password_confirmation'])
            ->toArray();
        
        // Handle file uploads
        $distributorData = $this->handleFileUploads($distributorData, $request, $user->id);
        
        // Add user reference
        $distributorData['user_id'] = $user->id;
        $distributorData['email'] = $email ?? $user->email;
        
        return Distributor::create($distributorData);
    }
    
    private function handleFileUploads(array $distributorData, Request $request, int $userId): array
    {
        $fileFields = [
            'cac_certificate', 'form_co7', 'memart', 'utility_bill',
            'tin_certificate', 'id_of_contact', 'referee_letter', 'signature'
        ];
        
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                try {
                    $file = $request->file($field);
                    $extension = $file->getClientOriginalExtension();
                    $filename = $field . '.' . $extension;
                    
                    // Store file with a more organized path structure
                    $path = $file->storeAs(
                        "distributors/{$userId}", 
                        $filename, 
                        'public'
                    );
                    
                    $distributorData[$field] = $path;
                } catch (Exception $e) {
                    // Log file upload error and throw exception to trigger rollback
                    Log::error("File upload failed for {$field}: " . $e->getMessage());
                    throw new Exception("Failed to upload {$field}. Please try again.");
                }
            }
        }
        
        return $distributorData;
    }
    
    private function cleanupUserFiles(int $userId): void
    {
        try {
            $userDirectory = "distributors/{$userId}";
            if (Storage::disk('public')->exists($userDirectory)) {
                Storage::disk(name: 'public')->deleteDirectory($userDirectory);
            }
        } catch (Exception $e) {
            Log::error('Failed to cleanup user files: ' . $e->getMessage(), [
                'user_id' => $userId
            ]);
        }
    }

    private function getVerificationLink($id, $email)
    {
        $hash = sha1($email);
        $temporarySignedUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(10),
            ['id' => $id,'hash' => $hash,]
        );

        // Extract query params (?expires=..&signature=..)
        $queryParams = parse_url($temporarySignedUrl, PHP_URL_QUERY);
        // Parse query string into array
        parse_str($queryParams, $params);
        // Add our required fields explicitly
        $params['id']   = $id;
        $params['hash'] = $hash;
        // Build final query string
        $finalQuery = http_build_query($params);
        // Return full frontend verification link
        return rtrim(config('app.frontend_url'), '/') . '/' . config('app.frontend_verify_path') . '?' . $finalQuery;
    }

    public function upgradeToDistributor(Request $request): JsonResponse
    {
        $user = auth()->user();

        if ($user->role == 'distributor') {
            return response()->json([
                'message' => 'Check dashboard for application response.'
            ], 403);
        }
        if ($user->role !== 'customer' || empty($user->email_verified_at)) {
            return response()->json([
                'message' => 'Only verified customers can apply to become distributors.'
            ], 403);
        }
        $fields = [
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            ...$this->getDistributorValidationRules(),
        ];
        $emailChanged = $user->email !== $request->email;
        $phoneChanged = $user->phone !== $request->phone;
        if ($emailChanged) {
            $fields['email'] .= '|unique:users,email';
        }
        if ($phoneChanged) {
            $fields['phone'] .= '|unique:users,phone';
        }
        

        try {
            // Validate distributor-specific fields
            $validatedData = Validator::make($request->all(), $fields)->validate();
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Check password & Try again.'
                ], 403);
            }

            // Use transaction to ensure consistency
            $result = DB::transaction(function () use ($user, $validatedData, $request, $emailChanged) {
                // Create distributor profile
                $this->createDistributorProfile($user, $validatedData, $request, $request->email);
                $toUpdate = [
                    'role' => 'distributor',
                    'status' => 'pending'
                ];
                if ($emailChanged) {
                    $toUpdate['email_verified_at'] = null;
                    $toUpdate['email'] = $request->email;
                    $user->tokens()->delete();
                }
                // Update user role and status
                $user->update($toUpdate);
                return $user;
            });

            if ($emailChanged) {
                $link = $this->getVerificationLink($result->id,$result->email);
                notify('EMAIL_VERIFY', $result, [
                    "name" => $result->first_name, "verification_link" => $link
                ], ['email'], false);
            }
            return response()->json([
                'message' => 'Your application to become a distributor has been submitted. Please wait for approval.'
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Upgrade to distributor failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Application failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }


    public function register(Request $request): JsonResponse
    {
        $role = $request->input('role', 'customer');
        
        try {
            // Validate the request
            $validatedData = $this->validateRegistration($request, $role);
            
            // Use database transaction to ensure data consistency
            $result = DB::transaction(function () use ($validatedData, $role, $request, &$userId) {
                // Create the user
                $user = $this->createUser($validatedData, $role);
                $userId = $user->id; // Store user ID for potential cleanup
                
                // Create distributor profile if needed
                if ($role === 'distributor') {
                    $this->createDistributorProfile($user, $validatedData, $request);
                }
                
                return $user;
            });

            // event(new Registered($result));
            $link = $this->getVerificationLink($result->id,$result->email);
            notify('EMAIL_VERIFY', $result, [
                "name" => $result->first_name, "verification_link" => $link
            ], ['email'], false);
            
            $token = $result->createToken('api-token')->plainTextToken;
            return response()->json([
                'message' => 'Registration successful. Please check your email to verify your account.',
                'user' => $result,
                'token' => $token,
                'token_type' => 'Bearer'
            ], 201);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            if ($userId && $role === 'distributor') { $this->cleanupUserFiles($userId); }

            // Log the error for debugging
            Log::error('Registration failed: ' . $e->getMessage(), [
                'email' => $request->email ?? 'unknown',
                'role' => $role,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Registration failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    










    public function login(Request $request): JsonResponse
    {
        try {
            // 1. Validate incoming request data
            $request->validate([
                'user' => ['required', 'string'],
                'password' => ['required', 'string'],
            ], [
                'user.required' => 'Enter a valid phone number or email address.'
            ]);

            // 2. Determine if input is email or phone
            $loginField = filter_var($request->input('user'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
            $loginValue = $request->input('user');
            $password = $request->input('password');

            // 3. Retrieve user with related distributor if exists
            $user = User::with('distributor')->where($loginField, $loginValue)->first();

            // 4. Verify credentials
            if (!$user || !Hash::check($password, $user->password)) {
                throw ValidationException::withMessages([
                    'user' => [trans('auth.failed')],
                ]);
            }

            // 5. Check if user's email is verified (optional, for security)
            if ($user->email && !$user->email_verified_at) {
                return response()->json([
                    'message' => 'Please verify your email before logging in.',
                ], 403);
            }

            // 6. Destroy old tokens (optional but good practice)
            // If you want to allow only one device login at a time:
            // $user->tokens()->delete();

            // 7. Generate a new API token
            $token = $user->createToken('api-token')->plainTextToken;

            // 8. Capture meta info
            $ipAddress = $request->ip();
            $loginTime = now()->toDateTimeString();

            // 9. Send login alert (queued for async)
            notify('LOGIN_ALERT', $user, [
                'name' => $user->first_name,
                'ipAddress' => $ipAddress,
                'loginTime' => $loginTime
            ], ['email'], true);

            // 10. Sync user cart if provided
            $cart = [];
            if ($user->role != 'admin') {
                $cartController = new CartController();
                $cart = $cartController->syncUserCart($user, $request->cart);
            }

            // 11. Return response
            return response()->json([
                'message' => 'Login successful.',
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
                'cart' => $cart
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error("API Login error: {$e->getMessage()}", [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => config('app.debug') ? $e->getMessage() : 'Please try again later.',
            ], 500);
        }
    }


    public function emailVerify(Request $request, $id, $hash): JsonResponse
    {
        // Retrieve the user by ID from the verification link
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link.'], 400);
        }

        // If already verified, just return a fresh token
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 400);
        }

        // Fulfill the verification (marks email as verified)
        $user->markEmailAsVerified();
        $token = $user->createToken('api-token')->plainTextToken;

        // Send a welcome/registration email

        // $this->sendRegistrationEmail($user);
        $template = $user->role == 'distributor' ? "REGISTERED_DISTRIBUTOR" : "REGISTERED_USER";
        notify($template, $user, ["name" => $user->first_name], ['email'], false);

        return response()->json([
            'message'     => 'Email verified successfully.',
            'user'        => $user,
            'token'       => $token,
            'token_type'  => 'bearer',
        ], 200);
    }

    public function verificationSend(Request $request): JsonResponse
    {
        // Check if the authenticated user's email is already verified
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.', 'user' => $user->refresh()], 400);
        }

        // Send the verification email notification
        $user = $request->user();
        $link = $this->getVerificationLink($user->id,$user->email);
        notify('EMAIL_VERIFY', $user, [
            "name" => $user->first_name, "verification_link" => $link
        ], ['email'], false);
            
        return response()->json(['message' => 'Verification link sent!']);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if ($user) {
                // Create password reset token
                $token = Str::random(64);

                DB::table('password_reset_tokens')->updateOrInsert(
                    ['email' => $user->email],
                    ['token' => Hash::make($token),'created_at' => Carbon::now()]
                );

                // Build reset link (frontend URL)
                $resetLink = rtrim(config('app.frontend_url'), "/") . "/" . trim(config('app.frontend_forgot_pass'), "/") . "?token={$token}&email=" . urlencode($user->email);
                notify('PASSWORD_RESET_LINK', $user, ['name' => $user->name, 'resetLink' => $resetLink],["email"], false);
            }

            return response()->json([
                "message" => "If an account with that email exists, a password reset link is on its way."
            ], 200);

        } catch (Exception $ex) {
            return response()->json([
                "message" => "An error occurred, please try again."
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email|exists:users,email',
                'password' => 'required|confirmed|min:8',
            ], [
                'token.required' => 'Password reset token is missing.',
                'email.required' => 'Email is required.',
                'email.email' => 'Provide a valid email address.',
                'email.exists' => 'Account not found.',
                'password.required' => 'New password is required.',
                'password.confirmed' => 'Password confirmation does not match.',
                'password.min' => 'Password must be at least 8 characters.',
            ]);

            $response = Password::broker()->reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ]);

                    // generate a new remember token
                    $user->setRememberToken(Str::random(60));

                    $user->save();

                    event(new PasswordReset($user));

                    notify('PASSWORD_RESET_SUCCESS',$user,['name' => $user->name],["email"],false);
                }
            );

            switch ($response) {
                case Password::PASSWORD_RESET:
                    return response()->json([
                        'status' => true,
                        'data' => [
                            'message' => 'Your password has been reset successfully.'
                        ]
                    ], 200);
                case Password::INVALID_TOKEN:
                    return response()->json([
                        'status' => false,
                        'data' => [
                            'message' => 'This password reset token is invalid.'
                        ]
                    ], 400);
                case Password::INVALID_USER:
                    return response()->json([
                        'status' => false,
                        'data' => [
                            'message' => 'Invalid credentials.'
                        ]
                    ], 400);
                default:
                    return response()->json([
                        'status' => false,
                        'data' => [
                            'message' => 'An error occurred. Please try again.'
                        ]
                    ], 400);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'data' => [
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ]
            ], 422);
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'data' => [
                    'message' => 'An unexpected error occurred during password reset.',
                    'error' => $ex->getMessage()
                ]
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = $request->user();

        // Check if the current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect.'
            ], 422);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully.'
        ]);
    }
    
    public function refresh(Request $request)
    {
        $user = $request->user();

        // Delete the old token
        $request->user()->currentAccessToken()->delete();

        // Create a new one
        $newToken = $user->createToken('admin_token')->plainTextToken;

        return response()->json([
            'token' => $newToken,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'message' => 'Successfully logged out',
            ], 200);
        }

        return response()->json([
            'message' => 'No active session.',
        ], 401);
    } 
}
