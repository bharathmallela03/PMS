<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin;
use App\Models\Pharmacist;
use App\Models\Supplier;
use App\Models\Customer;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        $userType = $request->get('type', 'customer');
        return view('auth.login', compact('userType'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'user_type' => 'required|in:admin,pharmacist,supplier,customer'
        ]);

        $credentials = $request->only('email', 'password');
        $userType = $request->user_type;

        $guard = $userType;
        $redirectRoute = $userType . '.dashboard';

        if (Auth::guard($guard)->attempt($credentials)) {
            $user = Auth::guard($guard)->user();
            
            // Check if user is active
            if (isset($user->is_active) && !$user->is_active) {
                Auth::guard($guard)->logout();
                return back()->withErrors(['email' => 'Your account has been deactivated.']);
            }

            // Check if user needs to setup password (for pharmacists and suppliers)
            if (in_array($userType, ['pharmacist', 'supplier']) && $user->needsPasswordSetup()) {
                Auth::guard($guard)->logout();
                return redirect()->route('password.setup.form', $user->setup_token)
                    ->with('info', 'Please setup your password to continue.');
            }

            return redirect()->route($redirectRoute);
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout(Request $request)
    {
        $guards = ['admin', 'pharmacist', 'supplier', 'customer'];
        
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
                break;
            }
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'country' => 'required|string|max:100',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'country' => $request->country,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        Auth::guard('customer')->login($customer);

        return redirect()->route('customer.dashboard')->with('success', 'Registration successful!');
    }

    public function showPasswordSetup($token)
    {
        $user = Pharmacist::where('setup_token', $token)->first() 
                ?? Supplier::where('setup_token', $token)->first();

        if (!$user || !$user->needsPasswordSetup()) {
            return redirect()->route('login')->withErrors(['token' => 'Invalid or expired token.']);
        }

        return view('auth.setup-password', compact('user', 'token'));
    }

    public function setupPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Pharmacist::where('setup_token', $request->token)->first() 
                ?? Supplier::where('setup_token', $request->token)->first();

        if (!$user || !$user->needsPasswordSetup()) {
            return back()->withErrors(['token' => 'Invalid or expired token.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'password_setup_at' => now(),
            'setup_token' => null,
        ]);

        $guard = $user instanceof Pharmacist ? 'pharmacist' : 'supplier';
        Auth::guard($guard)->login($user);

        $redirectRoute = $guard . '.dashboard';
        return redirect()->route($redirectRoute)->with('success', 'Password setup successful!');
    }

    /**
     * Show admin dashboard
     */
    public function adminDashboard()
    {
        return view('admin.dashboard');
    }

    /**
     * Show pharmacist dashboard
     */
    public function pharmacistDashboard()
    {
        return view('pharmacist.dashboard');
    }

    /**
     * Show supplier dashboard
     */
    public function supplierDashboard()
    {
        return view('supplier.dashboard');
    }

    /**
     * Show customer dashboard
     */
    public function customerDashboard()
    {
        return view('customer.dashboard');
    }
}