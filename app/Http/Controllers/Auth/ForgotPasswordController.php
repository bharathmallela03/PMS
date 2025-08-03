<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\Customer;
use App\Models\Pharmacist;
use App\Models\Supplier;

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link.
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     * This method dynamically finds the user type and uses the correct password broker.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->email;
        $broker = null;

        // Determine which user type this email belongs to and set the broker
        if (Pharmacist::where('email', $email)->exists()) {
            $broker = 'pharmacists';
        } elseif (Supplier::where('email', $email)->exists()) {
            $broker = 'suppliers';
        } elseif (Customer::where('email', $email)->exists()) {
            $broker = 'customers';
        }

        // If no user is found with that email, return an error
        if (!$broker) {
            return back()->withErrors(['email' => trans(Password::INVALID_USER)]);
        }

        // Use the determined broker to send the reset link
        $response = Password::broker($broker)->sendResetLink(
            $request->only('email')
        );

        // Redirect back with a success or error message
        return $response == Password::RESET_LINK_SENT
                    ? back()->with('status', trans($response))
                    : back()->withErrors(['email' => trans($response)]);
    }
}
