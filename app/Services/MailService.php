<?php

namespace App\Services;

use App\Mail\DynamicTemplateMail;
use App\Models\MailTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailService
{
    /**
     * Templates that cannot be disabled by user preferences (essential transactional emails)
     */
    protected static array $mandatoryTemplates = [
        'forget_password',
        'password_changed',
    ];

    /**
     * Send an email using a registered MailTemplate key.
     *
     * @param string $key
     * @param string|array $to Email address or [email, name]
     * @param array $variables Key-value pairs for placeholders
     * @param int|null $langId Language ID (defaults to current or 1)
     * @param User|null $user Optional user object to check notification preferences
     * @param array $attachments File paths or Attachment instances
     * @return bool
     */
    public static function sendTemplate(
        string $key,
        $to,
        array $variables = [],
        ?int $langId = null,
        ?User $user = null,
        array $attachments = []
    ): bool {
        try {
            $langId = $langId ?? (function_exists('getCurrentLanguageID') ? getCurrentLanguageID() : 1);

            $template = MailTemplate::where('key', $key)->where('is_active', true)->first();

            if (!$template) {
                Log::warning("MailService: Template with key '{$key}' not found or inactive.");
                return false;
            }

            // Check user preferences if user is passed and template is not mandatory
            if ($user && !in_array($key, self::$mandatoryTemplates)) {
                if (method_exists($user, 'hasDisabledTemplate') && $user->hasDisabledTemplate($template->id)) {
                    Log::info("MailService: User #{$user->id} has disabled template '{$key}'. Skipping email.");
                    return false;
                }
            }

            // Inject standard global variables if not provided
            $defaultVariables = [
                'app_name' => config('app.name', 'Localio'),
                'app_url' => url('/'),
                'year' => date('Y'),
                'current_year' => date('Y'),
                'support_email' => config('mail.from.address', 'support@localio.live'),
            ];

            $mergedVariables = array_merge($defaultVariables, $variables);

            // Render subject and body with variables
            $rendered = $template->renderTemplate($langId, $mergedVariables);
            $subject = $rendered['subject'];
            $body = $rendered['body'];

            $showPreferences = $user !== null && !in_array($key, self::$mandatoryTemplates);

            Mail::to($to)->send(new DynamicTemplateMail($subject, $body, $showPreferences, $attachments));

            Log::info("MailService: Successfully sent template '{$key}' to " . (is_array($to) ? json_encode($to) : $to));
            return true;
        } catch (\Throwable $e) {
            Log::error("MailService: Failed sending template '{$key}' to " . (is_array($to) ? json_encode($to) : $to) . ". Error: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return false;
        }
    }

    /**
     * Send raw HTML wrapped in the branded master email template.
     *
     * @param string|array $to
     * @param string $subject
     * @param string $htmlBody
     * @param bool $showPreferences
     * @param array $attachments
     * @return bool
     */
    public static function sendRaw(
        $to,
        string $subject,
        string $htmlBody,
        bool $showPreferences = false,
        array $attachments = []
    ): bool {
        try {
            Mail::to($to)->send(new DynamicTemplateMail($subject, $htmlBody, $showPreferences, $attachments));
            Log::info("MailService: Successfully sent raw email to " . (is_array($to) ? json_encode($to) : $to));
            return true;
        } catch (\Throwable $e) {
            Log::error("MailService: Failed sending raw email to " . (is_array($to) ? json_encode($to) : $to) . ". Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a test email for an existing MailTemplate.
     *
     * @param int|MailTemplate $template
     * @param string $toEmail
     * @param int $langId
     * @param array $sampleData
     * @return array ['success' => bool, 'message' => string]
     */
    public static function sendTestTemplate($template, string $toEmail, int $langId = 1, array $sampleData = []): array
    {
        try {
            $templateModel = is_numeric($template) ? MailTemplate::findOrFail($template) : $template;

            // Merge sample data
            $defaultVariables = [
                'app_name' => config('app.name', 'Localio'),
                'app_url' => url('/'),
                'year' => date('Y'),
                'name' => 'John Doe',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'user_name' => 'John Doe',
                'email' => $toEmail,
                'otp' => '849201',
                'business_name' => 'Bluehost',
                'rating' => '4.5',
                'review_url' => url('/'),
                'admin_review_url' => url('/admin-dashboard/reviews'),
                'login_url' => url('/login'),
                'dashboard_url' => url('/user-dashboard'),
                'ticket_id' => 'TICK-90812',
                'subject' => 'Test Subject',
                'message' => 'This is a test message body.',
                'reply_message' => 'Thank you for reaching out to us. We have resolved your issue.',
                'vendor_name' => 'John Doe',
                'reason' => 'Content violated community guidelines.',
            ];

            $mergedVariables = array_merge($defaultVariables, $sampleData);

            $rendered = $templateModel->renderTemplate($langId, $mergedVariables);
            $subject = '[TEST] ' . $rendered['subject'];
            $body = $rendered['body'];

            Mail::to($toEmail)->send(new DynamicTemplateMail($subject, $body, false));

            return [
                'success' => true,
                'message' => "Test email successfully sent to {$toEmail}."
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => "Failed to send test email: " . $e->getMessage()
            ];
        }
    }
}
