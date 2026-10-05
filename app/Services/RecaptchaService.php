<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    /**
     * Verify a reCAPTCHA v3 response token against Google's siteverify endpoint.
     *
     * @param string|null $token
     * @param string|null $action Expected action name (e.g., 'login', 'register', 'contact')
     * @param string|null $ip
     * @return array
     */
    public function verify(?string $token, ?string $action = null, ?string $ip = null): array
    {
        $secretKey = config('services.recaptcha.secret_key');

        // Graceful fallback: If secret key is not set, allow requests (e.g. in development/tests)
        if (empty($secretKey)) {
            Log::info('reCAPTCHA bypassed: RECAPTCHA_SECRET_KEY is not configured in services.');
            return [
                'success' => true,
                'score' => 1.0,
                'bypassed' => true,
                'message' => null,
            ];
        }

        if (empty($token)) {
            return [
                'success' => false,
                'score' => 0.0,
                'bypassed' => false,
                'message' => 'reCAPTCHA verification failed: missing security token. Please refresh the page and try again.',
            ];
        }

        try {
            $postData = [
                'secret'   => $secretKey,
                'response' => $token,
            ];
            if (!empty($ip)) {
                $postData['remoteip'] = $ip;
            }

            $response = Http::asForm()
                ->timeout(8)
                ->post('https://www.google.com/recaptcha/api/siteverify', $postData);

            if (!$response->successful()) {
                Log::error('reCAPTCHA HTTP error: ' . $response->status() . ' - ' . $response->body());
                return [
                    'success' => false,
                    'score' => 0.0,
                    'bypassed' => false,
                    'message' => 'Unable to verify security challenge. Please try again shortly.',
                ];
            }

            $data = $response->json();
            $isSuccess = ($data['success'] ?? false) === true;
            $score = isset($data['score']) ? (float) $data['score'] : 0.0;
            $minScore = (float) config('services.recaptcha.min_score', 0.5);

            if (!$isSuccess) {
                $errorCodes = implode(', ', $data['error-codes'] ?? ['unknown']);
                Log::warning("reCAPTCHA failed from Google: {$errorCodes}");
                return [
                    'success' => false,
                    'score' => $score,
                    'bypassed' => false,
                    'message' => 'Security check failed. Please refresh the page and try again.',
                ];
            }

            if ($score < $minScore) {
                Log::warning("reCAPTCHA score ({$score}) below threshold ({$minScore}) for action '{$action}'");
                return [
                    'success' => false,
                    'score' => $score,
                    'bypassed' => false,
                    'message' => 'Suspicious activity detected. Please try again or contact support.',
                ];
            }

            // Optional action verification
            if ($action && isset($data['action']) && $data['action'] !== $action) {
                Log::warning("reCAPTCHA action mismatch: expected '{$action}', got '{$data['action']}'");
            }

            return [
                'success' => true,
                'score' => $score,
                'action' => $data['action'] ?? null,
                'bypassed' => false,
                'message' => null,
            ];
        } catch (\Throwable $e) {
            Log::error('reCAPTCHA exception: ' . $e->getMessage());
            return [
                'success' => false,
                'score' => 0.0,
                'bypassed' => false,
                'message' => 'An error occurred during security verification. Please try again.',
            ];
        }
    }

    /**
     * Check if the verification passes.
     */
    public function passes(?string $token, ?string $action = null, ?string $ip = null): bool
    {
        return $this->verify($token, $action, $ip)['success'];
    }
}
