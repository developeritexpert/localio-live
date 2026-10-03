<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class DynamicTemplateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailSubject;
    public $renderedHtmlBody;
    public $showPreferences;
    public $mailAttachments;

    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param string $renderedHtmlBody
     * @param bool $showPreferences
     * @param array $attachments
     */
    public function __construct(string $subject, string $renderedHtmlBody, bool $showPreferences = true, array $attachments = [])
    {
        $this->mailSubject = $subject;
        $this->renderedHtmlBody = $renderedHtmlBody;
        $this->showPreferences = $showPreferences;
        $this->mailAttachments = $attachments;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.layouts.master',
            with: [
                'subject' => $this->mailSubject,
                'content' => $this->renderedHtmlBody,
                'showPreferences' => $this->showPreferences,
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
        $builtAttachments = [];

        foreach ($this->mailAttachments as $attachment) {
            if (is_string($attachment) && file_exists($attachment)) {
                $builtAttachments[] = Attachment::fromPath($attachment);
            } elseif ($attachment instanceof Attachment) {
                $builtAttachments[] = $attachment;
            }
        }

        return $builtAttachments;
    }
}
