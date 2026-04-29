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

        $isEmployer = $notifiable->role?->name === 'employer';
        $templateKey = $isEmployer ? 'auth.verify_email_employer' : 'auth.verify_email';

        $template = EmailTemplate::findByKey($templateKey);
        if (! $template && $isEmployer) {
            $templateKey = 'auth.verify_email';
            $template = EmailTemplate::findByKey($templateKey);
        }
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

        if ($isEmployer) {
            $vars['company_name'] = $notifiable->company?->name ?? $notifiable->name;
        }

        $subject = $dispatcher->substitute($template->subject, $vars);
        $body = $dispatcher->substitute($template->body_html, $vars);

        EmailLog::create([
            'user_id' => $notifiable->id,
            'to_email' => $notifiable->email,
            'template_key' => $templateKey,
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
