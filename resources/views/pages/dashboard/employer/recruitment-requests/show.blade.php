@extends('layouts.dashboard')

@section('title', 'Recruitment Request')
@section('page-title', 'Recruitment Request')

@section('sidebar-nav')
    @include('pages.dashboard.employer.partials.sidebar')
@endsection

@section('content')
@php
    $statusBadge = function ($status) {
        return match ($status) {
            'pending' => 'bg-yellow-100 text-yellow-700',
            'quote_sent' => 'bg-blue-100 text-blue-700',
            'paid', 'in_progress' => 'bg-indigo-100 text-indigo-700',
            'candidates_delivered' => 'bg-purple-100 text-purple-700',
            'completed' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-gray-100 text-gray-500',
            default => 'bg-gray-100 text-gray-600',
        };
    };
@endphp

<div class="mb-6">
    <a href="{{ route('employer.recruitment-requests.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-gray-400 hover:text-gray-600 mb-2">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to requests
    </a>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#073057]">{{ $recruitment->job_title }}</h2>
            <p class="text-[#6B7280] text-sm">{{ \App\Models\RecruitmentRequest::SERVICE_TYPES[$recruitment->service_type] }} &middot; Submitted {{ $recruitment->created_at->format('M d, Y') }}</p>
        </div>
        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusBadge($recruitment->status) }}">
            {{ \App\Models\RecruitmentRequest::STATUSES[$recruitment->status] }}
        </span>
    </div>
</div>

@if (session('success'))
    <div class="mb-5 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="mb-5 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
@endif

