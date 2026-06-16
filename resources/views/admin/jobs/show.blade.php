@extends('admin.layouts.app')

@section('title', $job->title)
@section('page-title', 'Job Detail')

@section('content')
    @php
        $statusClass = $job->status === 'active' ? 'bg-green-100 text-green-700'
            : ($job->status === 'pending' ? 'bg-yellow-100 text-yellow-700'
            : (in_array($job->status, ['closed', 'expired']) ? 'bg-red-100 text-red-700'
            : ($job->status === 'paused' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600')));

        $salary = ($job->salary_min || $job->salary_max)
            ? trim(($job->salary_min ? number_format((float) $job->salary_min) : '').' – '.($job->salary_max ? number_format((float) $job->salary_max) : '').' '.($job->salary_type ?? ''))
            : 'Not disclosed';

        $requirements = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $job->qualification)));

        $details = [
            'Job Type' => $job->jobType?->name,
            'Category' => $job->category?->name,
            'Location' => $job->location?->name,
            'Address' => $job->address,
            'Salary' => $salary,
            'Experience' => $job->experience_required,
            'Vacancies' => $job->vacancies,
            'Hours' => $job->hours ? trim($job->hours.' '.($job->hours_type ?? '')) : null,
            'Gender Preference' => $job->gender_preference,
            'Deadline' => $job->deadline?->format('M d, Y'),
        ];
    @endphp

    <a href="{{ route('admin.jobs') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-800 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Job Listings
    </a>

    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h2 class="text-xl font-bold text-gray-900">{{ $job->title }}</h2>
                    <span class="text-xs px-2 py-1 rounded-full {{ $statusClass }}">{{ ucfirst($job->status ?? 'N/A') }}</span>
                    @if($job->is_approved)<span class="text-xs px-2 py-1 rounded-full bg-green-50 text-green-700">Approved</span>@endif
                    @if($job->is_featured)<span class="text-xs px-2 py-1 rounded-full bg-indigo-50 text-indigo-700">Featured</span>@endif
                    @if($job->is_urgent)<span class="text-xs px-2 py-1 rounded-full bg-red-50 text-red-700">Urgent</span>@endif
                    @if($job->is_contract_staffing)<span class="text-xs px-2 py-1 rounded-full bg-sky-50 text-sky-700">Contract Staffing</span>@endif
                </div>
                <p class="text-sm text-gray-500">{{ $job->company?->name ?? 'No company' }}</p>
            </div>

            <div class="flex items-center gap-2" x-data="{ open: false }">
                @if($job->slug)
                    <a href="{{ route('job.detail', $job->slug) }}" target="_blank"
                       class="px-3 py-2 text-sm font-medium border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">View public listing ↗</a>
                @endif
                <a href="{{ route('admin.jobs.edit', $job) }}"
                   class="px-3 py-2 text-sm font-medium bg-[#1AAD94] text-white rounded-lg hover:bg-[#1AAD94]/90">Edit Job</a>
                <div class="relative">
                    <button @click="open = !open" class="px-3 py-2 text-sm font-medium bg-[#073057] text-white rounded-lg hover:bg-[#073057]/90">Change Status ▾</button>
                    <div x-show="open" @click.away="open = false" x-cloak
                         class="absolute right-0 mt-1 w-36 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                        @foreach(['pending', 'active', 'paused', 'closed', 'expired', 'draft'] as $s)
                            <form method="POST" action="{{ route('admin.jobs.status', $job) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="{{ $s }}">
                                <button type="submit" class="w-full text-left px-3 py-1.5 text-sm hover:bg-gray-50 {{ $job->status === $s ? 'font-semibold text-[#1AAD94]' : 'text-gray-600' }}">
                                    {{ ucfirst($s) }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.jobs.delete', $job) }}" onsubmit="return confirm('Delete this job?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-3 py-2 text-sm font-medium border border-red-200 text-red-600 rounded-lg hover:bg-red-50">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Description</h3>
                @if($job->description)
                    <div class="prose prose-sm max-w-none text-gray-700 whitespace-pre-line">{{ $job->description }}</div>
                @else
                    <p class="text-sm text-gray-400">No description provided.</p>
                @endif
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Requirements / Qualifications</h3>
                @if(count($requirements))
                    <ul class="list-disc pl-5 space-y-1 text-sm text-gray-700">
                        @foreach($requirements as $req)<li>{{ $req }}</li>@endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-400">No qualifications listed.</p>
                @endif
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Job Details</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    @foreach($details as $label => $value)
                        <div>
                            <dt class="text-xs font-medium text-gray-400">{{ $label }}</dt>
                            <dd class="text-sm text-gray-800 mt-0.5">{{ filled($value) ? $value : '—' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Employer</h3>
                @if($job->company)
                    <p class="text-sm font-medium text-gray-900">{{ $job->company->name }}</p>
                    <dl class="mt-3 space-y-2 text-sm">
                        @if($job->company->email)<div><dt class="text-xs text-gray-400">Email</dt><dd class="text-gray-700">{{ $job->company->email }}</dd></div>@endif
                        @if($job->company->phone)<div><dt class="text-xs text-gray-400">Phone</dt><dd class="text-gray-700">{{ $job->company->phone }}</dd></div>@endif
                        @if($job->company->website)<div><dt class="text-xs text-gray-400">Website</dt><dd><a href="{{ $job->company->website }}" target="_blank" class="text-[#1AAD94] hover:underline break-all">{{ $job->company->website }}</a></dd></div>@endif
                    </dl>
                @else
                    <p class="text-sm text-gray-400">No company linked.</p>
                @endif
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Posting Info</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-400">Applicants</dt>
                        <dd class="font-semibold text-gray-900">{{ $job->applications_count }}</dd>
                    </div>
                    <div><dt class="text-xs text-gray-400">Posted by</dt><dd class="text-gray-700">{{ $job->postedBy?->name ?? '—' }}{{ $job->postedBy?->email ? ' ('.$job->postedBy->email.')' : '' }}</dd></div>
                    <div><dt class="text-xs text-gray-400">Created</dt><dd class="text-gray-700">{{ $job->created_at?->format('M d, Y g:i A') ?? '—' }}</dd></div>
                    <div><dt class="text-xs text-gray-400">Last updated</dt><dd class="text-gray-700">{{ $job->updated_at?->format('M d, Y g:i A') ?? '—' }}</dd></div>
                    <div><dt class="text-xs text-gray-400">Apply method</dt><dd class="text-gray-700">{{ $job->apply_method ?? '—' }}{{ $job->apply_email ? ' · '.$job->apply_email : '' }}{{ $job->apply_url ? ' · '.$job->apply_url : '' }}</dd></div>
                </dl>
            </div>
        </div>
    </div>
@endsection
