@extends('admin.layouts.app')

@section('title', 'Settings')

@section('content')
<div class="max-w-5xl mx-auto" x-data="{ tab: 'auth' }">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#0A1929]">Settings</h1>
        <p class="text-sm text-gray-500 mt-1">Manage authentication, OAuth keys, and email delivery for the platform.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Tabs --}}
    <div class="bg-white rounded-xl border border-gray-200 mb-6">
        <div class="flex flex-wrap gap-1 p-2 border-b border-gray-200">
            <button type="button" @click="tab = 'auth'" :class="tab === 'auth' ? 'bg-[#1AAD94] text-white' : 'text-gray-600 hover:bg-gray-100'" class="px-4 py-2 rounded-lg text-sm font-semibold transition">Authentication</button>
            <button type="button" @click="tab = 'google'" :class="tab === 'google' ? 'bg-[#1AAD94] text-white' : 'text-gray-600 hover:bg-gray-100'" class="px-4 py-2 rounded-lg text-sm font-semibold transition">Google OAuth</button>
            <button type="button" @click="tab = 'microsoft'" :class="tab === 'microsoft' ? 'bg-[#1AAD94] text-white' : 'text-gray-600 hover:bg-gray-100'" class="px-4 py-2 rounded-lg text-sm font-semibold transition">Microsoft OAuth</button>
            <button type="button" @click="tab = 'mail'" :class="tab === 'mail' ? 'bg-[#1AAD94] text-white' : 'text-gray-600 hover:bg-gray-100'" class="px-4 py-2 rounded-lg text-sm font-semibold transition">Email / SMTP</button>
            <button type="button" @click="tab = 'reminders'" :class="tab === 'reminders' ? 'bg-[#1AAD94] text-white' : 'text-gray-600 hover:bg-gray-100'" class="px-4 py-2 rounded-lg text-sm font-semibold transition">Reminders</button>
            <button type="button" @click="tab = 'paystack'" :class="tab === 'paystack' ? 'bg-[#1AAD94] text-white' : 'text-gray-600 hover:bg-gray-100'" class="px-4 py-2 rounded-lg text-sm font-semibold transition">Paystack</button>
            <button type="button" @click="tab = 'bank'" :class="tab === 'bank' ? 'bg-[#1AAD94] text-white' : 'text-gray-600 hover:bg-gray-100'" class="px-4 py-2 rounded-lg text-sm font-semibold transition">Bank Transfer</button>
            <button type="button" @click="tab = 'receipt'" :class="tab === 'receipt' ? 'bg-[#1AAD94] text-white' : 'text-gray-600 hover:bg-gray-100'" class="px-4 py-2 rounded-lg text-sm font-semibold transition">Receipt Template</button>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            {{-- Auth tab --}}
            <div x-show="tab === 'auth'" class="space-y-6">
                <div>
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="hidden" name="auth_require_email_verification" value="0">
                        <input type="checkbox" name="auth_require_email_verification" value="1"
                               class="mt-1 w-4 h-4 rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]"
                               {{ ($auth['auth.require_email_verification'] ?? false) ? 'checked' : '' }} />
                        <div>
                            <div class="font-semibold text-[#0A1929]">Force email confirmation after registration</div>
                            <div class="text-sm text-gray-500">When enabled, newly registered users must click the verification link sent to their email before being redirected to their dashboard. Existing accounts are unaffected.</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Google tab --}}
            <div x-show="tab === 'google'" x-cloak class="space-y-5">
                <p class="text-sm text-gray-600">Get these from <span class="font-mono text-xs bg-gray-100 px-1.5 py-0.5 rounded">console.cloud.google.com</span> &rarr; APIs &amp; Services &rarr; Credentials.</p>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Client ID</label>
                    <input type="text" name="google_client_id" value="{{ $google['oauth.google.client_id'] ?? '' }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Client Secret <span class="text-gray-400 font-normal normal-case">(leave blank to keep existing)</span></label>
                    <input type="password" name="google_client_secret" value="" autocomplete="new-password" placeholder="{{ ($google['oauth.google.client_secret'] ?? null) ? '•••••••• (saved)' : '' }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Redirect URI</label>
                    <input type="url" name="google_redirect" value="{{ $google['oauth.google.redirect'] ?? url('/auth/google/callback') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    <p class="mt-1 text-xs text-gray-500">Register this exact URL in your Google OAuth client. Suggested: <span class="font-mono">{{ url('/auth/google/callback') }}</span></p>
                </div>
            </div>

            {{-- Microsoft tab --}}
            <div x-show="tab === 'microsoft'" x-cloak class="space-y-5">
                <p class="text-sm text-gray-600">Get these from <span class="font-mono text-xs bg-gray-100 px-1.5 py-0.5 rounded">portal.azure.com</span> &rarr; Microsoft Entra ID &rarr; App registrations.</p>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Application (Client) ID</label>
                    <input type="text" name="microsoft_client_id" value="{{ $microsoft['oauth.microsoft.client_id'] ?? '' }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Client Secret <span class="text-gray-400 font-normal normal-case">(leave blank to keep existing)</span></label>
                    <input type="password" name="microsoft_client_secret" value="" autocomplete="new-password" placeholder="{{ ($microsoft['oauth.microsoft.client_secret'] ?? null) ? '•••••••• (saved)' : '' }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Redirect URI</label>
                    <input type="url" name="microsoft_redirect" value="{{ $microsoft['oauth.microsoft.redirect'] ?? url('/auth/microsoft/callback') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    <p class="mt-1 text-xs text-gray-500">Register this exact URL in your Azure app. Suggested: <span class="font-mono">{{ url('/auth/microsoft/callback') }}</span></p>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Tenant</label>
                    <input type="text" name="microsoft_tenant" value="{{ $microsoft['oauth.microsoft.tenant'] ?? 'common' }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    <p class="mt-1 text-xs text-gray-500">Use <span class="font-mono">common</span> (any account), <span class="font-mono">organizations</span> (work/school only), <span class="font-mono">consumers</span> (personal only), or a specific tenant GUID.</p>
                </div>
            </div>

            {{-- Mail tab --}}
            <div x-show="tab === 'mail'" x-cloak class="space-y-5">
                <div>
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="hidden" name="smtp_enabled" value="0">
                        <input type="checkbox" name="smtp_enabled" value="1"
                               class="mt-1 w-4 h-4 rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]"
                               {{ ($mail['mail.smtp.enabled'] ?? false) ? 'checked' : '' }} />
                        <div>
                            <div class="font-semibold text-[#0A1929]">Use SMTP for outgoing email</div>
                            <div class="text-sm text-gray-500">When off, outgoing email uses the default driver from <span class="font-mono">.env</span> (currently: <span class="font-mono">{{ config('mail.default') }}</span>).</div>
                        </div>
                    </label>
                </div>
                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">SMTP Host</label>
                        <input type="text" name="smtp_host" value="{{ $mail['mail.smtp.host'] ?? '' }}" placeholder="smtp.example.com"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">SMTP Port</label>
                        <input type="number" name="smtp_port" value="{{ $mail['mail.smtp.port'] ?? 587 }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Username</label>
                        <input type="text" name="smtp_username" value="{{ $mail['mail.smtp.username'] ?? '' }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Password <span class="text-gray-400 font-normal normal-case">(leave blank to keep existing)</span></label>
                        <input type="password" name="smtp_password" value="" autocomplete="new-password" placeholder="{{ ($mail['mail.smtp.password'] ?? null) ? '•••••••• (saved)' : '' }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Encryption</label>
                        <select name="smtp_encryption" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">
                            @foreach (['tls' => 'TLS', 'ssl' => 'SSL', 'null' => 'None'] as $val => $label)
                                <option value="{{ $val }}" {{ ($mail['mail.smtp.encryption'] ?? 'tls') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-5 pt-4 border-t border-gray-100">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">From Address</label>
                        <input type="email" name="mail_from_address" value="{{ $mail['mail.from.address'] ?? '' }}" placeholder="no-reply@yourdomain.com"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">From Name</label>
                        <input type="text" name="mail_from_name" value="{{ $mail['mail.from.name'] ?? '' }}" placeholder="Jose Consulting Limited"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    </div>
                </div>

                {{-- Test SMTP --}}
                <div class="pt-4 mt-2 border-t border-gray-100"
                     x-data="{
                         recipient: '{{ auth()->user()->email ?? '' }}',
                         loading: false,
                         result: null,
                         async send() {
                             if (! this.recipient) return;
                             this.loading = true;
                             this.result = null;
                             try {
                                 const res = await fetch('{{ route('admin.settings.mail.test') }}', {
                                     method: 'POST',
                                     headers: {
                                         'Content-Type': 'application/json',
                                         'Accept': 'application/json',
                                         'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                     },
                                     body: JSON.stringify({ test_recipient: this.recipient }),
                                 });
                                 const data = await res.json();
                                 this.result = { ok: res.ok && data.success, message: data.message || (res.ok ? 'Sent.' : 'Request failed.') };
                             } catch (e) {
                                 this.result = { ok: false, message: 'Network error: ' + e.message };
                             } finally {
                                 this.loading = false;
                             }
                         }
                     }">
                    <div class="flex items-end gap-3 flex-wrap">
                        <div class="flex-1 min-w-[260px]">
                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Send Test Email</label>
                            <input type="email" x-model="recipient" placeholder="recipient@example.com"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                        </div>
                        <button type="button" @click="send()" :disabled="loading || !recipient"
                                :class="loading ? 'opacity-60 cursor-not-allowed' : ''"
                                class="px-5 py-2.5 bg-[#073057] hover:bg-[#0a4275] text-white text-sm font-semibold rounded-lg transition">
                            <span x-show="!loading">Send Test</span>
                            <span x-show="loading">Sending…</span>
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Save your changes first — the test uses the currently saved SMTP settings, not unsaved form values.</p>
                    <template x-if="result">
                        <div class="mt-3 rounded-lg px-4 py-3 text-sm"
                             :class="result.ok ? 'bg-emerald-50 border border-emerald-200 text-emerald-700' : 'bg-red-50 border border-red-200 text-red-700'"
                             x-text="result.message"></div>
                    </template>
                </div>
            </div>

            {{-- Reminders tab --}}
            <div x-show="tab === 'reminders'" x-cloak class="space-y-5">
                <p class="text-sm text-gray-600">Cadence for the CV-upload and profile-completion reminder emails. The scheduled task runs daily and sends per these rules.</p>
                <div class="grid md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">First reminder after (days)</label>
                        <input type="number" min="0" max="365" name="reminders_first_after_days" value="{{ $reminders['reminders.first_after_days'] ?? 3 }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                        <p class="mt-1 text-xs text-gray-500">Wait this many days after registration before the first reminder.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Repeat every (days)</label>
                        <input type="number" min="1" max="365" name="reminders_repeat_every_days" value="{{ $reminders['reminders.repeat_every_days'] ?? 7 }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                        <p class="mt-1 text-xs text-gray-500">Interval between subsequent reminders for the same user.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Max reminders per user</label>
                        <input type="number" min="1" max="50" name="reminders_max_count" value="{{ $reminders['reminders.max_count'] ?? 3 }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                        <p class="mt-1 text-xs text-gray-500">Stop reminding once this many have been sent.</p>
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-100">
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Profile completion threshold (%)</label>
                    <input type="number" min="0" max="100" name="reminders_profile_threshold_percent" value="{{ $reminders['reminders.profile_threshold_percent'] ?? 70 }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none max-w-xs" />
                    <p class="mt-1 text-xs text-gray-500">Profile-completion reminders go to candidates whose profile is below this percent.</p>
                </div>
                <div class="rounded-lg bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 text-xs">
                    <strong>Cron:</strong> Make sure <span class="font-mono">php artisan schedule:run</span> runs every minute (Linux cron / Windows Task Scheduler). Without it, reminders won't fire.
                </div>
            </div>

            {{-- Paystack tab --}}
            <div x-show="tab === 'paystack'" x-cloak class="space-y-5">
                <p class="text-sm text-gray-600">Get keys from <span class="font-mono text-xs bg-gray-100 px-1.5 py-0.5 rounded">dashboard.paystack.com</span> &rarr; Settings &rarr; API Keys &amp; Webhooks. Use test keys (<span class="font-mono">pk_test_</span>, <span class="font-mono">sk_test_</span>) until ready for live.</p>

                <div>
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="hidden" name="paystack_enabled" value="0">
                        <input type="checkbox" name="paystack_enabled" value="1"
                               class="mt-1 w-4 h-4 rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]"
                               {{ ($paystack['paystack.enabled'] ?? false) ? 'checked' : '' }} />
                        <div>
                            <div class="font-semibold text-[#0A1929]">Enable Paystack online payment</div>
                            <div class="text-sm text-gray-500">When on, the "Pay with Paystack" option appears on the order page. Manual bank transfer remains available regardless.</div>
                        </div>
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Public Key</label>
                    <input type="text" name="paystack_public_key" value="{{ $paystack['paystack.public_key'] ?? '' }}" placeholder="pk_test_..."
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Secret Key <span class="text-gray-400 font-normal normal-case">(leave blank to keep existing)</span></label>
                    <input type="password" name="paystack_secret_key" value="" autocomplete="new-password" placeholder="{{ ($paystack['paystack.secret_key'] ?? null) ? '•••••••• (saved)' : 'sk_test_...' }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                </div>
                <div class="rounded-lg bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 text-xs">
                    <strong>Webhook URL:</strong> <span class="font-mono">{{ url('/payment/paystack/callback') }}</span> &mdash; add this in your Paystack dashboard for instant payment confirmations.
                </div>
            </div>

            {{-- Bank tab --}}
            <div x-show="tab === 'bank'" x-cloak class="space-y-5">
                <p class="text-sm text-gray-600">These details show on the employer's order page when they choose to pay via bank transfer. Leave blank to disable manual transfer.</p>
                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Bank Name</label>
                        <input type="text" name="bank_bank_name" value="{{ $bank['bank.bank_name'] ?? '' }}" placeholder="Zenith Bank"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Account Name</label>
                        <input type="text" name="bank_account_name" value="{{ $bank['bank.account_name'] ?? '' }}" placeholder="Jose Consulting Limited"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Account Number</label>
                        <input type="text" name="bank_account_number" value="{{ $bank['bank.account_number'] ?? '' }}" placeholder="1234567890"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">SWIFT / BIC (optional)</label>
                        <input type="text" name="bank_swift_code" value="{{ $bank['bank.swift_code'] ?? '' }}" placeholder="ZEIBNGLA"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Additional Instructions</label>
                    <textarea name="bank_instructions" rows="3" placeholder="Please include the order number as the transfer reference..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">{{ $bank['bank.instructions'] ?? '' }}</textarea>
                </div>
            </div>

            {{-- Receipt template tab --}}
            <div x-show="tab === 'receipt'" x-cloak class="space-y-5">
                <p class="text-sm text-gray-600">These details appear on PDF receipts issued from <span class="font-mono">/admin/payments</span>. The layout is fixed; only the values below change.</p>

                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Business Name</label>
                        <input type="text" name="receipt_business_name" value="{{ $receipt['receipt.business_name'] ?? config('app.name') }}" placeholder="Jose Consulting Limited"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Tax / VAT ID (optional)</label>
                        <input type="text" name="receipt_tax_id" value="{{ $receipt['receipt.tax_id'] ?? '' }}" placeholder="RC 123456 / VAT 123-456-789"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Business Address</label>
                        <textarea name="receipt_business_address" rows="2" placeholder="Street, City, State, Postcode, Country"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">{{ $receipt['receipt.business_address'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Phone</label>
                        <input type="text" name="receipt_business_phone" value="{{ $receipt['receipt.business_phone'] ?? '' }}" placeholder="+234 ..."
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Email</label>
                        <input type="email" name="receipt_business_email" value="{{ $receipt['receipt.business_email'] ?? '' }}" placeholder="billing@yourdomain.com"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Logo</label>
                    @if (! empty($receipt['receipt.logo_path']))
                        <div class="flex items-center gap-4 mb-3">
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($receipt['receipt.logo_path']) }}" alt="Receipt logo" class="h-16 w-auto rounded border border-gray-200 bg-gray-50 p-2" />
                            <label class="flex items-center gap-2 text-sm text-red-600 cursor-pointer">
                                <input type="hidden" name="receipt_remove_logo" value="0">
                                <input type="checkbox" name="receipt_remove_logo" value="1" class="w-4 h-4 rounded border-gray-300 text-red-600">
                                Remove this logo
                            </label>
                        </div>
                    @endif
                    <input type="file" name="receipt_logo" accept="image/png,image/jpeg,image/svg+xml"
                           class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#073057] file:text-white hover:file:bg-[#0a4275]" />
                    <p class="mt-1 text-xs text-gray-500">PNG, JPG or SVG. Max 2MB. Will display at the top-left of every receipt PDF.</p>
                </div>

                <div class="grid md:grid-cols-2 gap-5 pt-4 border-t border-gray-100">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Header Note (optional)</label>
                        <textarea name="receipt_header_note" rows="2" placeholder="A short message to show under the title."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">{{ $receipt['receipt.header_note'] ?? '' }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Footer / Terms</label>
                        <textarea name="receipt_footer_text" rows="3" placeholder="Thank you for your business. This is a computer-generated receipt and does not require a signature."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">{{ $receipt['receipt.footer_text'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Signature Label</label>
                        <input type="text" name="receipt_signature_label" value="{{ $receipt['receipt.signature_label'] ?? 'Authorised Signature' }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Receipt Number Prefix</label>
                        <input type="text" name="receipt_number_prefix" value="{{ $receipt['receipt.number_prefix'] ?? 'RCP-' }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                        <p class="mt-1 text-xs text-gray-500">Receipt numbers will look like <span class="font-mono">{{ ($receipt['receipt.number_prefix'] ?? 'RCP-') }}000123</span>.</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-[#073057] text-white rounded-lg font-semibold hover:brightness-110 transition shadow">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
