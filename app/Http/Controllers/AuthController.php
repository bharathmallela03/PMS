<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail; // <-- Import Mail facade
use App\Models\Admin;
use App\Models\Pharmacist;
use App\Models\Supplier;
use App\Models\Customer;
use App\Mail\WelcomeCustomer; // <-- Import your new Mailable

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin(Request $request)
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        // 1. Validate only email and password
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        
        // 2. Define the order of guards to check
        $guards = ['admin', 'pharmacist', 'supplier', 'customer'];

        // 3. Loop through guards and attempt to login
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->attempt($credentials, $request->boolean('remember'))) {
                $user = Auth::guard($guard)->user();
                $redirectRoute = $guard . '.dashboard';

                if (isset($user->is_active) && !$user->is_active) {
                    Auth::guard($guard)->logout();
                    return back()->withErrors(['email' => 'Your account has been deactivated.']);
                }

                if (in_array($guard, ['pharmacist', 'supplier']) && $user->needsPasswordSetup()) {
                    Auth::guard($guard)->logout();
                    return redirect()->route('password.setup.form', $user->setup_token)
                        ->with('info', 'Please set up your password to continue.');
                }
                
                $request->session()->regenerate();
                return redirect()->intended(route($redirectRoute));
            }
        }

        // 4. If no guard authenticated, return error
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
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

        // Login the new customer
        Auth::guard('customer')->login($customer);

        // ** NEW: Send the welcome email **
        try {
            Mail::to($customer->email)->send(new WelcomeCustomer($customer));
        } catch (\Exception $e) {
            // Optional: Log the error or handle it gracefully if mail fails
            // \Log::error("Mail sending failed: " . $e->getMessage());
            \Log::error("Mail sending failed: " . $e->getMessage());
        }

        return redirect()->route('customer.dashboard')->with('success', 'Registration successful! Welcome aboard.');
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

    public function adminDashboard()
    {
        return view('admin.dashboard');
    }

    public function pharmacistDashboard()
    {
        return view('pharmacist.dashboard');
    }

    public function supplierDashboard()
    {
        return view('supplier.dashboard');
    }

    public function customerDashboard()
    {
        return view('customer.dashboard');
    }
}
