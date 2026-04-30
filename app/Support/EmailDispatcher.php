<?php

namespace App\Support;

use App\Mail\TemplatedMail;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailDispatcher
{
    /**
     * Render a template by key with variables and send it.
     *
     * @param  string  $key   Template key (e.g. 'auth.welcome')
     * @param  string|User|array{0:string,1:?int}  $to  Email string, User model, or [email, user_id]
     * @param  array<string,mixed>  $vars  Variables for substitution
     * @param  string|null  $replyTo  Optional Reply-To address applied to the outgoing message
     * @param  array<int, array{name: string, data: string, mime?: string}>  $attachments  Optional binary attachments
     */
    public function send(string $key, string|User|array $to, array $vars = [], ?string $replyTo = null, array $attachments = []): bool
    {
        $template = EmailTemplate::findByKey($key);

        if (! $template) {
            Log::warning("Email template not found: {$key}");
            return false;
        }

        [$email, $userId, $userName] = $this->resolveRecipient($to);

        $vars = array_merge($this->defaultVars($email, $userName), $vars);

        $subject = $this->substitute($template->subject, $vars);
        $body = $this->substitute($template->body_html, $vars);

        try {
            $pending = Mail::to($email);
            if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
                $pending->replyTo($replyTo);
            }
            $pending->send(new TemplatedMail($subject, $body, $attachments));

            EmailLog::create([
                'user_id' => $userId,
                'to_email' => $email,
                'template_key' => $key,
                'subject' => $subject,
                'context' => $vars,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send templated email [{$key}] to {$email}: {$e->getMessage()}");

            EmailLog::create([
                'user_id' => $userId,
                'to_email' => $email,
                'template_key' => $key,
                'subject' => $subject,
                'context' => $vars,
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Render template body without sending — used for admin preview.
     */
    public function preview(string $key, array $vars = []): array
    {
        $template = EmailTemplate::findByKey($key);
        if (! $template) {
            return ['subject' => '', 'body' => ''];
        }
        $vars = array_merge($this->defaultVars(null, null), $vars);
        return [
            'subject' => $this->substitute($template->subject, $vars),
            'body' => $this->substitute($template->body_html, $vars),
        ];
    }

    public function substitute(string $template, array $vars): string
    {
        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_.]+)\s*\}\}/', function ($m) use ($vars) {
            return (string) ($vars[$m[1]] ?? '');
        }, $template);
    }

    protected function resolveRecipient(string|User|array $to): array
    {
        if ($to instanceof User) {
            return [$to->email, $to->id, $to->name];
        }
        if (is_array($to)) {
            return [$to[0], $to[1] ?? null, $to[2] ?? null];
        }
        return [$to, null, null];
    }

    protected function defaultVars(?string $email, ?string $name): array
    {
        return [
            'app_name' => config('app.name', 'JOSEOCEANJOBS'),
            'app_url' => url('/'),
            'support_email' => 'info@joseoceanjobs.com',
            'name' => $name ?? '',
            'email' => $email ?? '',
            'year' => date('Y'),
        ];
    }
}
