<?php

namespace App\Mail;

use App\Models\MailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ForgetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data; // Property to hold the data passed to the mail
    public $langId;
    /**
     * Create a new message instance.
     *
     * @param array $data Data to be passed to the email view
     */
    public function __construct($data, $langId = 1)
    {
        $this->data = $data;
        $this->langId = $langId;
    }
    public function build()
    {
        $template = MailTemplate::getByKey('forget_password', $this->langId);

        if ($template) {
            $templateModel = MailTemplate::find($template['id']);
            if ($templateModel) {
                $rendered = $templateModel->renderTemplate($this->langId, $this->data);
                $subject = $rendered['subject'];
                $body = $rendered['body'];

                Log::info('Building email with custom HTML template.');

                return $this->subject($subject)->html($body);
            }
        }
        // Fallback if template is missing
        return $this->subject('Reset Your Password')
                    ->view('emails.forget_password')
                    ->with([
                        'otp' => $this->data['otp'] ?? '',
                        'data' => $this->data,
                    ]);
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
      // Get the mail template with translation
      $template = MailTemplate::getByKey('forget_password', $this->langId);

      if ($template) {
          Log::info('Template found for envelope:', ['template_id' => $template['id']]);

          $templateModel = MailTemplate::find($template['id']);
          if ($templateModel) {
              $rendered = $templateModel->renderTemplate($this->langId, $this->data);
              $subject = $rendered['subject'];

              Log::info('Rendered subject:', ['subject' => $subject]);
          } else {
              Log::warning('Template model not found:', ['template_id' => $template['id']]);
              $subject = 'Reset Your Password';
          }
      } else {
          Log::warning('No template found for forget_password');
          $subject = 'Reset Your Password';
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
        // Get the mail template with translation
        $template = MailTemplate::getByKey('forget_password', $this->langId);

        if ($template) {
            Log::info('Template found for content:', ['template_id' => $template['id']]);

            $templateModel = MailTemplate::find($template['id']);
            if ($templateModel) {
                $rendered = $templateModel->renderTemplate($this->langId, $this->data);
                $body = $rendered['body'];

                Log::info('Rendered body:', [
                    'body_length' => strlen($body),
                    'contains_otp' => strpos($body, $this->data['otp'] ?? '') !== false,
                    'body_preview' => substr($body, 0, 200) . '...'
                ]);
                return new Content(
                    html: $body,
                );
            } else {
                Log::error('Template model not found:', ['template_id' => $template['id']]);
            }
        } else {
            Log::warning('No template found, using fallback view');
        }
        // Fallback to original view if template not found
        Log::info('Using fallback view with data:', [
            'otp' => $this->data['otp'] ?? 'NOT_SET'
        ]);

        return new Content(
            view: 'emails.forget_password',
            with: [
                'otp' => $this->data['otp'] ?? '',
                'data' => $this->data, // Pass all data to the view
            ],
        );
    }
    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return []; // No attachments
    }
}
