<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RecaptchaService
{
    public function verify(string $token, string $action = 'login', float $threshold = 0.5): bool
    {
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret'),
            'response' => $token,
        ]);

        $data = $response->json();

        // Optional: Log for debugging
        if (
            !$data['success'] ||
            $data['score'] < $threshold ||
            ($action && $data['action'] !== $action)
        ) {
            \Log::warning('reCAPTCHA verification failed', [
                'response' => $data,
                'expected_action' => $action,
                'expected_threshold' => $threshold,
            ]);
        }

        return $data['success'] &&
            $data['score'] >= $threshold &&
            ($action ? $data['action'] === $action : true);
    }
}
