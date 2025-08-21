<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'contact_number' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'contact_number' => $request->contact_number,
                'password' => Hash::make($request->password),
                'is_active' => true,
            ]);

            $token = $customer->createToken('authToken');

            return response()->json([
                'success' => true,
                'message' => 'Registration successful!',
                'user' => $customer,
                'token' => $token
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Login a customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::guard('customer')->attempt($credentials)) {
            return response()->json([
                'success' => false, 
                'message' => 'Invalid email or password.'
            ], 401);
        }

        $customer = Auth::guard('customer')->user();

        if (!$customer->is_active) {
            Auth::guard('customer')->logout();
            return response()->json([
                'success' => false, 
                'message' => 'Your account has been deactivated. Please contact support.'
            ], 403);
        }

        // Revoke all existing tokens for this customer (optional - for single session)
        // $customer->tokens()->delete();

        $token = $customer->createToken('authToken');

        return response()->json([
            'success' => true,
            'message' => 'Login successful!',
            'user' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'contact_number' => $customer->contact_number,
                'is_active' => $customer->is_active,
                'created_at' => $customer->created_at,
                'updated_at' => $customer->updated_at,
            ],
            'token' => $token
        ], 200);
    }

    /**
     * Logout the authenticated customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            // For Laravel Sanctum
            $request->user()->currentAccessToken()->delete();
            
            // Alternative: For Laravel Passport
            // $request->user()->token()->revoke();

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed.'
            ], 500);
        }
    }

    /**
     * Get the authenticated customer's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ], 200);
    }
}