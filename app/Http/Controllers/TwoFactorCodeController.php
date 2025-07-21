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

        $result = $user->twofactor_code === $request->input('twofactor_code') &&
            now()->lessThanOrEqualTo($user->twofactor_code_expires_at);

        if ($result) {

            $user->clearTwoFactorCode();

            if ($user->role == 'admin') {
                return redirect()->route('admin');
            } elseif ($user->role == 'staff') {
                return redirect()->route('staff');
            } else {
                return redirect()->route('user');
            }
        } else {
            return back()->with('error', 'Try Again');
        }
    }
}
