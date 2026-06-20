<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            // Payment method fields (all optional during registration)
            'mpesa_phone' => 'nullable|string|regex:/^[0-9]{10}$/',
            'tigopesa_phone' => 'nullable|string|regex:/^[0-9]{10}$/',
            'halopesa_phone' => 'nullable|string|regex:/^[0-9]{10}$/',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mpesa_phone' => $request->mpesa_phone,
            'tigopesa_phone' => $request->tigopesa_phone,
            'halopesa_phone' => $request->halopesa_phone,
            'bank_name' => $request->bank_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_name' => $request->bank_account_name,
            'role' => 'user',
            'wallet_balance' => 0,
        ]);

        $tokenData = $user->createApiToken('auth_token');

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role,
                'wallet_balance' => (float) $user->wallet_balance,
            ],
            'access_token' => $tokenData['access_token'],
            'token_type' => $tokenData['token_type'],
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid login credentials'
            ], 401);
        }

        $user = Auth::user();
        $deviceName = $request->device_name ?? 'mobile_app_' . time();
        
        $tokenData = $user->createApiToken($deviceName);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role,
                'wallet_balance' => (float) $user->wallet_balance,
                'has_mpesa' => !empty($user->mpesa_phone),
                'has_tigopesa' => !empty($user->tigopesa_phone),
                'has_halopesa' => !empty($user->halopesa_phone),
                'has_bank' => !empty($user->bank_account_number),
            ],
            'access_token' => $tokenData['access_token'],
            'token_type' => $tokenData['token_type'],
        ]);
    }

    /**
     * Update user payment details
     */
    public function updatePaymentDetails(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'mpesa_phone' => 'nullable|string|regex:/^[0-9]{10}$/',
            'tigopesa_phone' => 'nullable|string|regex:/^[0-9]{10}$/',
            'halopesa_phone' => 'nullable|string|regex:/^[0-9]{10}$/',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('mpesa_phone')) {
            $user->mpesa_phone = $request->mpesa_phone;
        }
        if ($request->has('tigopesa_phone')) {
            $user->tigopesa_phone = $request->tigopesa_phone;
        }
        if ($request->has('halopesa_phone')) {
            $user->halopesa_phone = $request->halopesa_phone;
        }
        if ($request->has('bank_name')) {
            $user->bank_name = $request->bank_name;
        }
        if ($request->has('bank_account_number')) {
            $user->bank_account_number = $request->bank_account_number;
        }
        if ($request->has('bank_account_name')) {
            $user->bank_account_name = $request->bank_account_name;
        }
        
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment details updated successfully',
            'data' => [
                'mpesa_phone' => $user->mpesa_phone,
                'tigopesa_phone' => $user->tigopesa_phone,
                'halopesa_phone' => $user->halopesa_phone,
                'bank_name' => $user->bank_name,
                'bank_account_number' => $user->bank_account_number,
                'bank_account_name' => $user->bank_account_name,
            ]
        ]);
    }

    /**
     * Logout user (revoke current token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Logout from all devices
     */
    public function logoutAllDevices(Request $request)
    {
        $request->user()->revokeAllTokens();

        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices'
        ]);
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role,
                'wallet_balance' => (float) $user->wallet_balance,
                'avatar' => $user->avatar_url,
                'joined_date' => $user->created_at->format('M d, Y'),
                // Payment methods
                'payment_methods' => [
                    'mpesa' => [
                        'enabled' => true,
                        'phone' => $user->mpesa_phone,
                        'is_setup' => !empty($user->mpesa_phone),
                    ],
                    'tigopesa' => [
                        'enabled' => true,
                        'phone' => $user->tigopesa_phone,
                        'is_setup' => !empty($user->tigopesa_phone),
                    ],
                    'halopesa' => [
                        'enabled' => true,
                        'phone' => $user->halopesa_phone,
                        'is_setup' => !empty($user->halopesa_phone),
                    ],
                    'card' => [
                        'enabled' => true,
                        'is_setup' => false, // Cards are handled by Stripe/Pesapal
                    ],
                    'bank' => [
                        'enabled' => true,
                        'bank_name' => $user->bank_name,
                        'account_number' => $user->bank_account_number,
                        'account_name' => $user->bank_account_name,
                        'is_setup' => !empty($user->bank_account_number),
                    ],
                    'pesapal' => [
                        'enabled' => true,
                        'is_setup' => false,
                    ],
                ],
            ]
        ]);
    }

    /**
     * Refresh token
     */
    public function refreshToken(Request $request)
    {
        $user = $request->user();
        
        // Revoke old token
        $request->user()->currentAccessToken()->delete();
        
        // Create new token
        $tokenData = $user->createApiToken('refresh_token_' . time());

        return response()->json([
            'success' => true,
            'access_token' => $tokenData['access_token'],
            'token_type' => $tokenData['token_type'],
        ]);
    }

    /**
     * Get available payment methods for the user
     */
    public function getPaymentMethods(Request $request)
    {
        $user = $request->user();
        
        $methods = [];
        
        // M-Pesa
        if (!empty($user->mpesa_phone)) {
            $methods['mpesa'] = [
                'name' => 'M-Pesa',
                'icon' => 'ti-device-mobile',
                'phone' => $user->mpesa_phone,
                'fee' => 0,
                'min_amount' => 100,
                'max_amount' => 500000,
                'is_setup' => true,
            ];
        } else {
            $methods['mpesa'] = [
                'name' => 'M-Pesa',
                'icon' => 'ti-device-mobile',
                'fee' => 0,
                'min_amount' => 100,
                'max_amount' => 500000,
                'is_setup' => false,
                'setup_required' => true,
            ];
        }
        
        // TigoPesa
        if (!empty($user->tigopesa_phone)) {
            $methods['tigopesa'] = [
                'name' => 'TigoPesa',
                'icon' => 'ti-device-mobile',
                'phone' => $user->tigopesa_phone,
                'fee' => 0,
                'min_amount' => 100,
                'max_amount' => 500000,
                'is_setup' => true,
            ];
        } else {
            $methods['tigopesa'] = [
                'name' => 'TigoPesa',
                'icon' => 'ti-device-mobile',
                'fee' => 0,
                'min_amount' => 100,
                'max_amount' => 500000,
                'is_setup' => false,
                'setup_required' => true,
            ];
        }
        
        // HaloPesa
        if (!empty($user->halopesa_phone)) {
            $methods['halopesa'] = [
                'name' => 'HaloPesa',
                'icon' => 'ti-device-mobile',
                'phone' => $user->halopesa_phone,
                'fee' => 0,
                'min_amount' => 100,
                'max_amount' => 500000,
                'is_setup' => true,
            ];
        } else {
            $methods['halopesa'] = [
                'name' => 'HaloPesa',
                'icon' => 'ti-device-mobile',
                'fee' => 0,
                'min_amount' => 100,
                'max_amount' => 500000,
                'is_setup' => false,
                'setup_required' => true,
            ];
        }
        
        // Card (always available)
        $methods['card'] = [
            'name' => 'Credit/Debit Card',
            'icon' => 'ti-credit-card',
            'fee' => 2.5,
            'min_amount' => 500,
            'max_amount' => 1000000,
            'is_setup' => true,
        ];
        
        // Bank Transfer
        if (!empty($user->bank_account_number)) {
            $methods['bank'] = [
                'name' => 'Bank Transfer',
                'icon' => 'ti-building-bank',
                'bank_name' => $user->bank_name,
                'account_number' => $user->bank_account_number,
                'account_name' => $user->bank_account_name,
                'fee' => 0,
                'min_amount' => 1000,
                'max_amount' => 10000000,
                'is_setup' => true,
            ];
        } else {
            $methods['bank'] = [
                'name' => 'Bank Transfer',
                'icon' => 'ti-building-bank',
                'fee' => 0,
                'min_amount' => 1000,
                'max_amount' => 10000000,
                'is_setup' => false,
                'setup_required' => true,
            ];
        }
        
        // Pesapal (always available)
        $methods['pesapal'] = [
            'name' => 'PesaPal',
            'icon' => 'ti-world',
            'fee' => 0,
            'min_amount' => 100,
            'max_amount' => 10000000,
            'is_setup' => true,
        ];
        
        return response()->json([
            'success' => true,
            'gateways' => $methods,
            'recommended' => 'mpesa',
        ]);
    }
}