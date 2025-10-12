<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user needs 2FA verification
        if ($user->needsTwoFactorVerification()) {
            // User needs to verify their 2FA code first
            return redirect()->route('verify.show');
        }

        // If user has an expired 2FA code, clear it
        if ($user->hasExpiredTwoFactorCode()) {
            $user->clearTwoFactorCode();
        }

        // Allow the request to proceed if no 2FA verification is needed
        return $next($request);
    }
}
