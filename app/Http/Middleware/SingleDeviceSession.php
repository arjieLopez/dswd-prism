<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SingleDeviceSession
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
            $currentSessionId = Session::getId();

            // Check if user has a stored session ID from a different device
            if ($user->session_id && $user->session_id !== $currentSessionId) {
                // This is the OLD session being kicked out
                // Clear the user's session_id so they can login again
                $user->session_id = null;
                $user->save();

                // Log out current (old) session and show message
                Auth::logout();
                Session::flush();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('status', 'Your account has been logged in from another device. Please login again.');
            }

            // Update user's session ID to current session
            if ($user->session_id !== $currentSessionId) {
                $user->session_id = $currentSessionId;
                $user->save();
            }
        }

        return $next($request);
    }
}
