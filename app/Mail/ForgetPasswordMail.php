<?php

namespace App\Mail;

use App\Models\MailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForgetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $langId;

    /**
     * Create a new message instance.
     *
     * @param array $data Data to be passed to the email view
     * @param int $langId
     */
    public function __construct(array $data, int $langId = 1)
    {
        $this->data = $data;
        $this->langId = $langId;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Reset Your Password';
        $template = MailTemplate::getByKey('forget_password', $this->langId);

        if ($template) {
            $templateModel = MailTemplate::find($template['id']);
            if ($templateModel) {
                $rendered = $templateModel->renderTemplate($this->langId, $this->data);
                $subject = $rendered['subject'] ?: $subject;
            }
        }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $body = null;
        $template = MailTemplate::getByKey('forget_password', $this->langId);

        if ($template) {
            $templateModel = MailTemplate::find($template['id']);
            if ($templateModel) {
                $rendered = $templateModel->renderTemplate($this->langId, $this->data);
                $body = $rendered['body'];
            }
        }

        if (!$body) {
            $otp = $this->data['otp'] ?? '';
            $body = "<h2>Reset Your Password</h2>
                     <p>We received a request to reset your password. Use the following OTP to proceed:</p>
                     <div class='otp-box'><p class='otp-code'>{$otp}</p></div>
                     <p>This code will expire in 15 minutes. If you did not request a password reset, please ignore this email.</p>
                     <p>Thank you,<br>" . config('app.name', 'Localio') . " Team</p>";
        }

        return new Content(
            view: 'emails.layouts.master',
            with: [
                'subject' => 'Reset Your Password',
                'content' => $body,
                'showPreferences' => false,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
