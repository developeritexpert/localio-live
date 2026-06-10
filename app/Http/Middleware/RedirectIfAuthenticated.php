<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated

{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {


        // Check if the user is authenticated


        if (Auth::check()) {
            // Get the authenticated user
            $user = Auth::user();
            // Check the user's role and redirect accordingly
            if ($user->user_type === 'admin') {
                return redirect('/admin-dashboard');
            } 

            // Default redirect for other roles (optional)
            return $next($request);
        }

        // If the user is not authenticated, proceed with the request
        return $next($request);
    }
}
