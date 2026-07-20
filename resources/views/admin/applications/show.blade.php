@extends('admin.layouts.app')

@php
    $candidate = $application->candidate;
    $name = $candidate?->user?->name ?? 'Candidate';
    $job = $application->jobListing;
    $submittedCv = $application->resume;
    $statusClass = $application->status === 'hired' ? 'bg-green-100 text-green-700'
        : ($application->status === 'shortlisted' ? 'bg-blue-100 text-blue-700'
        : ($application->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'));
    $dlIcon = 'M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
@endphp

@section('title', $name . ' — Application')
@section('page-title', 'Application Details')

@section('content')
    <div class="mb-4">
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('admin.applications') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Applications
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Application summary --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ $name }}</h2>
                <p class="text-sm text-gray-500">{{ $candidate?->user?->email ?? '' }}</p>
            </div>
            <span class="text-xs px-3 py-1 rounded-full font-medium {{ $statusClass }}">{{ ucfirst($application->status ?? 'pending') }}</span>
        </div>

        <dl class="grid sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-4 text-sm mt-5 pt-5 border-t border-gray-100">
            <div>
                <dt class="text-xs uppercase tracking-wide text-gray-400 mb-0.5">Applied for</dt>
                <dd class="text-gray-800 font-medium">
                    @if ($job)
                        <a href="{{ route('admin.jobs.show', $job) }}" class="text-[#1AAD94] hover:underline">{{ $job->title }}</a>
                    @else
                        <span class="text-gray-400">Job removed</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-gray-400 mb-0.5">Source</dt>
                <dd class="text-gray-800 font-medium">
                    @if ($job?->is_contract_staffing) Contract Staffing
                    @elseif ($job?->company) {{ $job->company->name }}
                    @else — @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-gray-400 mb-0.5">Applied on</dt>
                <dd class="text-gray-800 font-medium">{{ $application->created_at?->format('M d, Y g:i A') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-gray-400 mb-0.5">Submitted CV</dt>
                <dd class="text-gray-800 font-medium">
                    @if ($submittedCv && $submittedCv->url())
                        <a href="{{ $submittedCv->url() }}" target="_blank" rel="noopener" download
                           class="inline-flex items-center gap-1.5 text-[#1AAD94] hover:underline">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $dlIcon }}"/></svg>
                            {{ $submittedCv->title ?: 'Download CV' }}
                        </a>
                    @else
                        <span class="text-gray-400">No CV attached</span>
                    @endif
                </dd>
            </div>
        </dl>

        @if ($application->cover_letter)
            <div class="mt-5 pt-5 border-t border-gray-100">
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Cover letter</p>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $application->cover_letter }}</p>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="font-semibold text-gray-900">Employer chat access</h3>
                @if($application->employer_chat_access_granted_at)
                    <p class="mt-1 text-sm text-green-700">Granted {{ $application->employer_chat_access_granted_at->diffForHumans() }}. This applicant can chat with {{ $job?->company?->name ?? 'the employer' }}.</p>
                @else
                    <p class="mt-1 text-sm text-gray-500">This applicant is not visible in the employer's chat.</p>
                @endif
            </div>
            @if($application->employer_chat_access_granted_at)
                <form method="POST" action="{{ route('admin.applications.chat-access.revoke', $application) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-semibold text-red-600 border border-red-200 rounded-lg hover:bg-red-50">Revoke access</button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.applications.chat-access.grant', $application) }}">
                    @csrf
                    <button type="submit" @disabled(!$job?->is_approved) class="px-4 py-2 text-sm font-semibold rounded-lg {{ $job?->is_approved ? 'bg-[#1AAD94] text-white hover:bg-[#158f7a]' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}">Grant chat access</button>
                </form>
            @endif
        </div>
        @if(!$job?->is_approved)
            <p class="mt-3 text-xs text-amber-700">Approve the job before granting employer chat access.</p>
        @endif
    </div>

    {{-- Full candidate profile: CV, certificates, experience, education, skills, details --}}
    @if ($candidate)
        @include('admin.users.partials.candidate-profile', ['candidate' => $candidate])
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-6 text-sm text-gray-400">
            This applicant no longer has a candidate profile.
        </div>
    @endif
@endsection
