{{-- Full candidate profile for the admin user-detail page. Expects $candidate. --}}
@php
    $c = $candidate;
    $skills = ! empty($c->skills_list) ? $c->skills_list : $c->skills->pluck('name')->all();
    $experience = is_array($c->experience) ? $c->experience : [];
    $education = is_array($c->education) ? $c->education : [];
    $awards = is_array($c->awards) ? $c->awards : [];
    $languages = is_array($c->languages) ? $c->languages : [];
    $social = is_array($c->social_links) ? $c->social_links : [];
    $cvs = $c->resumes->filter(fn ($r) => ! empty($r->file_path))->values();
    $categories = $c->categories;
    $dob = $c->date_of_birth;
    $locationName = $c->location?->name ?? $c->address;
    $fmt = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->format('M Y') : null;
    $fileIcon = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
    $dlIcon = 'M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
@endphp

<div class="space-y-6">
    {{-- Header + summary --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Candidate Profile</h3>
                <p class="text-sm text-gray-500">{{ $c->title ?: 'Maritime Professional' }}@if ($locationName) · {{ $locationName }}@endif</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if (! is_null($c->experience_years))
                    <span class="text-xs px-2 py-1 rounded-full bg-[#1AAD94]/10 text-[#1AAD94] font-medium">{{ $c->experience_years }} yrs exp</span>
                @endif
                <span class="text-xs px-2 py-1 rounded-full {{ $c->is_available ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $c->is_available ? 'Available' : 'Not available' }}</span>
                @if ($c->expected_salary)
                    <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700 font-medium">${{ number_format((float) $c->expected_salary) }}@if ($c->salary_type)/{{ $c->salary_type }}@endif</span>
                @endif
            </div>
        </div>
        @if ($c->bio)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Professional summary</p>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $c->bio }}</p>
            </div>
        @endif
    </div>

    {{-- CV & Documents --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">CV &amp; Documents</h3>
        @if ($cvs->isNotEmpty())
            <ul class="space-y-2">
                @foreach ($cvs as $r)
                    <li>
                        <a href="{{ $r->url() }}" target="_blank" rel="noopener" download
                           class="inline-flex items-center gap-2 text-sm font-medium text-gray-800 hover:text-[#1AAD94]">
                            <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $dlIcon }}"/></svg>
                            {{ $r->title ?: 'CV' }}@if ($r->is_default) <span class="text-xs text-gray-400 font-normal">· default</span>@endif
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-sm text-gray-400">No CV uploaded.</p>
        @endif
    </div>

    {{-- Certifications --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Certifications</h3>
        @if (! empty($awards))
            <ul class="space-y-3">
                @foreach ($awards as $cert)
                    <li class="flex items-start justify-between gap-3 border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900">{{ $cert['name'] ?? '—' }}</p>
                            <p class="text-xs text-gray-500">
                                @if (! empty($cert['issuer'])) {{ $cert['issuer'] }} @endif
                                @if (! empty($cert['issue_date'])) · {{ $fmt($cert['issue_date']) }} @endif
                                @if (! empty($cert['expiry_date'])) – Expires {{ $fmt($cert['expiry_date']) }} @elseif (! empty($cert['no_expiry'])) · No expiry @endif
                                @if (! empty($cert['credential_id'])) · ID: {{ $cert['credential_id'] }} @endif
                            </p>
                        </div>
                        @if (! empty($cert['certificate_path']))
                            <a href="{{ asset('storage/'.$cert['certificate_path']) }}" target="_blank" rel="noopener"
                               class="shrink-0 inline-flex items-center gap-1.5 text-xs font-medium text-[#1AAD94] hover:underline">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $fileIcon }}"/></svg>
                                View file
                            </a>
                        @else
                            <span class="shrink-0 text-xs text-gray-400">No file</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-sm text-gray-400">No certifications added.</p>
        @endif
    </div>

    {{-- Work experience --}}
    @if (! empty($experience))
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Work Experience</h3>
            <div class="space-y-4">
                @foreach ($experience as $exp)
                    <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                        <p class="font-medium text-gray-900">{{ $exp['position'] ?? 'Role' }}</p>
                        <p class="text-sm text-[#1AAD94]">{{ $exp['company'] ?? '' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $fmt($exp['start_date'] ?? null) }} – {{ ($exp['is_current'] ?? false) ? 'Present' : ($fmt($exp['end_date'] ?? null) ?: '—') }}
                            @if (! empty($exp['vessel_type']) || ! empty($exp['vessel_name'])) · {{ $exp['vessel_type'] ?? '' }} {{ ! empty($exp['vessel_name']) ? '('.$exp['vessel_name'].')' : '' }} @endif
                        </p>
                        @if (! empty($exp['description']))
                            <p class="text-sm text-gray-600 mt-1 whitespace-pre-line">{{ $exp['description'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Education --}}
    @if (! empty($education))
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Education</h3>
            <div class="space-y-4">
                @foreach ($education as $edu)
                    <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                        <p class="font-medium text-gray-900">{{ $edu['degree'] ?? 'Qualification' }}</p>
                        <p class="text-sm text-[#1AAD94]">{{ $edu['institution'] ?? '' }}@if (! empty($edu['field_of_study'])) · {{ $edu['field_of_study'] }} @endif</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $edu['start_year'] ?? '' }} – {{ $edu['end_year'] ?? 'Present' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Skills + Languages --}}
    @if (! empty($skills) || ! empty($languages))
        <div class="grid sm:grid-cols-2 gap-6">
            @if (! empty($skills))
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Skills</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($skills as $skill)
                            <span class="text-sm px-3 py-1 rounded-full bg-gray-100 text-gray-700">{{ is_array($skill) ? ($skill['name'] ?? '') : $skill }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
            @if (! empty($languages))
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Languages</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($languages as $lang)
                            <span class="text-sm px-3 py-1 rounded-full bg-gray-100 text-gray-700">{{ is_array($lang) ? ($lang['name'] ?? '') : $lang }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Details + links --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Details</h3>
        <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
            @if ($c->gender)
                <div><dt class="text-xs uppercase tracking-wide text-gray-400 mb-0.5">Gender</dt><dd class="text-gray-800 font-medium">{{ ucfirst($c->gender) }}</dd></div>
            @endif
            @if ($dob)
                <div><dt class="text-xs uppercase tracking-wide text-gray-400 mb-0.5">Date of birth</dt><dd class="text-gray-800 font-medium">{{ $dob->format('M d, Y') }}@if ($dob->age) <span class="text-gray-400 font-normal">({{ $dob->age }} yrs)</span>@endif</dd></div>
            @endif
            @if ($c->education_level)
                <div><dt class="text-xs uppercase tracking-wide text-gray-400 mb-0.5">Education level</dt><dd class="text-gray-800 font-medium">{{ $c->education_level }}</dd></div>
            @endif
            @if ($c->expected_salary || $c->salary_type)
                <div><dt class="text-xs uppercase tracking-wide text-gray-400 mb-0.5">Expected salary</dt><dd class="text-gray-800 font-medium">@if ($c->expected_salary)${{ number_format((float) $c->expected_salary) }} @endif @if ($c->salary_type)<span class="text-gray-400 font-normal">/ {{ $c->salary_type }}</span>@endif</dd></div>
            @endif
            @if ($locationName)
                <div><dt class="text-xs uppercase tracking-wide text-gray-400 mb-0.5">Location</dt><dd class="text-gray-800 font-medium">{{ $locationName }}</dd></div>
            @endif
            @if ($c->video_url)
                <div><dt class="text-xs uppercase tracking-wide text-gray-400 mb-0.5">Intro video</dt><dd><a href="{{ $c->video_url }}" target="_blank" rel="noopener" class="text-[#1AAD94] hover:underline font-medium">Watch video</a></dd></div>
            @endif
        </dl>

        @if ($categories->isNotEmpty())
            <div class="mt-5 pt-5 border-t border-gray-100">
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-2">Desired roles</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($categories as $cat)
                        <span class="text-sm px-3 py-1 rounded-full bg-gray-100 text-gray-700">{{ $cat->name }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($c->website || ! empty($social['linkedin']) || ! empty($social['twitter']) || ! empty($social['github']))
            <div class="mt-5 pt-5 border-t border-gray-100">
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-2">Links</p>
                <div class="flex flex-wrap gap-x-5 gap-y-2 text-sm">
                    @if ($c->website)<a href="{{ $c->website }}" target="_blank" rel="noopener" class="text-[#1AAD94] hover:underline">Website</a>@endif
                    @if (! empty($social['linkedin']))<a href="{{ $social['linkedin'] }}" target="_blank" rel="noopener" class="text-[#1AAD94] hover:underline">LinkedIn</a>@endif
                    @if (! empty($social['twitter']))<a href="{{ $social['twitter'] }}" target="_blank" rel="noopener" class="text-[#1AAD94] hover:underline">Twitter / X</a>@endif
                    @if (! empty($social['github']))<a href="{{ $social['github'] }}" target="_blank" rel="noopener" class="text-[#1AAD94] hover:underline">GitHub</a>@endif
                </div>
            </div>
        @endif
    </div>
</div>
