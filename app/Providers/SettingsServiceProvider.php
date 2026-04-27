<?php

namespace App\Providers;

use App\Support\Settings;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Settings::class, fn () => new Settings());
    }

    public function boot(Settings $settings): void
    {
        try {
            $google = $settings->group('oauth_google');
            if (! empty($google)) {
                config([
                    'services.google.client_id' => $google['oauth.google.client_id'] ?? config('services.google.client_id'),
                    'services.google.client_secret' => $google['oauth.google.client_secret'] ?? config('services.google.client_secret'),
                    'services.google.redirect' => $google['oauth.google.redirect'] ?? config('services.google.redirect'),
                ]);
            }

            $microsoft = $settings->group('oauth_microsoft');
            if (! empty($microsoft)) {
                config([
                    'services.microsoft.client_id' => $microsoft['oauth.microsoft.client_id'] ?? config('services.microsoft.client_id'),
                    'services.microsoft.client_secret' => $microsoft['oauth.microsoft.client_secret'] ?? config('services.microsoft.client_secret'),
                    'services.microsoft.redirect' => $microsoft['oauth.microsoft.redirect'] ?? config('services.microsoft.redirect'),
                    'services.microsoft.tenant' => $microsoft['oauth.microsoft.tenant'] ?? config('services.microsoft.tenant', 'common'),
                ]);
            }

            $mail = $settings->group('mail');
            if (! empty($mail) && ! empty($mail['mail.smtp.enabled'] ?? false)) {
                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.host' => $mail['mail.smtp.host'] ?? config('mail.mailers.smtp.host'),
                    'mail.mailers.smtp.port' => $mail['mail.smtp.port'] ?? config('mail.mailers.smtp.port'),
                    'mail.mailers.smtp.username' => $mail['mail.smtp.username'] ?? config('mail.mailers.smtp.username'),
                    'mail.mailers.smtp.password' => $mail['mail.smtp.password'] ?? config('mail.mailers.smtp.password'),
                    'mail.mailers.smtp.encryption' => $mail['mail.smtp.encryption'] ?? config('mail.mailers.smtp.encryption'),
                ]);
            }

            if (! empty($mail['mail.from.address'] ?? null)) {
                config(['mail.from.address' => $mail['mail.from.address']]);
            }
            if (! empty($mail['mail.from.name'] ?? null)) {
                config(['mail.from.name' => $mail['mail.from.name']]);
            }
        } catch (\Throwable $e) {
            // Silent: settings table may not exist yet (initial migrate).
        }
    }
}
