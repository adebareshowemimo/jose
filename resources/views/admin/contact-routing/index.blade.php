@extends('admin.layouts.app')

@section('title', 'Contact Routing')
@section('page-title', 'Contact Routing')

@php
    $oldSubjects = old('subjects');
    $rows = is_array($oldSubjects) ? $oldSubjects : $subjects;
@endphp

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#0A1929]">Contact Routing</h1>
        <p class="text-sm text-gray-500 mt-1">Map each contact-form subject to the team email that should receive submissions. The submitter's auto-response will use the same team address as Reply-To.</p>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.contact-routing.update') }}" class="space-y-6 max-w-4xl"
          x-data='@json(['rows' => array_values($rows)])'>
        @csrf
        @method('PUT')

        {{-- Default fallback --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-br from-[#073057] to-[#0a4275] text-white">
                <h2 class="text-base font-bold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Default fallback email
                </h2>
                <p class="text-xs text-white/70 mt-0.5">Used when a subject has no specific mapping or the form receives an unexpected subject value.</p>
            </div>
            <div class="p-6">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Fallback recipient</label>
                <input type="email" name="default_email" required
                       value="{{ old('default_email', $defaultEmail) }}"
                       placeholder="info@joseoceanjobs.com"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
            </div>
        </div>

        {{-- Subject mapping repeater --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-[#0A1929]">Subject &rarr; Email mapping</h2>
                    <p class="text-xs text-gray-500 mt-0.5">These labels populate the Subject dropdown on the public contact form. Submissions are routed to the email next to each subject.</p>
                </div>
                <button type="button" @click="rows.push({ label: '', email: '' })"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold uppercase tracking-widest text-[#1AAD94] border border-[#1AAD94]/30 hover:bg-[#1AAD94]/10 rounded-lg transition shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add subject
                </button>
            </div>
            <div class="p-6 space-y-3">
                <template x-for="(row, idx) in rows" :key="idx">
                    <div class="grid grid-cols-1 md:grid-cols-[1fr_1fr_auto] gap-3 items-start">
                        <div>
                            <label class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 mb-1 block md:hidden">Subject label</label>
                            <input type="text" :name="`subjects[${idx}][label]`" x-model="row.label"
                                   placeholder="e.g. Marine Insurance" required maxlength="255"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                        </div>
                        <div>
                            <label class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 mb-1 block md:hidden">Recipient email</label>
                            <input type="email" :name="`subjects[${idx}][email]`" x-model="row.email"
                                   placeholder="team@joseoceanjobs.com" required maxlength="255"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                        </div>
                        <button type="button" @click="rows.splice(idx, 1)"
                                :disabled="rows.length === 1"
                                class="inline-flex items-center justify-center w-10 h-10 text-red-500 hover:bg-red-50 rounded-lg transition disabled:opacity-30 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                        </button>
                    </div>
                </template>

                <div x-show="rows.length === 0" class="text-sm text-gray-400 italic text-center py-4">
                    No subjects configured. Click "Add subject" to create one.
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:justify-between gap-3">
            <button type="button"
                    onclick="if (confirm('Reset all subjects and the fallback email to the seeded defaults?')) document.getElementById('reset-form').submit();"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-gray-500 border border-gray-200 hover:bg-gray-50 rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Reset to defaults
            </button>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#073057] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-lg transition shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Save changes
            </button>
        </div>
    </form>

    <form id="reset-form" method="POST" action="{{ route('admin.contact-routing.update') }}" class="hidden">
        @csrf
        @method('PUT')
        <input type="hidden" name="reset" value="1">
    </form>
@endsection
