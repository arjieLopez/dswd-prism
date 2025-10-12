<?php

namespace App\Http\Controllers;

use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Http\Request;

class TwoFactorCodeController extends Controller
{
    /**
     * Show the form for verifying the two-factor code.
     */
    public function show()
    {
        $user = auth()->user();

        // If user doesn't need 2FA verification, redirect to appropriate dashboard
        if (!$user->needsTwoFactorVerification()) {
            // Clear any expired codes
            if ($user->hasExpiredTwoFactorCode()) {
                $user->clearTwoFactorCode();
            }

            if ($user->role == 'admin') {
                return redirect()->route('admin');
            } elseif ($user->role == 'staff') {
                return redirect()->route('staff');
            } else {
                return redirect()->route('user');
            }
        }

        return view('auth.verify');
    }

    public function resend(Request $request)
    {
        auth()->user()->regenerateTwoFactorCode();
        auth()->user()->notify(new TwoFactorCodeNotification());

        return back()->with('success', 'A new two-factor code has been sent to your email.');
    }
    /**
     * Handle the verification of the two-factor code.
     */
    public function verify(Request $request)
    {
        $request->validate(
            [
                'twofactor_code' => 'required|digits:6',
            ],
            [
                'twofactor_code.required' => 'The two-factor code is required.',
                'twofactor_code.digits' => 'The two-factor code must be 6 digits.',
            ]
        );

        $user = auth()->user();

        // Check if user has a 2FA code set
        if (!$user->twofactor_code) {
            return back()->with('error', 'No two-factor code found. Please request a new code.');
        }

        // Check if code has expired
        if (now()->greaterThan($user->twofactor_code_expires_at)) {
            $user->clearTwoFactorCode();
            return back()->with('error', 'Two-factor code has expired. Please request a new code.');
        }

        // Verify the code
        $isValidCode = $user->twofactor_code === $request->input('twofactor_code');

        if ($isValidCode) {
            // Clear the 2FA code after successful verification
            $user->clearTwoFactorCode();

            // Redirect based on user role
            if ($user->role == 'admin') {
                return redirect()->route('admin');
            } elseif ($user->role == 'staff') {
                return redirect()->route('staff');
            } else {
                return redirect()->route('user');
            }
        } else {
            return back()->with('error', 'Invalid two-factor code. Please try again.');
        }
    }
}
