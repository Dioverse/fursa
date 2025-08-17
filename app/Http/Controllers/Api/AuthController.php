<?php

namespace App\Http\Controllers\Api;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Distributor;
use App\Rules\PasswordCheck;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationSuccessMail;
use App\Mail\UserLoggedInNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Symfony\Component\Mailer\Exception\TransportException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        try {
            // 1. Validate incoming request data
            $request->validate([
                'user' => ['required', 'string'],
                'password' => ['required', 'string'],
            ], [
                'user.required' => "Enter valid phone number or email address"
            ]);

            // 2. Determine if the input is an email or a phone number
            $loginField = filter_var($request->input('user'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
            $loginValue = $request->input('user');
            $password = $request->input('password');

            // 3. Find the user by the determined login field
            $user = User::where($loginField, $loginValue)->first();

            // 4. Verify user existence and password
            if (!$user || !Hash::check($password, $user->password)) {
                throw ValidationException::withMessages([
                    'user' => [trans('auth.failed')],
                ]);
            }

            if ($user->role === 'distributor') {
                $user->load('distributor');
            }
            
            // 5. Credentials are valid, create an API token
            $token = $user->createToken('api-token')->plainTextToken;

            // 6. Prepare data for email notification
            $ipAddress = $request->ip();
            $loginTime = Carbon::now()->toDateTimeString();

            // 7. Send email notification (still queued for performance)
            Mail::to($user->email)->queue(new UserLoggedInNotification($user, $ipAddress, $loginTime));

            // 8. Return success response with user data and the token
            return response()->json([
                'message' => 'Login successful.',
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Catch any other unexpected errors
            Log::error("API Login error: " . $e->getMessage() . " on line " . $e->getLine() . " in " . $e->getFile());
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => 'Please try again later.',
            ], 500);
        }
    }

    public function register(Request $request): JsonResponse
    {
        // Determine the role, defaulting to 'customer' if not provided
        $role = $request->input('role', 'customer');

        // Common user validation rules
        $userRules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'phone'      => ['required', 'string', 'max:20'],
            'password'   => ['required', 'string', 'min:6', 'confirmed'], // Added 'confirmed' rule
        ];

        // Additional rules for distributors
        $distributorRules = [
            // Company
            'company_name'        => 'required|string|max:255',
            'registered_name'     => 'string|max:255',
            'rc_number'           => 'required|string|max:100',
            // 'email'               => 'required|email|max:255|unique:distributors,email',
            'business_address'    => 'required|string|max:500',
            'office_phone'        => 'required|string|max:20',
            'website'             => 'required|url|max:255',
            'company_type'        => 'required|string|max:100',

            // Contact Person
            'contact_full_name'   => 'required|string|max:255',
            'contact_position'    => 'required|string|max:100',
            'contact_mobile'      => 'required|string|max:20',
            'id_number'           => 'required|string|max:100',
            'means_of_id'         => 'required|string|max:100',

            // Distribution Capacity
            'years_in_business'   => 'required|integer|min:0|max:200',
            'current_product_lines' => 'required|string|max:500',
            'monthly_capacity'    => 'required|string|max:255',
            'regions_covered'     => 'required|string|max:255',
            'number_of_sales_staff'=> 'required|integer|min:0|max:10000',
            'has_warehouse'       => 'required|boolean',
            'preferred_region'    => 'required|string|max:255',
            'has_vehicles'        => 'required|boolean',
            'vehicle_details'     => 'required|string|max:500',

            // Distribution Strategy
            'product_categories'  => 'required|array',
            'product_categories.*'=> 'required|string|max:100', // each category item
            'willing_to_train'    => 'required|boolean',
            'has_technical_knowledge' => 'required|boolean',
            'distribution_start_time' => 'required|string|max:100',

            // States of Interest
            'preferred_states'    => 'required|array',
            'preferred_states.*'  => 'required|string|max:100',
            'promo_participation' => 'required|in:Yes,No,Depends',

            // Banking
            'bank_name'           => 'required|string|max:255',
            'account_name'        => 'required|string|max:255',
            'account_number'      => 'required|string|max:20',
            'bvn'                 => 'required|string|max:11', // BVN is 11 digits in Nigeria
            'partnerships'        => 'required|string',

            // Declaration
            'declarant_name'      => 'required|string|max:255',
            'declaration_date'    => 'required|date',

            // Uploads (files)
            'cac_certificate'     => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'form_co7'            => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'memart'              => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'utility_bill'        => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'tin_certificate'     => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'id_of_contact'       => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'referee_letter'      => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'signature'           => 'required|file|mimes:jpg,jpeg,png|max:1024',
        ];

        // Merge rules conditionally based on the role
        $validationRules = $role === 'distributor' ? array_merge($userRules, $distributorRules) : $userRules;

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create user
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'password'   => Hash::make($request->password), // Hash the password
            'role'       => $role === 'distributor' ? 'distributor' : 'customer',
            'status'     => $role === 'distributor' ? 'pending' : 'approved', // Default status based on role
        ]);

        // If distributor, create corresponding distributor profile
        if ($role === 'distributor') {
            $distributorData = collect($validator->validated())
                ->except(['first_name', 'last_name', 'email', 'phone', 'password'])
                ->all();

            $fileFields = [
                'cac_certificate', 'form_co7', 'memart', 'utility_bill',
                'tin_certificate', 'id_of_contact', 'referee_letter', 'signature'
            ];

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $file     = $request->file($field);
                    $ext      = $file->getClientOriginalExtension();
                    $filename = "{$field}.{$ext}";

                    $path = $file->storeAs("distributors/{$user->id}", $filename, 'public');
                    $distributorData[$field] = $path;
                }
            }

            $distributorData['user_id'] = $user->id;
            $distributorData['email'] = $user->email;
            Distributor::create($distributorData);
        }

        // Send Registration Success Email (your custom mail)
        Mail::to($user->email)->queue(new RegistrationSuccessMail($user));

        event(new Registered($user));

        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json([
            'message'   => 'Registration successful. Please check your email to verify your account.',
            'user'      => $user,
            'token'     => $token,
            'token_type'=> "Bearer"
        ], 201); // 201 Created status code
    }

    public function emailVerify(EmailVerificationRequest $request): JsonResponse
    {
        // Check if the authenticated user's email is already verified
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 400);
        }
        
        $request->fulfill(); // Mark user as verified
        return response()->json([
            'message' => 'Email verified successfully.'
        ]);
    }

    public function verificationSend(Request $request): JsonResponse
    {
        // Check if the authenticated user's email is already verified
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 400);
        }

        // Send the verification email notification
        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent!']);
    }


    public function forgotPassword(Request $request) {
        $validatedData = Validator::make($request->all(['email']),[
            'email' => "required|email|exists:users,email",
        ],[
            'email.required'=>'A valid email address is required.',
            'email.email'=>'Provide a valid email address.',
            'email.exists'=>'Account not found, check and try again.'
        ]);
        if ($validatedData->fails()) {
            $arr = [
                    'status'=> 'false',
                    'data' => [
                        'message' => 'Validation failed',
                        'error' => $validatedData->errors(),
                    ]
                ];
        } else {
            try {
                $response = Password::sendResetLink($request->only('email'));
                switch ($response) {
                    case Password::RESET_LINK_SENT:
                        return response()->json(['status'=>'true','data'=>["message"=>"An email has been sent to your address, Please check your inbox for the password reset button."]],200);
                    case Password::INVALID_USER:
                        return response()->json(['status'=>'false','data'=>['message'=>"Account not found, check and try again."]],401);
                    default:
                        return response()->json(["status"=> "false","data"=>["message"=> "An error occured, please try again"]],400);
                }
            } catch (TransportException $ex) {
                $arr = array("status" => "false", "message" => "An error occured, please try again", "data" => ['error' => $ex->getMessage()]);
            } catch (Exception $ex) {
                $arr = array("status" => "false", "message" => "An error occured, please try again", "data" => ['error' => $ex->getMessage()]);
            }
        }
        return response()->json($arr,401);
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
                    ])->save();
                    // ])->setRememberToken(null)->save();

                    event(new PasswordReset($user));
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
                            'message' => 'The provided email is invalid.'
                        ]
                    ], 400);
                default:
                    return response()->json([
                        'status' => false,
                        'data' => [
                            'message' => 'An error occurred during password reset. Please try again.'
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

    // public function updatePassword(Request $request) {
    //     $validatedData = Validator::make($request->all(['current_password','password','password_confirmation']), [
    //         'current_password' => ['required', new PasswordCheck],
    //         'password' => ['required', 'confirmed', PasswordRule::min(6)->mixedCase()->numbers()->symbols()],
    //         'password_confirmation' => 'required|min:6|same:password',
    //     ],[
    //         'current_password.required' => 'Your current password is required!',
    //         'password.required' => 'Password is required for security!',
    //         'password.confirmed' => 'Password & confirm password should be same!',
    //         'password.min' => 'Password should have at least 6 characters',
    //         'password_confirmation.required' => 'Password confirmation is required!',
    //         'password_confirmation.min' => 'Confirm password should have at least 6 characters!',
    //         'password_confirmation.same' => 'Confirm password does not match password!',
    //     ]);

    //     if ($validatedData->fails()) {
    //         return response()->json([
    //             'status'=>'false',
    //             'data' => [
    //                 'message' => "Validation failed",
    //                 'error' => $validatedData->errors()
    //             ]
    //         ],400);
    //     }
    
    //     $user = Auth::user();
    
    //     if ($user->update(['password' => Hash::make($request->password)])) {
    //         return response()->json([
    //             'status'=> 'true',
    //             'data' => [
    //                 'message'=> 'Password reset successfully',
    //                 'user'=> Auth::user()->refresh()
    //             ]
    //         ],200);
    //     }

    //     return response()->json([
    //         'status'=> 'false',
    //         'data' => [
    //             'message'=> 'Password reset failed',
    //         ]
    //     ],400);
    // }

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
