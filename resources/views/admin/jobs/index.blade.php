@extends('admin.layouts.app')

@section('title', 'Job Listings')
@section('page-title', 'Job Listings')

@section('content')
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="text-xs font-medium text-gray-500 mb-1 block">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Job title or company..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                    <option value="">All</option>
                    @foreach(['pending', 'active', 'paused', 'closed', 'expired', 'draft'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Sort</label>
                <select name="sort" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                    @foreach(['newest' => 'Newest', 'oldest' => 'Oldest', 'deadline' => 'Deadline', 'title' => 'Title (A–Z)'] as $value => $label)
                        <option value="{{ $value }}" {{ request('sort', 'newest') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-[#073057] text-white text-sm font-medium rounded-lg hover:bg-[#073057]/90">Filter</button>
            @if(request()->hasAny(['search', 'status', 'sort']))
                <a href="{{ route('admin.jobs') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Job Title</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Company</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Status</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Posted</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Deadline</th>
                        <th class="text-right px-5 py-3 font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($jobs as $job)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-900">{{ $job->title }}</p>
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $job->company?->name ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <span class="text-xs px-2 py-1 rounded-full
                                    {{ $job->status === 'active' ? 'bg-green-100 text-green-700' : ($job->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : (in_array($job->status, ['closed', 'expired']) ? 'bg-red-100 text-red-700' : ($job->status === 'paused' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600'))) }}">
                                    {{ ucfirst($job->status ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500">{{ $job->created_at?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $job->deadline?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.jobs.show', $job) }}" class="text-gray-600 hover:text-[#073057] hover:underline text-sm">View</a>

                                    {{-- Native select avoids the overflow-clipping that hid the old dropdown, and auto-submits the status change. --}}
                                    <form method="POST" action="{{ route('admin.jobs.update', $job) }}" class="inline-flex">
                                        @csrf @method('PUT')
                                        <select name="status" onchange="this.form.submit()" title="Change status"
                                                class="px-2 py-1 border border-gray-300 rounded-md text-xs text-gray-700 cursor-pointer focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                                            @foreach(['pending', 'active', 'paused', 'closed', 'expired', 'draft'] as $s)
                                                <option value="{{ $s }}" {{ $job->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                            @endforeach
                                        </select>
                                    </form>

                                    <form method="POST" action="{{ route('admin.jobs.delete', $job) }}"
                                          onsubmit="return confirm('Delete this job?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:underline text-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No jobs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jobs->total() > 0)
            <div class="px-5 py-3 border-t border-gray-200 flex flex-wrap items-center justify-between gap-3">
                <p class="text-xs text-gray-500">Showing {{ $jobs->firstItem() }}–{{ $jobs->lastItem() }} of {{ $jobs->total() }} jobs</p>
                @if($jobs->hasPages())
                    <div>{{ $jobs->links() }}</div>
                @endif
            </div>
        @endif
    </div>
@endsection
