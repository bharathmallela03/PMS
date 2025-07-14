<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PharmacistMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('pharmacist')->check()) {
            return redirect()->route('login', ['type' => 'pharmacist'])->with('error', 'Please login as pharmacist to access this page.');
        }

        $pharmacist = Auth::guard('pharmacist')->user();
        
        // Check if pharmacist is active
        if (!$pharmacist->is_active) {
            Auth::guard('pharmacist')->logout();
            return redirect()->route('login', ['type' => 'pharmacist'])->with('error', 'Your account has been deactivated.');
        }

        return $next($request);
    }
}