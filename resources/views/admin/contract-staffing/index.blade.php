@extends('admin.layouts.app')

@section('title', 'Contract Staffing')
@section('page-title', 'Contract Staffing')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#0A1929]">Contract Staffing Roles</h1>
            <p class="text-sm text-gray-500 mt-1">Post contract roles directly. Applications land in your admin inbox.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white border border-gray-200 text-xs text-gray-500">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                <span class="font-semibold text-gray-700">{{ $jobs->total() }}</span> roles
            </span>
            <a href="{{ route('admin.contract-staffing.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-lg shadow transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Add Role
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-5">
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <form method="GET" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 block">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Title, address..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 block">Category</label>
                    <select name="category_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                        <option value="">All</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (int) request('category_id') === (int) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 block">Status</label>
                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                        <option value="">All</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="paused" {{ request('status') === 'paused' ? 'selected' : '' }}>Paused</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-[#073057] text-white text-sm font-semibold rounded-lg hover:brightness-110">Filter</button>
                @if(request()->hasAny(['search', 'category_id', 'status']))
                    <a href="{{ route('admin.contract-staffing.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Clear</a>
                @endif
            </form>
        </div>

        <div class="space-y-3">
            @forelse($jobs as $job)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition overflow-hidden">
                    <div class="flex flex-col sm:flex-row gap-4 p-4">
                        <div class="shrink-0 w-full sm:w-44 h-32 rounded-lg overflow-hidden flex items-center justify-center relative {{ $job->thumbnail ? 'bg-gray-100' : 'bg-gradient-to-br from-[#073057] via-[#0a4275] to-[#1AAD94]' }}">
                            @if ($job->thumbnail)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($job->thumbnail) }}" alt="{{ $job->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="absolute inset-0 flex flex-col items-center justify-center text-white px-2 text-center">
                                    <svg class="w-8 h-8 mb-1 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span class="text-[10px] font-bold uppercase tracking-wider opacity-80">Contract Role</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $job->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ ucfirst($job->status) }}
                                </span>
                                @if ($job->is_featured)
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold bg-amber-100 text-amber-700">Featured</span>
                                @endif
                                @if ($job->is_urgent)
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold bg-red-100 text-red-700">Urgent</span>
                                @endif
                                @if ($job->category)
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold bg-sky-100 text-sky-700">{{ $job->category->name }}</span>
                                @endif
                            </div>
                            <h3 class="font-bold text-[#0A1929] text-base leading-snug">{{ $job->title }}</h3>
                            <p class="text-xs text-gray-500 font-mono mt-0.5">/{{ $job->slug }}</p>

                            <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 text-xs text-gray-500">
                                <div class="flex items-center gap-3 flex-wrap">
                                    @if ($job->location || $job->address)
                                        <span class="inline-flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg> {{ $job->location?->name ?? $job->address }}</span>
                                    @endif
                                    @if ($job->salary_min || $job->salary_max)
                                        <span class="font-bold text-[#073057] text-sm">
                                            {{ $job->salary_min ? number_format($job->salary_min, 0) : '?' }} – {{ $job->salary_max ? number_format($job->salary_max, 0) : '?' }}
                                            <span class="text-xs text-gray-500 font-normal">/ {{ $job->salary_type ?? '—' }}</span>
                                        </span>
                                    @endif
                                    @if ($job->deadline)
                                        <span class="inline-flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> {{ $job->deadline->format('M d, Y') }}</span>
                                    @endif
                                    <a href="{{ route('admin.applications', ['source' => 'contract_staffing']) }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 font-semibold">{{ $job->applications_count }} applicants</a>
                                </div>
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('admin.contract-staffing.edit', $job) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[#1AAD94] hover:bg-[#1AAD94]/10 font-semibold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit
                                    </a>
                                    <a href="{{ route('services.contract-staffing.detail', $job->slug) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-gray-600 hover:bg-gray-100 font-semibold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14"/></svg>
                                        View
                                    </a>
                                    <form method="POST" action="{{ route('admin.contract-staffing.destroy', $job) }}" onsubmit="return confirm('Delete this role? Applications will also be removed.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-red-600 hover:bg-red-50 font-semibold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M11 3a2 2 0 00-2 2v2h6V5a2 2 0 00-2-2h-2z"/></svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-dashed border-gray-300 p-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <p class="text-sm text-gray-500 mb-4">No contract staffing roles yet.</p>
                    <a href="{{ route('admin.contract-staffing.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-lg shadow transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Post your first role
                    </a>
                </div>
            @endforelse
        </div>

        @if($jobs->hasPages())
            <div class="bg-white rounded-xl border border-gray-200 px-5 py-3">{{ $jobs->links() }}</div>
        @endif
    </div>
@endsection
