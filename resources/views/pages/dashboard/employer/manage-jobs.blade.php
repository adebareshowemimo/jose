@extends('layouts.dashboard')

@section('title', 'Manage Jobs')
@section('page-title', 'Manage Jobs')

@section('sidebar-nav')
    @include('pages.dashboard.employer.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#073057]">Manage Jobs</h2>
            <p class="text-[#6B7280]">View and manage your real posted jobs.</p>
        </div>
        <a href="{{ route('employer.new-job') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/></svg>
            Post New Job
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-4">
            <p class="text-sm text-[#6B7280]">All Jobs</p>
            <p class="mt-2 text-2xl font-bold text-[#073057]">{{ number_format($stats['total'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-4">
            <p class="text-sm text-[#6B7280]">Active</p>
            <p class="mt-2 text-2xl font-bold text-[#1AAD94]">{{ number_format($stats['active'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-4">
            <p class="text-sm text-[#6B7280]">Pending Review</p>
            <p class="mt-2 text-2xl font-bold text-amber-500">{{ number_format($stats['pending'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-4">
            <p class="text-sm text-[#6B7280]">Expired</p>
            <p class="mt-2 text-2xl font-bold text-red-500">{{ number_format($stats['expired'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-4">
            <p class="text-sm text-[#6B7280]">Draft</p>
            <p class="mt-2 text-2xl font-bold text-[#073057]">{{ number_format($stats['draft'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-4">
            <p class="text-sm text-[#6B7280]">Closing Soon</p>
            <p class="mt-2 text-2xl font-bold text-amber-500">{{ number_format($stats['closing_soon'] ?? 0) }}</p>
        </div>
    </div>

    <form method="GET" action="{{ route('employer.manage-jobs') }}" class="bg-white rounded-xl border border-[#E5E7EB] p-4">
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[240px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search jobs..."
                    class="w-full pl-10 pr-4 py-2.5 border border-[#E5E7EB] rounded-xl text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none">
                <svg class="w-4 h-4 text-[#9CA3AF] absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <select name="status" class="px-4 py-2.5 border border-[#E5E7EB] rounded-xl text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none">
                <option value="">All Status</option>
                @foreach(['pending' => 'Pending Review', 'draft' => 'Draft', 'active' => 'Active', 'paused' => 'Paused', 'closed' => 'Closed', 'expired' => 'Expired'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="sort" class="px-4 py-2.5 border border-[#E5E7EB] rounded-xl text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none">
                <option value="">Newest</option>
                <option value="oldest" @selected(request('sort') === 'oldest')>Oldest</option>
                <option value="deadline" @selected(request('sort') === 'deadline')>Deadline</option>
                <option value="applications" @selected(request('sort') === 'applications')>Applications</option>
            </select>

            <button type="submit" class="px-5 py-2.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition">Apply</button>
            @if(request()->hasAny(['search', 'status', 'sort']))
                <a href="{{ route('employer.manage-jobs') }}" class="px-4 py-2.5 border border-[#E5E7EB] text-[#4B5563] font-medium rounded-xl hover:bg-[#F9FAFB] transition">Reset</a>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-xl border border-[#E5E7EB] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-[#F9FAFB] border-b border-[#E5E7EB]">
                    <tr>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-[#073057]">Job Title</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-[#073057]">Applications</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-[#073057]">Posted Date</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-[#073057]">Expiry</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-[#073057]">Status</th>
                        <th class="text-right px-6 py-4 text-sm font-semibold text-[#073057]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    @forelse($jobs as $job)
                        @php
                            $statusColors = [
                                'active' => 'bg-emerald-100 text-emerald-700',
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'paused' => 'bg-amber-100 text-amber-700',
                                'closed' => 'bg-gray-100 text-gray-700',
                                'expired' => 'bg-red-100 text-red-700',
                                'draft' => 'bg-slate-100 text-slate-700',
                            ];
                        @endphp
                        <tr class="hover:bg-[#F9FAFB] transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="font-semibold text-[#073057]">{{ $job->title }}</h4>
                                            @if($job->is_featured)
                                                <span class="px-2 py-0.5 bg-[#1AAD94] text-white text-xs font-medium rounded">Featured</span>
                                            @endif
                                            @if($job->is_urgent)
                                                <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-medium rounded">Urgent</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-[#6B7280]">
                                            {{ $job->jobType?->name ?? ucfirst((string) $job->hours_type ?: 'Job') }}
                                            @if($job->location?->name)
                                                · {{ $job->location->name }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('employer.applicants', ['job_id' => $job->id]) }}" class="inline-flex items-center gap-1 text-sm font-medium text-[#073057] hover:text-[#1AAD94]">
                                    <svg class="w-4 h-4 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    {{ number_format($job->applications_count) }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#6B7280]">{{ $job->created_at?->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm text-[#6B7280]">{{ $job->deadline?->format('M d, Y') ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $statusColors[$job->status] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst($job->status) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('employer.applicants', ['job_id' => $job->id]) }}" class="p-2 text-[#6B7280] hover:text-[#1AAD94] hover:bg-[#1AAD94]/10 rounded-lg transition" title="View Applicants">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </a>
                                    <a href="{{ route('employer.edit-job', $job->id) }}" class="p-2 text-[#6B7280] hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    @if($job->slug)
                                        <a href="{{ route('job.detail', $job->slug) }}" target="_blank" class="p-2 text-[#6B7280] hover:text-[#073057] hover:bg-[#F3F4F6] rounded-lg transition" title="View Public Job">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <h3 class="text-lg font-bold text-[#073057]">No jobs found</h3>
                                <p class="mt-2 text-[#6B7280]">Post a job or adjust your filters to see results.</p>
                                <a href="{{ route('employer.new-job') }}" class="mt-4 inline-flex px-5 py-2.5 bg-[#1AAD94] text-white font-semibold rounded-xl">Post New Job</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jobs->hasPages() || $jobs->total() > 0)
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 px-6 py-4 border-t border-[#E5E7EB]">
                <p class="text-sm text-[#6B7280]">Showing {{ $jobs->firstItem() ?? 0 }}-{{ $jobs->lastItem() ?? 0 }} of {{ $jobs->total() }} jobs</p>
                {{ $jobs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
