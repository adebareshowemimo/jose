@extends('layouts.dashboard')

@php
    $name = $candidate->user?->name ?? 'Candidate';
@endphp

@section('title', $name . ' — Candidate')
@section('page-title', 'Candidate Profile')

@section('sidebar-nav')
    @include('pages.dashboard.employer.partials.sidebar')
@endsection

@section('content')
@php
    $title = $candidate->title;
    $locationName = $candidate->location?->name ?? $candidate->address;
    $skills = ! empty($candidate->skills_list) ? $candidate->skills_list : $candidate->skills->pluck('name')->all();
    $experience = is_array($candidate->experience) ? $candidate->experience : [];
    $education = is_array($candidate->education) ? $candidate->education : [];
    $awards = is_array($candidate->awards) ? $candidate->awards : [];
    $languages = is_array($candidate->languages) ? $candidate->languages : [];
    $social = is_array($candidate->social_links) ? $candidate->social_links : [];
    $cvs = $candidate->resumes->filter(fn ($r) => ! empty($r->file_path))->values();
    $cv = $cvs->first();
    $fmt = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->format('M Y') : null;

    $email = $candidate->user?->email;
    $phone = $candidate->user?->phone;
    $dob = $candidate->date_of_birth;
    $age = $dob ? $dob->age : null;
    $categories = $candidate->categories;
    $memberSince = $candidate->user?->created_at ?? $candidate->created_at;
    $hasDetails = $candidate->gender || $dob || $candidate->education_level
        || $candidate->expected_salary || $candidate->salary_type || $categories->isNotEmpty() || $memberSince;
@endphp

