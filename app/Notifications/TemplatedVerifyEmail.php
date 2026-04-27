<?php

namespace App\Notifications;

use App\Mail\TemplatedMail;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Support\EmailDispatcher;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class TemplatedVerifyEmail extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $verifyUrl = $this->verificationUrl($notifiable);

        $template = EmailTemplate::findByKey('auth.verify_email');
        if (! $template) {
            // Fallback to Laravel default if template missing.
            return parent::toMail($notifiable);
        }

        $dispatcher = app(EmailDispatcher::class);
        $vars = [
            'name' => $notifiable->name,
            'email' => $notifiable->email,
            'verify_url' => $verifyUrl,
            'app_name' => config('app.name', 'JOSEOCEANJOBS'),
            'app_url' => url('/'),
            'support_email' => 'info@joseoceanjobs.com',
            'year' => date('Y'),
        ];
        $subject = $dispatcher->substitute($template->subject, $vars);
        $body = $dispatcher->substitute($template->body_html, $vars);

        EmailLog::create([
            'user_id' => $notifiable->id,
            'to_email' => $notifiable->email,
            'template_key' => 'auth.verify_email',
            'subject' => $subject,
            'context' => $vars,
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return (new MailMessage())
            ->subject($subject)
            ->view('emails.layouts.master', [
                'subject' => $subject,
                'body' => $body,
                'appName' => config('app.name', 'JOSEOCEANJOBS'),
            ]);
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
