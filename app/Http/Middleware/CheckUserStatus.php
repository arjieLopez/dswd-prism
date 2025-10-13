<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Check if user is inactive (email_verified_at is null)
            if (is_null($user->email_verified_at)) {
                // Log the user out
                Auth::logout();

                // Invalidate the session
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Redirect to login with error message
                return redirect()->route('login')->withErrors([
                    'email' => 'Your account has been deactivated. Please contact the administrator.',
                ]);
            }
        }

        return $next($request);
    }
}
