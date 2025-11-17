<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Notifications\TwoFactorCodeNotification;
use App\Providers\RouteServiceProvider;
use App\Services\RecaptchaService;
use App\Services\ActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request, RecaptchaService $recaptcha): RedirectResponse
    {
        // 1. Validate login fields (LoginRequest already does this)
        // Temporarily commented out recaptcha
        // $request->validate([
        //     'recaptcha_token' => 'required',
        // ]);

        // 2. reCAPTCHA check - Temporarily commented out
        // if (!$recaptcha->verify($request->recaptcha_token, 'login')) {
        //     // Log failed login attempt
        //     ActivityService::logLoginFailed($request->email);
        //     return back()->withErrors(['recaptcha' => 'reCAPTCHA verification failed.']);
        // }

        // 3. Authenticate user
        $request->authenticate();
        $request->session()->regenerate();

        // 4. Two-Factor Authentication
        $user = Auth::user();
        if ($user) {
            $user->regenerateTwoFactorCode();
            $user->notify(new TwoFactorCodeNotification());

            // Log successful login
            ActivityService::logUserLogin($user->id, $user->name);
        } else {
            // Log failed login attempt
            ActivityService::logLoginFailed($request->email);
            return back()->withErrors(['email' => 'Authentication failed. Please try again.']);
        }

        // 6. Redirect to verify page for 2FA
        return redirect()->route('verify.show');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Log logout before destroying session
        if (Auth::check()) {
            $user = Auth::user();
            ActivityService::logUserLogout($user->id, $user->name);

            // Clear user's session_id to allow login from any device
            $user->session_id = null;
            $user->save();
        }

        Auth::guard('web')->logout();

        // Flush all session data
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