<div x-data="{ inviteOpen: false }">
    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('employer.recruitment-requests.index') }}"
       class="inline-flex items-center gap-1 text-xs font-semibold text-gray-400 hover:text-gray-600 mb-4">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back
    </a>

    @if (session('success'))
        <div class="mb-5 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-5 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-5 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <div class="grid lg:grid-cols-[1fr_340px] gap-6">
        {{-- Main column --}}
        <div class="space-y-6">
            {{-- Identity card --}}
            <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 md:p-8">
                <div class="flex items-start gap-5">
                    <div class="w-20 h-20 rounded-2xl bg-[#073057]/10 flex items-center justify-center text-2xl font-extrabold text-[#073057] shrink-0 overflow-hidden">
                        @if ($candidate->user?->avatar)
                            <img src="{{ \Illuminate\Support\Str::startsWith($candidate->user->avatar, ['http', '/']) ? $candidate->user->avatar : asset($candidate->user->avatar) }}" alt="{{ $name }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(\Illuminate\Support\Str::substr($name, 0, 2)) }}
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-[28px] font-extrabold text-[#073057] leading-tight">{{ $name }}</h1>
                        <p class="text-[#6B7280] mt-1">{{ $title ?: 'Maritime Professional' }}@if ($locationName) • {{ $locationName }}@endif</p>
                        <div class="flex flex-wrap gap-2 mt-4">
                            @if (! is_null($candidate->experience_years))
                                <span class="px-3 py-1 rounded-full bg-[#1AAD94]/10 text-[#1AAD94] text-xs font-bold uppercase">{{ $candidate->experience_years }} yrs experience</span>
                            @endif
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $candidate->is_available ? 'bg-[#16A34A]/10 text-[#16A34A]' : 'bg-gray-100 text-gray-500' }}">
                                {{ $candidate->is_available ? 'Available' : 'Not currently available' }}
                            </span>
                            @if ($candidate->expected_salary)
                                <span class="px-3 py-1 rounded-full bg-[#073057]/5 text-[#073057] text-xs font-bold">${{ number_format((float) $candidate->expected_salary) }}/mo expected</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary --}}
            @if ($candidate->bio)
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 md:p-8">
                    <h2 class="text-[#073057] text-lg font-bold mb-3">Professional Summary</h2>
                    <p class="text-[#2C2C2C] leading-relaxed whitespace-pre-line">{{ $candidate->bio }}</p>
                </div>
            @endif

            {{-- Skills --}}
            @if (! empty($skills))
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 md:p-8">
                    <h2 class="text-[#073057] text-lg font-bold mb-4">Skills</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($skills as $skill)
                            <span class="px-3 py-1 bg-[#F3F4F6] text-[#4B5563] text-sm font-medium rounded-full">{{ is_array($skill) ? ($skill['name'] ?? '') : $skill }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Experience --}}
            @if (! empty($experience))
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 md:p-8">
                    <h2 class="text-[#073057] text-lg font-bold mb-5">Work Experience</h2>
                    <div class="space-y-5">
                        @foreach ($experience as $exp)
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-lg bg-[#073057]/10 flex items-center justify-center shrink-0 text-[#073057]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-semibold text-[#073057]">{{ $exp['position'] ?? 'Role' }}</h4>
                                    <p class="text-[#1AAD94] text-sm">{{ $exp['company'] ?? '' }}</p>
                                    <p class="text-xs text-[#6B7280] mt-0.5">
                                        {{ $fmt($exp['start_date'] ?? null) }} – {{ ($exp['is_current'] ?? false) ? 'Present' : ($fmt($exp['end_date'] ?? null) ?: '—') }}
                                        @if (! empty($exp['vessel_type']) || ! empty($exp['vessel_name'])) · {{ $exp['vessel_type'] ?? '' }} {{ ! empty($exp['vessel_name']) ? '('.$exp['vessel_name'].')' : '' }} @endif
                                    </p>
                                    @if (! empty($exp['description']))
                                        <p class="text-sm text-[#4B5563] mt-1 whitespace-pre-line">{{ $exp['description'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Education --}}
            @if (! empty($education))
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 md:p-8">
                    <h2 class="text-[#073057] text-lg font-bold mb-5">Education</h2>
                    <div class="space-y-4">
                        @foreach ($education as $edu)
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-lg bg-[#1AAD94]/10 flex items-center justify-center shrink-0 text-[#1AAD94]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-semibold text-[#073057]">{{ $edu['degree'] ?? 'Qualification' }}</h4>
                                    <p class="text-[#1AAD94] text-sm">{{ $edu['institution'] ?? '' }}@if (! empty($edu['field_of_study'])) · {{ $edu['field_of_study'] }}@endif</p>
                                    <p class="text-xs text-[#6B7280] mt-0.5">{{ $edu['start_year'] ?? '' }} – {{ $edu['end_year'] ?? 'Present' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Certifications / Awards --}}
            @if (! empty($awards))
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 md:p-8">
                    <h2 class="text-[#073057] text-lg font-bold mb-4">Certifications &amp; Awards</h2>
                    <ul class="space-y-3">
                        @foreach ($awards as $cert)
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-[#1AAD94] mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <div>
                                    <p class="font-semibold text-[#073057]">{{ $cert['name'] ?? '—' }}</p>
                                    <p class="text-xs text-[#6B7280]">
                                        @if (! empty($cert['issuer'])) {{ $cert['issuer'] }} @endif
                                        @if (! empty($cert['issue_date'])) · {{ $fmt($cert['issue_date']) }} @endif
                                        @if (! empty($cert['expiry_date'])) – Expires {{ $fmt($cert['expiry_date']) }} @elseif (! empty($cert['no_expiry'])) · No expiry @endif
                                        @if (! empty($cert['credential_id'])) · ID: {{ $cert['credential_id'] }} @endif
                                    </p>
                                    @if (! empty($cert['certificate_path']))
                                        <a href="{{ asset('storage/'.$cert['certificate_path']) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 mt-1.5 text-xs font-medium text-[#1AAD94] hover:underline">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            View certificate
                                        </a>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Languages --}}
            @if (! empty($languages))
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 md:p-8">
                    <h2 class="text-[#073057] text-lg font-bold mb-4">Languages</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($languages as $lang)
                            <span class="px-3 py-1 bg-[#F3F4F6] text-[#4B5563] text-sm font-medium rounded-full">{{ is_array($lang) ? ($lang['name'] ?? '') : $lang }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Candidate Details --}}
            @if ($hasDetails)
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 md:p-8">
                    <h2 class="text-[#073057] text-lg font-bold mb-4">Candidate Details</h2>
                    <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        @if ($candidate->gender)
                            <div><dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">Gender</dt><dd class="text-[#073057] font-semibold">{{ ucfirst($candidate->gender) }}</dd></div>
                        @endif
                        @if ($dob)
                            <div><dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">Date of birth</dt><dd class="text-[#073057] font-semibold">{{ $dob->format('M d, Y') }}@if ($age) <span class="text-[#6B7280] font-normal">({{ $age }} yrs)</span>@endif</dd></div>
                        @endif
                        @if ($candidate->education_level)
                            <div><dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">Education level</dt><dd class="text-[#073057] font-semibold">{{ $candidate->education_level }}</dd></div>
                        @endif
                        @if ($candidate->expected_salary || $candidate->salary_type)
                            <div>
                                <dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">Expected salary</dt>
                                <dd class="text-[#073057] font-semibold">
                                    @if ($candidate->expected_salary)${{ number_format((float) $candidate->expected_salary) }}@endif
                                    @if ($candidate->salary_type)<span class="text-[#6B7280] font-normal"> / {{ $candidate->salary_type }}</span>@endif
                                </dd>
                            </div>
                        @endif
                        @if ($memberSince)
                            <div><dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">Member since</dt><dd class="text-[#073057] font-semibold">{{ $memberSince->format('M Y') }}</dd></div>
                        @endif
                    </dl>

                    @if ($categories->isNotEmpty())
                        <div class="mt-5 pt-5 border-t border-gray-100">
                            <p class="text-xs uppercase tracking-widest text-gray-400 mb-2">Desired roles</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($categories as $cat)
                                    <span class="px-3 py-1 bg-[#F3F4F6] text-[#4B5563] text-sm font-medium rounded-full">{{ $cat->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Intro video --}}
            @if ($candidate->video_url)
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 md:p-8">
                    <h2 class="text-[#073057] text-lg font-bold mb-4">Intro Video</h2>
                    <a href="{{ $candidate->video_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-[#1AAD94] font-semibold hover:underline">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Watch introduction video
                    </a>
                </div>
            @endif

            {{-- CVs / Documents --}}
            @if ($cvs->isNotEmpty())
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 md:p-8">
                    <h2 class="text-[#073057] text-lg font-bold mb-4">CVs &amp; Documents</h2>
                    <ul class="space-y-2">
                        @foreach ($cvs as $r)
                            <li>
                                <a href="{{ $r->url() }}" target="_blank" rel="noopener" download
                                   class="flex items-center gap-2 text-sm font-semibold text-[#073057] hover:text-[#1AAD94]">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    {{ $r->title ?: 'CV' }}@if ($r->is_default) <span class="text-xs font-normal text-[#6B7280]">· default</span>@endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Sidebar actions --}}
        <aside class="space-y-4 h-fit lg:sticky lg:top-24">
            <div class="bg-white border border-[#E5E7EB] rounded-xl p-6">
                <h3 class="text-[#073057] text-base font-bold mb-4">Actions</h3>

                <div class="space-y-3">
                    {{-- Invite --}}
                    <button type="button" @click="inviteOpen = true"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2zM12 14v4m-2-2h4"/></svg>
                        Invite candidate
                    </button>

                    {{-- Save CV (download) --}}
                    @if ($cv)
                        <a href="{{ $cv->url() }}" target="_blank" rel="noopener" download
                           class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#073057] hover:brightness-110 text-white text-sm font-bold rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Save CV
                        </a>
                    @else
                        <button type="button" disabled
                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-100 text-gray-400 text-sm font-bold rounded-lg cursor-not-allowed">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            No CV uploaded
                        </button>
                    @endif

                    {{-- Save profile (shortlist toggle) --}}
                    <form method="POST" action="{{ route('employer.saved-candidates.toggle', $candidate->id) }}">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-bold rounded-lg border transition {{ $isSaved ? 'border-[#1AAD94] text-[#1AAD94] bg-[#1AAD94]/5 hover:bg-[#1AAD94]/10' : 'border-[#E5E7EB] text-[#4B5563] hover:bg-[#F9FAFB]' }}">
                            <svg class="w-5 h-5" fill="{{ $isSaved ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                            {{ $isSaved ? 'Saved to shortlist' : 'Save profile' }}
                        </button>
                    </form>
                </div>

                <div class="mt-4 pt-4 border-t border-[#E5E7EB]">
                    <x-ui.button :href="route('employer.recruitment-requests.index')" variant="outline" class="w-full">Back to Hiring Services</x-ui.button>
                </div>
            </div>

            {{-- Contact --}}
            @if ($email || $phone)
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6">
                    <h3 class="text-[#073057] text-base font-bold mb-3">Contact</h3>
                    <div class="space-y-3 text-sm">
                        @if ($email)
                            <a href="mailto:{{ $email }}" class="flex items-center gap-3 text-[#4B5563] hover:text-[#1AAD94] break-all">
                                <svg class="w-4 h-4 shrink-0 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                {{ $email }}
                            </a>
                        @endif
                        @if ($phone)
                            <a href="tel:{{ $phone }}" class="flex items-center gap-3 text-[#4B5563] hover:text-[#1AAD94]">
                                <svg class="w-4 h-4 shrink-0 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ $phone }}
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Links --}}
            @if (! empty($social) && (($social['linkedin'] ?? false) || ($social['twitter'] ?? false) || ($social['github'] ?? false) || $candidate->website))
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6">
                    <h3 class="text-[#073057] text-base font-bold mb-3">Links</h3>
                    <div class="space-y-2 text-sm">
                        @if ($candidate->website)
                            <a href="{{ $candidate->website }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-[#1AAD94] hover:underline"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.6 9h16.8M3.6 15h16.8M12 3a15 15 0 010 18M12 3a15 15 0 000 18"/></svg> Website</a>
                        @endif
                        @if (! empty($social['linkedin']))
                            <a href="{{ $social['linkedin'] }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-[#1AAD94] hover:underline"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5M10.172 13.828a4 4 0 010-5.656l3-3a4 4 0 015.656 5.656l-1.5 1.5"/></svg> LinkedIn</a>
                        @endif
                        @if (! empty($social['twitter']))
                            <a href="{{ $social['twitter'] }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-[#1AAD94] hover:underline"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5M10.172 13.828a4 4 0 010-5.656l3-3a4 4 0 015.656 5.656l-1.5 1.5"/></svg> Twitter / X</a>
                        @endif
                        @if (! empty($social['github']))
                            <a href="{{ $social['github'] }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-[#1AAD94] hover:underline"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5M10.172 13.828a4 4 0 010-5.656l3-3a4 4 0 015.656 5.656l-1.5 1.5"/></svg> GitHub</a>
                        @endif
                    </div>
                </div>
            @endif
        </aside>
    </div>

    {{-- Invite modal --}}
    <div x-show="inviteOpen" x-cloak class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" @click.self="inviteOpen = false" @keydown.escape.window="inviteOpen = false">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-xl p-6">
            <form method="POST" action="{{ route('employer.candidates.invite', $candidate->id) }}" class="space-y-4">
                @csrf
                <h3 class="text-lg font-bold text-[#073057]">Invite {{ $name }}</h3>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">Invitation type</label>
                    <select name="invite_type" required class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-1 focus:ring-[#1AAD94]">
                        <option value="pre_screening">Pre-screening interview</option>
                        <option value="interview">Interview</option>
                        <option value="meeting">Meeting</option>
                    </select>
                </div>
                <input name="scheduled_at" required placeholder="Date / time (e.g. Mon 24 Jun, 2:00 PM)" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-1 focus:ring-[#1AAD94]">
                <input name="location" required placeholder="Location or meeting link" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-1 focus:ring-[#1AAD94]">
                <textarea name="note" rows="4" placeholder="Optional note to the candidate" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-1 focus:ring-[#1AAD94]"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="inviteOpen = false" class="px-4 py-2 text-sm text-gray-600">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[#1AAD94] hover:brightness-110 text-white rounded-lg font-semibold">Send invitation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
