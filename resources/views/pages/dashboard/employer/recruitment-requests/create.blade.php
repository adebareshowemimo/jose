@extends('layouts.dashboard')

@section('title', 'New Recruitment Request')
@section('page-title', 'New Recruitment Request')

@section('sidebar-nav')
    @include('pages.dashboard.employer.partials.sidebar')
@endsection

@section('content')
<div class="mb-6">
    <a href="{{ route('employer.recruitment-requests.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-gray-400 hover:text-gray-600 mb-2">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to requests
    </a>
    <h2 class="text-2xl font-bold text-[#073057]">Tell us who you're looking for</h2>
    <p class="text-[#6B7280]">We'll review and get back with a quote within one business day.</p>
</div>

@if ($errors->any())
    <div class="mb-5 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
        @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
@endif

<form method="POST" action="{{ route('employer.recruitment-requests.store') }}" enctype="multipart/form-data" class="space-y-6"
      x-data="{ submitting: false }" @submit="submitting = true">
    @csrf

    {{-- Service tier --}}
    <div class="bg-white rounded-xl border border-[#E5E7EB] p-6" x-data="{ tier: '{{ old('service_type', 'cv_sourcing') }}' }">
        <h3 class="text-base font-bold text-[#073057] mb-4">Choose a service tier</h3>
        <div class="grid md:grid-cols-3 gap-3">
            @foreach (\App\Models\RecruitmentRequest::SERVICE_TYPES as $val => $label)
                @php
                    $blurb = match($val) {
                        'cv_sourcing' => 'We send you matching CVs. You handle screening + interviews.',
                        'partial_recruitment' => 'We source and pre-screen candidates. You make the final call.',
                        'full_recruitment' => 'End-to-end: sourcing, screening, interviews, offer support.',
                    };
                @endphp
                <label class="cursor-pointer block">
                    <input type="radio" name="service_type" value="{{ $val }}" required x-model="tier" class="sr-only" />
                    <div :class="tier === '{{ $val }}' ? 'border-[#1AAD94] bg-[#1AAD94]/5 ring-2 ring-[#1AAD94]/30' : 'border-[#E5E7EB]'"
                         class="p-4 rounded-xl border-2 hover:border-[#1AAD94]/60 transition h-full">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <div class="font-semibold text-[#073057]">{{ $label }}</div>
                            <div x-show="tier === '{{ $val }}'" class="text-[#1AAD94]">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            </div>
                        </div>
                        <div class="text-xs text-[#6B7280] leading-relaxed">{{ $blurb }}</div>
                    </div>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Role details --}}
    <div class="bg-white rounded-xl border border-[#E5E7EB] p-6 space-y-5">
        <h3 class="text-base font-bold text-[#073057]">Role details</h3>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Job title <span class="text-red-500">*</span></label>
                <input type="text" name="job_title" value="{{ old('job_title') }}" required placeholder="e.g. Chief Officer — Container Vessel"
                       class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Number of CVs <span class="text-red-500">*</span></label>
                <input type="number" name="cv_count" value="{{ old('cv_count', 5) }}" min="1" max="50" required
                       class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Category</label>
                <select name="category_id" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">
                    <option value="">— Any —</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (int) old('category_id') === $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Job type</label>
                <select name="job_type_id" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">
                    <option value="">— Any —</option>
                    @foreach ($jobTypes as $jt)
                        <option value="{{ $jt->id }}" {{ (int) old('job_type_id') === $jt->id ? 'selected' : '' }}>{{ $jt->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Country</label>
                <select name="location_id" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}" {{ (int) old('location_id', $defaultCountryId ?? 0) === $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Experience level</label>
                <select name="experience_level" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">
                    <option value="">— Any —</option>
                    @foreach (['entry' => 'Entry level', '1-3 yrs' => '1–3 years', '3-5 yrs' => '3–5 years', '5+ yrs' => '5+ years', 'senior' => 'Senior / Leadership'] as $val => $label)
                        <option value="{{ $val }}" {{ old('experience_level') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Salary min</label>
                <input type="number" name="salary_min" value="{{ old('salary_min') }}" step="0.01" min="0" placeholder="0.00"
                       class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Salary max</label>
                <input type="number" name="salary_max" value="{{ old('salary_max') }}" step="0.01" min="0" placeholder="0.00"
                       class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Currency</label>
                @php $defaultCurrency = \App\Support\Currency::default(); @endphp
                <select name="salary_currency" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">
                    @foreach (\App\Support\Currency::ALLOWED as $cur)
                        <option value="{{ $cur }}" {{ old('salary_currency', $defaultCurrency) === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Needed by (optional)</label>
                <input type="date" name="needed_by" value="{{ old('needed_by') }}"
                       class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
            </div>
        </div>

        {{-- Skills (repeatable rows) --}}
        <div x-data="{
                rows: {{ \Illuminate\Support\Js::from(
                    old('skills')
                        ? array_values(array_filter(old('skills'), fn($s) => trim((string) $s) !== ''))
                        : ['']
                ) }},
                add() { this.rows.push(''); },
                remove(i) { this.rows.splice(i, 1); if (this.rows.length === 0) this.rows.push(''); },
            }">
            <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Skills</label>
            <div class="space-y-2">
                <template x-for="(row, i) in rows" :key="i">
                    <div class="flex items-center gap-2">
                        <input type="text" :name="`skills[${i}]`" x-model="rows[i]" placeholder="e.g. Leadership, Teamwork"
                               class="flex-1 px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                        <button type="button" @click="remove(i)"
                                class="shrink-0 inline-flex items-center justify-center h-10 w-10 rounded-lg border border-[#E5E7EB] text-gray-400 hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition cursor-pointer"
                                :title="'Remove skill'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
            </div>
            <button type="button" @click="add()"
                    class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-[#1AAD94] hover:text-[#073057] cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Add skill
            </button>
        </div>

        {{-- Certificates (repeatable rows) --}}
        <div x-data="{
                rows: {{ \Illuminate\Support\Js::from(
                    old('certificates')
                        ? array_values(array_filter(old('certificates'), fn($r) =>
                            ($r['name'] ?? '') !== '' || ($r['vendor'] ?? '') !== ''
                            || ($r['issued_at'] ?? '') !== '' || ($r['expires_at'] ?? '') !== ''))
                        : [['name' => '', 'vendor' => '', 'issued_at' => '', 'expires_at' => '']]
                ) }},
                add() { this.rows.push({ name: '', vendor: '', issued_at: '', expires_at: '' }); },
                remove(i) {
                    this.rows.splice(i, 1);
                    if (this.rows.length === 0) this.rows.push({ name: '', vendor: '', issued_at: '', expires_at: '' });
                },
            }">
            <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Certificates</label>
            <p class="text-xs text-gray-400 mb-3">List the certifications candidates must hold — name, issuing organisation, date received, and expiry.</p>
            <div class="space-y-3">
                <template x-for="(row, i) in rows" :key="i">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-start p-3 rounded-lg border border-[#E5E7EB] bg-gray-50/50">
                        <div class="md:col-span-4">
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-1">Name</label>
                            <input type="text" :name="`certificates[${i}][name]`" x-model="rows[i].name" placeholder="e.g. STCW Basic Safety Training"
                                   class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none bg-white" />
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-1">Issuing organisation</label>
                            <input type="text" :name="`certificates[${i}][vendor]`" x-model="rows[i].vendor" placeholder="e.g. MCA, IMO, Lloyd's"
                                   class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none bg-white" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-1">Date received</label>
                            <input type="date" :name="`certificates[${i}][issued_at]`" x-model="rows[i].issued_at"
                                   class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none bg-white" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-1">Expiry date</label>
                            <input type="date" :name="`certificates[${i}][expires_at]`" x-model="rows[i].expires_at"
                                   class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none bg-white" />
                        </div>
                        <div class="md:col-span-1 flex md:items-end md:justify-end md:h-full">
                            <button type="button" @click="remove(i)"
                                    class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-[#E5E7EB] text-gray-400 hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition cursor-pointer bg-white"
                                    title="Remove certificate">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
            <button type="button" @click="add()"
                    class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-[#1AAD94] hover:text-[#073057] cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Add certificate
            </button>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Description / requirements <span class="text-red-500">*</span></label>
            <textarea name="description" rows="6" required placeholder="Describe the role, key responsibilities, must-have certifications, and anything else our team should know..."
                      class="w-full px-4 py-3 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Job description file (optional)</label>
            <input type="file" name="jd_file" accept=".pdf,.doc,.docx"
                   class="block w-full text-sm text-[#4B5563] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-[#073057] file:text-white file:font-semibold file:text-xs hover:file:brightness-110" />
            <p class="mt-1 text-xs text-gray-400">PDF, DOC, or DOCX up to 5 MB.</p>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('employer.recruitment-requests.index') }}" class="px-5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm font-semibold text-[#4B5563] hover:bg-gray-50 cursor-pointer">Cancel</a>
        <button type="submit" :disabled="submitting"
                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#073057] text-white rounded-xl text-sm font-bold uppercase tracking-widest hover:brightness-110 active:brightness-90 active:scale-[0.98] transition-all shadow cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:brightness-100 disabled:active:scale-100">
            <svg x-show="submitting" x-cloak class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"></circle>
                <path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            <span x-text="submitting ? 'Submitting…' : 'Submit Request'">Submit Request</span>
        </button>
    </div>
</form>
@endsection
