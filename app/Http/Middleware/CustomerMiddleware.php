<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerMiddleware
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
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('login', ['type' => 'customer'])->with('error', 'Please login as customer to access this page.');
        }

        $customer = Auth::guard('customer')->user();
        
        // Check if customer is active
        if (!$customer->is_active) {
            Auth::guard('customer')->logout();
            return redirect()->route('login', ['type' => 'customer'])->with('error', 'Your account has been deactivated.');
        }

        return $next($request);
    }
}