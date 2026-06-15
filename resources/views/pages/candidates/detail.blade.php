@extends('layouts.app')

@section('title', ($candidate->user?->name ?? 'Candidate Profile').' — JOSEOCEANJOBS')

@section('content')
@php
    $name = $candidate->user?->name ?? 'Candidate';
    $title = $candidate->title;
    $locationName = $candidate->location?->name ?? $candidate->address;
    $skills = ! empty($candidate->skills_list) ? $candidate->skills_list : $candidate->skills->pluck('name')->all();
    $experience = is_array($candidate->experience) ? $candidate->experience : [];
    $education = is_array($candidate->education) ? $candidate->education : [];
    $awards = is_array($candidate->awards) ? $candidate->awards : [];
    $languages = is_array($candidate->languages) ? $candidate->languages : [];
    $social = is_array($candidate->social_links) ? $candidate->social_links : [];
    $cv = $candidate->resumes->first(fn ($r) => ! empty($r->file_path));
    $fmt = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->format('M Y') : null;
@endphp

<section class="py-12 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />

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
                <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-8">
                    <div class="flex items-start gap-5">
                        <div class="w-20 h-20 rounded-2xl bg-[#073057]/10 flex items-center justify-center text-2xl font-extrabold text-[#073057] shrink-0 overflow-hidden">
                            @if ($candidate->user?->avatar)
                                <img src="{{ \Illuminate\Support\Str::startsWith($candidate->user->avatar, ['http', '/']) ? $candidate->user->avatar : asset($candidate->user->avatar) }}" alt="{{ $name }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(\Illuminate\Support\Str::substr($name, 0, 2)) }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h1 class="text-[32px] font-extrabold text-[#073057] leading-tight">{{ $name }}</h1>
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
                    <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-8">
                        <h2 class="text-[#073057] text-xl font-bold mb-3">Professional Summary</h2>
                        <p class="text-[#2C2C2C] leading-relaxed whitespace-pre-line">{{ $candidate->bio }}</p>
                    </div>
                @endif

                {{-- Skills --}}
                @if (! empty($skills))
                    <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-8">
                        <h2 class="text-[#073057] text-xl font-bold mb-4">Skills</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($skills as $skill)
                                <span class="px-3 py-1 bg-[#F3F4F6] text-[#4B5563] text-sm font-medium rounded-full">{{ is_array($skill) ? ($skill['name'] ?? '') : $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Experience --}}
                @if (! empty($experience))
                    <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-8">
                        <h2 class="text-[#073057] text-xl font-bold mb-5">Work Experience</h2>
                        <div class="space-y-5">
                            @foreach ($experience as $exp)
                                <div class="flex gap-4">
                                    <div class="w-10 h-10 rounded-lg bg-[#073057]/10 flex items-center justify-center shrink-0">
                                        <iconify-icon icon="lucide:briefcase" class="text-[#073057]"></iconify-icon>
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
                    <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-8">
                        <h2 class="text-[#073057] text-xl font-bold mb-5">Education</h2>
                        <div class="space-y-4">
                            @foreach ($education as $edu)
                                <div class="flex gap-4">
                                    <div class="w-10 h-10 rounded-lg bg-[#1AAD94]/10 flex items-center justify-center shrink-0">
                                        <iconify-icon icon="lucide:graduation-cap" class="text-[#1AAD94]"></iconify-icon>
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
                    <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-8">
                        <h2 class="text-[#073057] text-xl font-bold mb-4">Certifications &amp; Awards</h2>
                        <ul class="space-y-3">
                            @foreach ($awards as $cert)
                                <li class="flex items-start gap-3">
                                    <iconify-icon icon="lucide:award" class="text-[#1AAD94] mt-0.5"></iconify-icon>
                                    <div>
                                        <p class="font-semibold text-[#073057]">{{ $cert['name'] ?? '—' }}</p>
                                        <p class="text-xs text-[#6B7280]">
                                            @if (! empty($cert['issuer'])) {{ $cert['issuer'] }} @endif
                                            @if (! empty($cert['issue_date'])) · {{ $fmt($cert['issue_date']) }} @endif
                                            @if (! empty($cert['credential_id'])) · ID: {{ $cert['credential_id'] }} @endif
                                        </p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Languages --}}
                @if (! empty($languages))
                    <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-8">
                        <h2 class="text-[#073057] text-xl font-bold mb-4">Languages</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($languages as $lang)
                                <span class="px-3 py-1 bg-[#F3F4F6] text-[#4B5563] text-sm font-medium rounded-full">{{ is_array($lang) ? ($lang['name'] ?? '') : $lang }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar actions --}}
            <aside class="space-y-4 h-fit lg:sticky lg:top-24">
                <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-6">
                    <h3 class="text-[#073057] text-lg font-bold mb-4">Actions</h3>

                    <div class="space-y-3">
                        @if ($cv)
                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($cv->file_path) }}" target="_blank" rel="noopener"
                               class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#073057] hover:brightness-110 text-white text-sm font-bold rounded-lg transition">
                                <iconify-icon icon="lucide:download"></iconify-icon>
                                Download CV
                            </a>
                        @else
                            <p class="text-sm text-[#6B7280]">No CV has been uploaded for this candidate yet.</p>
                        @endif

                        @if ($isOwner)
                            <a href="{{ route('user.candidate.profile') }}"
                               class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 border border-[#1AAD94] text-[#1AAD94] text-sm font-bold rounded-lg hover:bg-[#1AAD94]/5 transition">
                                <iconify-icon icon="lucide:pencil"></iconify-icon>
                                Edit my profile
                            </a>
                        @endif
                    </div>

                    <div class="mt-4 pt-4 border-t border-[#E5E7EB] space-y-3">
                        @if ($isAdmin)
                            <x-ui.button :href="route('candidate.index')" variant="outline" class="w-full">Back to Directory</x-ui.button>
                        @else
                            <x-ui.button :href="route('user.dashboard')" variant="outline" class="w-full">Back to Dashboard</x-ui.button>
                        @endif
                    </div>
                </div>

                @if (! empty($social) && (($social['linkedin'] ?? false) || ($social['twitter'] ?? false) || ($social['github'] ?? false) || $candidate->website))
                    <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-6">
                        <h3 class="text-[#073057] text-base font-bold mb-3">Links</h3>
                        <div class="space-y-2 text-sm">
                            @if ($candidate->website)
                                <a href="{{ $candidate->website }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-[#1AAD94] hover:underline"><iconify-icon icon="lucide:globe"></iconify-icon> Website</a>
                            @endif
                            @if (! empty($social['linkedin']))
                                <a href="{{ $social['linkedin'] }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-[#1AAD94] hover:underline"><iconify-icon icon="lucide:linkedin"></iconify-icon> LinkedIn</a>
                            @endif
                            @if (! empty($social['twitter']))
                                <a href="{{ $social['twitter'] }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-[#1AAD94] hover:underline"><iconify-icon icon="lucide:twitter"></iconify-icon> Twitter / X</a>
                            @endif
                            @if (! empty($social['github']))
                                <a href="{{ $social['github'] }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-[#1AAD94] hover:underline"><iconify-icon icon="lucide:github"></iconify-icon> GitHub</a>
                            @endif
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</section>
@endsection
