<?php

namespace App\Rules;

use App\Services\RecaptchaService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\DataAwareRule;

class Recaptcha implements ValidationRule, DataAwareRule
{
    protected ?string $action;
    protected array $data = [];

    /**
     * Create a new rule instance.
     *
     * @param string|null $action Expected action (e.g., 'login', 'register', 'contact')
     */
    public function __construct(?string $action = null)
    {
        $this->action = $action;
    }

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value) || !is_string($value)) {
            $fail('Security check token is missing. Please refresh and try again.');
            return;
        }

        $service = app(RecaptchaService::class);
        $result = $service->verify($value, $this->action, request()->ip());

        if (!$result['success']) {
            $fail($result['message'] ?? 'reCAPTCHA verification failed. Please try again.');
        }
    }
}
