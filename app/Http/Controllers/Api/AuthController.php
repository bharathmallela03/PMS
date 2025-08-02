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
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        $token = $customer->createToken('authToken')->accessToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful!',
            'user' => $customer,
            'token' => $token
        ], 201);
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
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::guard('customer')->attempt($credentials)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials.'], 401);
        }

        $customer = Auth::guard('customer')->user();

        if (!$customer->is_active) {
            return response()->json(['success' => false, 'message' => 'Your account has been deactivated.'], 403);
        }

        $token = $customer->createToken('authToken')->accessToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful!',
            'user' => $customer,
            'token' => $token
        ]);
    }

    /**
     * Logout the authenticated customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out.'
        ]);
    }
}