<div class="grid lg:grid-cols-3 gap-6">
    {{-- Left: details --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Quote / pay CTA --}}
        @if ($recruitment->status === 'quote_sent' && $recruitment->order)
            <div class="bg-gradient-to-br from-[#073057] to-[#0a4275] text-white rounded-xl p-6 shadow-lg">
                <div class="text-xs font-bold uppercase tracking-widest text-white/60 mb-2">Quote Ready</div>
                <div class="text-3xl font-extrabold mb-1">{{ $recruitment->salary_currency }} {{ number_format($recruitment->quoted_amount, 2) }}</div>
                <p class="text-sm text-white/80 mb-4">Issued on {{ $recruitment->quoted_at?->format('M d, Y') }}. Pay to start delivery.</p>
                <a href="{{ route('order.detail', $recruitment->order_id) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1AAD94] hover:brightness-110 rounded-lg text-sm font-bold uppercase tracking-widest">
                    View invoice &amp; pay
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>
        @elseif (in_array($recruitment->status, ['paid', 'in_progress']))
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5 text-indigo-800">
                <strong>We're on it.</strong> Our team is sourcing candidates for your role. You'll get an email when they're ready to review.
            </div>
        @endif

        {{-- Delivered candidates --}}
        @if ($recruitment->candidates->isNotEmpty())
            <div class="bg-white rounded-xl border border-[#E5E7EB] p-6">
                <h3 class="text-base font-bold text-[#073057] mb-4">Candidates ({{ $recruitment->candidates->count() }})</h3>
                <div class="space-y-3">
                    @foreach ($recruitment->candidates as $cand)
                        <div class="border border-[#E5E7EB] rounded-lg p-4">
                            <div class="flex flex-wrap items-start justify-between gap-3 mb-2">
                                <div>
                                    <p class="font-semibold text-[#073057]">{{ $cand->displayName() }}</p>
                                    <p class="text-xs text-[#6B7280]">{{ $cand->displayEmail() ?? 'No email' }} @if ($cand->external_phone) &middot; {{ $cand->external_phone }} @endif</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full
                                    {{ match($cand->employer_decision) {
                                        'shortlisted' => 'bg-blue-100 text-blue-700',
                                        'contacted' => 'bg-purple-100 text-purple-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        'hired' => 'bg-green-100 text-green-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    } }}">
                                    {{ \App\Models\RecruitmentRequestCandidate::DECISIONS[$cand->employer_decision] }}
                                </span>
                            </div>
                            @if ($cand->summary)
                                <p class="text-sm text-[#4B5563] mb-3">{{ $cand->summary }}</p>
                            @endif
                            <div class="flex flex-wrap items-center gap-2 mt-3">
                                @if ($cand->external_cv_path)
                                    <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($cand->external_cv_path) }}" target="_blank"
                                       class="inline-flex items-center gap-1 text-xs font-semibold text-[#1AAD94] hover:text-[#0F8B75]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Download CV
                                    </a>
                                @endif
                                @if ($cand->isPlatformCandidate())
                                    <span class="text-xs text-gray-400">Platform candidate &middot; ID #{{ $cand->candidate_id }}</span>
                                @endif

                                @if (! in_array($recruitment->status, ['cancelled', 'completed']))
                                    <form method="POST" action="{{ route('employer.recruitment-requests.candidate.decide', [$recruitment, $cand]) }}" class="ml-auto flex items-center gap-2">
                                        @csrf
                                        <select name="decision" class="text-xs px-3 py-1.5 border border-gray-300 rounded-md focus:ring-1 focus:ring-[#1AAD94]">
                                            @foreach (\App\Models\RecruitmentRequestCandidate::DECISIONS as $val => $label)
                                                <option value="{{ $val }}" {{ $cand->employer_decision === $val ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="text-xs font-semibold px-3 py-1.5 bg-[#073057] text-white rounded-md hover:brightness-110">Update</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Request details --}}
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-6">
            <h3 class="text-base font-bold text-[#073057] mb-4">Request details</h3>
            <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                <div><dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">CVs requested</dt><dd class="text-[#073057] font-semibold">{{ $recruitment->cv_count }}</dd></div>
                <div><dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">Category</dt><dd class="text-[#4B5563]">{{ $recruitment->category?->name ?? '—' }}</dd></div>
                <div><dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">Job type</dt><dd class="text-[#4B5563]">{{ $recruitment->jobType?->name ?? '—' }}</dd></div>
                <div><dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">Location</dt><dd class="text-[#4B5563]">{{ $recruitment->location?->name ?? 'Any' }}</dd></div>
                <div><dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">Experience</dt><dd class="text-[#4B5563]">{{ $recruitment->experience_level ?? 'Any' }}</dd></div>
                <div><dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">Salary range</dt>
                    <dd class="text-[#4B5563]">
                        @if ($recruitment->salary_min || $recruitment->salary_max)
                            {{ $recruitment->salary_currency }} {{ number_format((float) $recruitment->salary_min, 0) }}–{{ number_format((float) $recruitment->salary_max, 0) }}
                        @else
                            Not specified
                        @endif
                    </dd>
                </div>
                <div><dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">Needed by</dt><dd class="text-[#4B5563]">{{ $recruitment->needed_by?->format('M d, Y') ?? 'Flexible' }}</dd></div>
                <div><dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">JD attachment</dt>
                    <dd class="text-[#4B5563]">
                        @if ($recruitment->jd_file_path)
                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($recruitment->jd_file_path) }}" target="_blank" class="text-[#1AAD94] hover:text-[#0F8B75] font-semibold">Download</a>
                        @else
                            <span class="text-gray-400">None</span>
                        @endif
                    </dd>
                </div>
            </dl>

            @if (! empty($recruitment->skills_list))
                <div class="mt-5 pt-5 border-t border-gray-100">
                    <p class="text-xs uppercase tracking-widest text-gray-400 mb-2">Skills</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($recruitment->skills_list as $skill)
                            <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-[#4B5563] text-xs font-medium rounded-full">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-5 pt-5 border-t border-gray-100">
                <p class="text-xs uppercase tracking-widest text-gray-400 mb-2">Description</p>
                <p class="text-sm text-[#4B5563] whitespace-pre-line">{{ $recruitment->description }}</p>
            </div>
        </div>
    </div>

    {{-- Right: actions --}}
    <aside class="space-y-4">
        @if ($recruitment->isCancellable())
            <div class="bg-white rounded-xl border border-[#E5E7EB] p-5">
                <h4 class="text-sm font-bold text-[#073057] mb-2">Need to cancel?</h4>
                <p class="text-xs text-[#6B7280] mb-3">You can cancel this request at any time before it's completed.</p>
                <form method="POST" action="{{ route('employer.recruitment-requests.cancel', $recruitment) }}" onsubmit="return confirm('Cancel this recruitment request?');">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 border border-red-200 text-red-600 hover:bg-red-50 rounded-lg text-sm font-semibold transition">Cancel Request</button>
                </form>
            </div>
        @endif

        <div class="bg-[#F9FAFB] rounded-xl border border-[#E5E7EB] p-5 text-xs text-[#6B7280]">
            <p class="font-semibold text-[#073057] text-sm mb-2">Need help?</p>
            <p class="mb-2">Reply to any of our emails or contact <a href="mailto:info@joseoceanjobs.com" class="text-[#1AAD94] font-semibold">info@joseoceanjobs.com</a> with any questions about this request.</p>
        </div>
    </aside>
</div>
@endsection
