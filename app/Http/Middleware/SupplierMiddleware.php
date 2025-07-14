<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierMiddleware
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
        if (!Auth::guard('supplier')->check()) {
            return redirect()->route('login', ['type' => 'supplier'])->with('error', 'Please login as supplier to access this page.');
        }

        $supplier = Auth::guard('supplier')->user();
        
        // Check if supplier is active
        if (!$supplier->is_active) {
            Auth::guard('supplier')->logout();
            return redirect()->route('login', ['type' => 'supplier'])->with('error', 'Your account has been deactivated.');
        }

        return $next($request);
    }
}