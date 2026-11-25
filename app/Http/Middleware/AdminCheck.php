<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->user_type === 'admin') {
                if (!session('admin_2fa_verified')) {
                    return redirect()->route('admin.2fa.show')->with('error', 'Please complete Two-Factor Authentication to access the dashboard.');
                }
                $language = Language::find(1);
                $lang = $language ? $language->lang_code : 'en-us';
                
                App::setLocale($lang);
                return $next($request);
            }

            return redirect('/')->with('error', 'Access denied.');
        }

        // If admin has 2FA pending in session, redirect to 2FA verification screen
        if (session('admin_2fa_pending')) {
            return redirect()->route('admin.2fa.show');
        }

        return redirect()->route('login')->with('error', 'Please log in to open the dashboard.');
    }
}
