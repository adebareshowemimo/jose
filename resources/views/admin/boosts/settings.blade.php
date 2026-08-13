@extends('admin.layouts.app')

@section('title', 'Boost Settings')
@section('page-title', 'Boost Settings')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#0A1929]">Boost Settings</h1>
        <p class="text-sm text-gray-500 mt-1">Who may buy a profile boost, and the limits that apply.</p>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.boosts.settings.update') }}" class="space-y-6 max-w-3xl">
        @csrf
        @method('PUT')

        {{-- Feature switch --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <h2 class="font-bold text-[#0A1929] mb-1">Availability</h2>
            <p class="text-xs text-gray-500 mb-4">Turning boosts off hides the sidebar link for candidates and makes the boost page return 404.</p>

            <label class="flex items-center gap-2.5 cursor-pointer">
                <input type="checkbox" name="boost_enabled" value="1" {{ $values['boost.enabled'] ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
                <span class="text-sm font-semibold text-gray-700">Boosts are available to candidates</span>
            </label>
        </div>

        {{-- Eligibility --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <h2 class="font-bold text-[#0A1929] mb-1">Eligibility</h2>
            <p class="text-xs text-gray-500 mb-4">Candidates who fail these are told why on the boost page, rather than hitting a dead button.</p>

            <div class="space-y-3">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="require_verified_email" value="1" {{ $values['boost.require_verified_email'] ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
                    <span class="text-sm text-gray-700">Require a verified email address</span>
                </label>

                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="require_cv" value="1" {{ $values['boost.require_cv'] ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
                    <span class="text-sm text-gray-700">Require an uploaded CV</span>
                </label>

                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="block_when_active" value="1" {{ $values['boost.block_when_active'] ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
                    <span class="text-sm text-gray-700">Block buying while a boost is already running</span>
                </label>
                <p class="text-xs text-gray-500 pl-7 -mt-1">Leave off to let candidates stack boosts and extend their run.</p>
            </div>

            <div class="mt-5 max-w-xs">
                <label for="min_profile_completion" class="block text-sm font-semibold text-gray-700 mb-1.5">Minimum profile completion</label>
                <div class="flex items-center gap-2">
                    <input id="min_profile_completion" type="number" name="min_profile_completion" min="0" max="100"
                           value="{{ old('min_profile_completion', $values['boost.min_profile_completion']) }}"
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none">
                    <span class="text-sm text-gray-500">%</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">0 disables this check.</p>
            </div>
        </div>

        {{-- Caps --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <h2 class="font-bold text-[#0A1929] mb-1">Limits</h2>
            <p class="text-xs text-gray-500 mb-4">Guardrails against indefinite stacking. Set any to 0 to disable it.</p>

            <div class="grid sm:grid-cols-3 gap-5">
                <div>
                    <label for="max_stacked_days" class="block text-sm font-semibold text-gray-700 mb-1.5">Max stacked days</label>
                    <input id="max_stacked_days" type="number" name="max_stacked_days" min="0" max="3650"
                           value="{{ old('max_stacked_days', $values['boost.max_stacked_days']) }}"
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none">
                </div>
                <div>
                    <label for="cooldown_days" class="block text-sm font-semibold text-gray-700 mb-1.5">Cooldown (days)</label>
                    <input id="cooldown_days" type="number" name="cooldown_days" min="0" max="365"
                           value="{{ old('cooldown_days', $values['boost.cooldown_days']) }}"
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none">
                </div>
                <div>
                    <label for="max_per_year" class="block text-sm font-semibold text-gray-700 mb-1.5">Max per year</label>
                    <input id="max_per_year" type="number" name="max_per_year" min="0" max="365"
                           value="{{ old('max_per_year', $values['boost.max_per_year']) }}"
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none">
                </div>
            </div>
        </div>

        {{-- Reminders --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <h2 class="font-bold text-[#0A1929] mb-1">Reminder emails</h2>
            <p class="text-xs text-gray-500 mb-4">Warns candidates before a boost lapses, and confirms once it has.</p>

            <label class="flex items-center gap-2.5 cursor-pointer">
                <input type="checkbox" name="reminders_enabled" value="1" {{ $values['boost.reminders_enabled'] ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
                <span class="text-sm text-gray-700">Send boost reminder emails</span>
            </label>

            <div class="mt-5 max-w-xs">
                <label for="reminder_lead_days" class="block text-sm font-semibold text-gray-700 mb-1.5">Warn this many days ahead</label>
                <input id="reminder_lead_days" type="number" name="reminder_lead_days" min="1" max="60"
                       value="{{ old('reminder_lead_days', $values['boost.reminder_lead_days']) }}"
                       class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none">
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-5 py-2.5 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-lg shadow transition">
                Save settings
            </button>
        </div>
    </form>
@endsection
