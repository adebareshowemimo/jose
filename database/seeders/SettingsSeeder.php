<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Support\ContactRoutes;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Auth
            ['key' => 'auth.require_email_verification', 'value' => false, 'group' => 'auth', 'is_encrypted' => false],

            // Google OAuth
            ['key' => 'oauth.google.client_id',     'value' => null, 'group' => 'oauth_google', 'is_encrypted' => false],
            ['key' => 'oauth.google.client_secret', 'value' => null, 'group' => 'oauth_google', 'is_encrypted' => true],
            ['key' => 'oauth.google.redirect',      'value' => null, 'group' => 'oauth_google', 'is_encrypted' => false],

            // Microsoft OAuth
            ['key' => 'oauth.microsoft.client_id',     'value' => null,     'group' => 'oauth_microsoft', 'is_encrypted' => false],
            ['key' => 'oauth.microsoft.client_secret', 'value' => null,     'group' => 'oauth_microsoft', 'is_encrypted' => true],
            ['key' => 'oauth.microsoft.redirect',      'value' => null,     'group' => 'oauth_microsoft', 'is_encrypted' => false],
            ['key' => 'oauth.microsoft.tenant',        'value' => 'common', 'group' => 'oauth_microsoft', 'is_encrypted' => false],

            // SMTP
            ['key' => 'mail.smtp.enabled',    'value' => false, 'group' => 'mail', 'is_encrypted' => false],
            ['key' => 'mail.smtp.host',       'value' => null,  'group' => 'mail', 'is_encrypted' => false],
            ['key' => 'mail.smtp.port',       'value' => 587,   'group' => 'mail', 'is_encrypted' => false],
            ['key' => 'mail.smtp.username',   'value' => null,  'group' => 'mail', 'is_encrypted' => false],
            ['key' => 'mail.smtp.password',   'value' => null,  'group' => 'mail', 'is_encrypted' => true],
            ['key' => 'mail.smtp.encryption', 'value' => 'tls', 'group' => 'mail', 'is_encrypted' => false],
            ['key' => 'mail.from.address',    'value' => null,  'group' => 'mail', 'is_encrypted' => false],
            ['key' => 'mail.from.name',       'value' => null,  'group' => 'mail', 'is_encrypted' => false],

            // Paystack
            ['key' => 'paystack.public_key', 'value' => null, 'group' => 'paystack', 'is_encrypted' => false],
            ['key' => 'paystack.secret_key', 'value' => null, 'group' => 'paystack', 'is_encrypted' => true],
            ['key' => 'paystack.enabled',    'value' => false, 'group' => 'paystack', 'is_encrypted' => false],

            // Manual bank transfer details (shown to employer when paying offline)
            ['key' => 'bank.account_name',   'value' => null, 'group' => 'bank', 'is_encrypted' => false],
            ['key' => 'bank.account_number', 'value' => null, 'group' => 'bank', 'is_encrypted' => false],
            ['key' => 'bank.bank_name',      'value' => null, 'group' => 'bank', 'is_encrypted' => false],
            ['key' => 'bank.swift_code',     'value' => null, 'group' => 'bank', 'is_encrypted' => false],
            ['key' => 'bank.instructions',   'value' => null, 'group' => 'bank', 'is_encrypted' => false],

            // Reminder cadence (shared by CV-upload + profile-completion reminders)
            ['key' => 'reminders.first_after_days',  'value' => 3, 'group' => 'reminders', 'is_encrypted' => false],
            ['key' => 'reminders.repeat_every_days', 'value' => 7, 'group' => 'reminders', 'is_encrypted' => false],
            ['key' => 'reminders.max_count',         'value' => 3, 'group' => 'reminders', 'is_encrypted' => false],
            ['key' => 'reminders.profile_threshold_percent', 'value' => 70, 'group' => 'reminders', 'is_encrypted' => false],

            // Contact form subject -> recipient routing
            ['key' => 'contact.subject_routes', 'value' => ContactRoutes::DEFAULT_ROUTES,           'group' => 'contact_routing', 'is_encrypted' => false],
            ['key' => 'contact.default_email',  'value' => ContactRoutes::DEFAULT_FALLBACK_EMAIL,   'group' => 'contact_routing', 'is_encrypted' => false],
        ];

        foreach ($defaults as $row) {
            $existing = Setting::where('key', $row['key'])->first();
            if ($existing) {
                continue;
            }
            $setting = new Setting();
            $setting->key = $row['key'];
            $setting->group = $row['group'];
            $setting->is_encrypted = $row['is_encrypted'];
            $setting->value = $row['value'];
            $setting->save();
        }
    }
}
