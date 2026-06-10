<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;

class CheckVendor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to access the vendor dashboard.');
        }

        // Ensure user is a vendor
        if (Auth::user()->user_type === 'vendor') {
            return $next($request);
        }

        // If not a vendor, redirect to login
        return redirect()->route('login')->with('error', 'Access denied. Vendors only.');
    }

}
