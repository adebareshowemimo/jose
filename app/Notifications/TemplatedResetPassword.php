<?php

namespace App\Notifications;

use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Support\EmailDispatcher;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class TemplatedResetPassword extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $resetUrl = route('auth.reset-password', ['token' => $this->token])
            . '?email=' . urlencode($notifiable->getEmailForPasswordReset());

        $template = EmailTemplate::findByKey('auth.reset_password');
        if (! $template) {
            return parent::toMail($notifiable);
        }

        $dispatcher = app(EmailDispatcher::class);
        $vars = [
            'name' => $notifiable->name,
            'email' => $notifiable->email,
            'reset_url' => $resetUrl,
            'expire_minutes' => config('auth.passwords.users.expire', 60),
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
            'template_key' => 'auth.reset_password',
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
}
