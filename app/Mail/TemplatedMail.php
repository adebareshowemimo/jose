<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TemplatedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{name: string, data: string, mime?: string}>  $pdfAttachments
     */
    public function __construct(
        public string $renderedSubject,
        public string $renderedBody,
        public array $pdfAttachments = [],
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->renderedSubject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.layouts.master',
            with: [
                'subject' => $this->renderedSubject,
                'body' => $this->renderedBody,
                'appName' => config('app.name', 'JOSEOCEANJOBS'),
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $items = [];
        foreach ($this->pdfAttachments as $a) {
            $name = $a['name'] ?? 'attachment.pdf';
            $data = $a['data'] ?? '';
            $mime = $a['mime'] ?? 'application/pdf';
            $items[] = Attachment::fromData(fn () => $data, $name)->withMime($mime);
        }
        return $items;
    }
}
