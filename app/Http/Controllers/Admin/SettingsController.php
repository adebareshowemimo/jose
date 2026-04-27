<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    public function index(Settings $settings)
    {
        return view('admin.settings.index', [
            'auth' => $settings->group('auth'),
            'google' => $settings->group('oauth_google'),
            'microsoft' => $settings->group('oauth_microsoft'),
            'mail' => $settings->group('mail'),
            'reminders' => $settings->group('reminders'),
            'paystack' => $settings->group('paystack'),
            'bank' => $settings->group('bank'),
        ]);
    }

    public function update(Request $request, Settings $settings)
    {
        $data = $request->validate([
            'auth_require_email_verification' => 'sometimes|boolean',

            'google_client_id'     => 'nullable|string|max:255',
            'google_client_secret' => 'nullable|string|max:512',
            'google_redirect'      => 'nullable|url|max:512',

            'microsoft_client_id'     => 'nullable|string|max:255',
            'microsoft_client_secret' => 'nullable|string|max:512',
            'microsoft_redirect'      => 'nullable|url|max:512',
            'microsoft_tenant'        => 'nullable|string|max:255',

            'smtp_enabled'    => 'sometimes|boolean',
            'smtp_host'       => 'nullable|string|max:255',
            'smtp_port'       => 'nullable|integer|min:1|max:65535',
            'smtp_username'   => 'nullable|string|max:255',
            'smtp_password'   => 'nullable|string|max:512',
            'smtp_encryption' => 'nullable|in:tls,ssl,null',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name'    => 'nullable|string|max:255',

            'reminders_first_after_days'         => 'nullable|integer|min:0|max:365',
            'reminders_repeat_every_days'        => 'nullable|integer|min:1|max:365',
            'reminders_max_count'                => 'nullable|integer|min:1|max:50',
            'reminders_profile_threshold_percent'=> 'nullable|integer|min:0|max:100',

            'paystack_enabled'      => 'sometimes|boolean',
            'paystack_public_key'   => 'nullable|string|max:255',
            'paystack_secret_key'   => 'nullable|string|max:512',

            'bank_account_name'   => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_bank_name'      => 'nullable|string|max:255',
            'bank_swift_code'     => 'nullable|string|max:50',
            'bank_instructions'   => 'nullable|string|max:2000',
        ]);

        $map = [
            'auth.require_email_verification' => ['auth', false, $request->boolean('auth_require_email_verification')],

            'oauth.google.client_id'     => ['oauth_google', false, $data['google_client_id'] ?? null],
            'oauth.google.client_secret' => ['oauth_google', true,  $data['google_client_secret'] ?? null],
            'oauth.google.redirect'      => ['oauth_google', false, $data['google_redirect'] ?? null],

            'oauth.microsoft.client_id'     => ['oauth_microsoft', false, $data['microsoft_client_id'] ?? null],
            'oauth.microsoft.client_secret' => ['oauth_microsoft', true,  $data['microsoft_client_secret'] ?? null],
            'oauth.microsoft.redirect'      => ['oauth_microsoft', false, $data['microsoft_redirect'] ?? null],
            'oauth.microsoft.tenant'        => ['oauth_microsoft', false, $data['microsoft_tenant'] ?? 'common'],

            'mail.smtp.enabled'    => ['mail', false, $request->boolean('smtp_enabled')],
            'mail.smtp.host'       => ['mail', false, $data['smtp_host'] ?? null],
            'mail.smtp.port'       => ['mail', false, $data['smtp_port'] ?? null],
            'mail.smtp.username'   => ['mail', false, $data['smtp_username'] ?? null],
            'mail.smtp.password'   => ['mail', true,  $data['smtp_password'] ?? null],
            'mail.smtp.encryption' => ['mail', false, $data['smtp_encryption'] ?? null],
            'mail.from.address'    => ['mail', false, $data['mail_from_address'] ?? null],
            'mail.from.name'       => ['mail', false, $data['mail_from_name'] ?? null],

            'reminders.first_after_days'          => ['reminders', false, $data['reminders_first_after_days'] ?? 3],
            'reminders.repeat_every_days'         => ['reminders', false, $data['reminders_repeat_every_days'] ?? 7],
            'reminders.max_count'                 => ['reminders', false, $data['reminders_max_count'] ?? 3],
            'reminders.profile_threshold_percent' => ['reminders', false, $data['reminders_profile_threshold_percent'] ?? 70],

            'paystack.enabled'    => ['paystack', false, $request->boolean('paystack_enabled')],
            'paystack.public_key' => ['paystack', false, $data['paystack_public_key'] ?? null],
            'paystack.secret_key' => ['paystack', true,  $data['paystack_secret_key'] ?? null],

            'bank.account_name'   => ['bank', false, $data['bank_account_name'] ?? null],
            'bank.account_number' => ['bank', false, $data['bank_account_number'] ?? null],
            'bank.bank_name'      => ['bank', false, $data['bank_bank_name'] ?? null],
            'bank.swift_code'     => ['bank', false, $data['bank_swift_code'] ?? null],
            'bank.instructions'   => ['bank', false, $data['bank_instructions'] ?? null],
        ];

        foreach ($map as $key => [$group, $encrypted, $value]) {
            // For password/secret fields: don't overwrite stored value with empty input.
            if ($encrypted && ($value === null || $value === '')) {
                continue;
            }
            $row = Setting::firstOrNew(['key' => $key]);
            $row->group = $group;
            $row->is_encrypted = $encrypted;
            $row->value = $value;
            $row->save();
        }

        $settings->flush();

        return back()->with('success', 'Settings updated successfully.');
    }

    public function testMail(Request $request)
    {
        $data = $request->validate([
            'test_recipient' => 'required|email|max:255',
        ]);

        try {
            Mail::raw(
                "This is a test email from " . config('app.name', 'Jose Ocean Jobs') . ".\n\n"
                . "If you received this, your SMTP configuration is working.\n\n"
                . "Sent at: " . now()->toDateTimeString() . " UTC\n"
                . "From: " . config('mail.from.address') . " (" . config('mail.from.name') . ")\n"
                . "Mailer: " . config('mail.default') . "\n"
                . "SMTP host: " . config('mail.mailers.smtp.host') . ":" . config('mail.mailers.smtp.port'),
                function ($message) use ($data) {
                    $message->to($data['test_recipient'])
                            ->subject('SMTP test from ' . config('app.name', 'Jose Ocean Jobs'));
                }
            );
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Test email sent to ' . $data['test_recipient'] . '. Check the inbox (and spam folder).',
        ]);
    }
}
