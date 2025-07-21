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
        if(auth()->check() && (auth()->user()->twofactor_code && now()->lessThanOrEqualTo(auth()->user()->twofactor_code_expires_at))) {
            // If the user has a valid two-factor code, allow the request to proceed
            return redirect()->route('verify.show');
        }
        return $next($request);
    }
}
