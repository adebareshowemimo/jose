@extends('admin.layouts.app')

@section('title', 'Company: ' . $company->name)
@section('page-title', 'Company Details')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.companies') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Companies
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Company Info --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" alt="" class="w-20 h-20 rounded-xl object-cover mx-auto mb-3">
                @else
                    <div class="w-20 h-20 bg-[#073057] rounded-xl mx-auto mb-3 flex items-center justify-center text-white text-2xl font-bold">
                        {{ substr($company->name, 0, 1) }}
                    </div>
                @endif
                <h2 class="text-lg font-bold text-gray-900">{{ $company->name }}</h2>
                <p class="text-sm text-gray-500">{{ $company->email ?? '—' }}</p>

                <dl class="mt-4 space-y-2 text-sm text-left">
                    <div class="flex justify-between"><dt class="text-gray-500">Owner</dt><dd class="font-medium">{{ $company->owner?->name ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Location</dt><dd>{{ $company->location?->name ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Size</dt><dd>{{ $company->company_size ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Founded</dt><dd>{{ $company->founded_in ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Website</dt><dd>{{ $company->website ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Jobs</dt><dd class="font-semibold">{{ $company->jobListings->count() }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Reviews</dt><dd>{{ $company->reviews->count() }} ({{ $company->review_score ?? 0 }}★)</dd></div>
                </dl>
            </div>

            @if($company->industries->isNotEmpty())
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Industries</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($company->industries as $industry)
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">{{ $industry->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Admin Controls --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Admin Controls</h3>
                <form method="POST" action="{{ route('admin.companies.update', $company) }}">
                    @csrf @method('PUT')
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                                @foreach(['active', 'inactive', 'pending'] as $s)
                                    <option value="{{ $s }}" {{ $company->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center gap-2 pt-5">
                            <input type="checkbox" name="is_featured" value="1" {{ $company->is_featured ? 'checked' : '' }}
                                class="rounded border-gray-300 text-amber-500 focus:ring-amber-500" id="is_featured">
                            <label for="is_featured" class="text-sm text-gray-700">Featured</label>
                        </div>
                        <div class="flex items-center gap-2 pt-5">
                            <input type="checkbox" name="is_verified" value="1" {{ $company->is_verified ? 'checked' : '' }}
                                class="rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]" id="is_verified">
                            <label for="is_verified" class="text-sm text-gray-700">Verified</label>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-5 py-2 bg-[#1AAD94] text-white text-sm font-medium rounded-lg hover:bg-[#1AAD94]/90">Save</button>
                    </div>
                </form>
            </div>

            {{-- About --}}
            @if($company->about)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-3">About</h3>
                    <div class="text-sm text-gray-600 prose prose-sm max-w-none">{!! nl2br(e($company->about)) !!}</div>
                </div>
            @endif

            {{-- Job Listings --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Job Listings ({{ $company->jobListings->count() }})</h3>
                @if($company->jobListings->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($company->jobListings->take(10) as $job)
                            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $job->title }}</p>
                                    <p class="text-xs text-gray-500">Posted {{ $job->created_at?->diffForHumans() }}</p>
                                </div>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $job->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($job->status ?? 'N/A') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400">No jobs posted yet.</p>
                @endif
            </div>

            {{-- Delete --}}
            <div class="bg-white rounded-xl border border-red-200 p-6">
                <h3 class="text-base font-semibold text-red-600 mb-2">Danger Zone</h3>
                <p class="text-sm text-gray-500 mb-4">Permanently delete this company and all its data.</p>
                <form method="POST" action="{{ route('admin.companies.delete', $company) }}"
                      onsubmit="return confirm('Are you sure you want to delete this company?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-5 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">Delete Company</button>
                </form>
            </div>
        </div>
    </div>
@endsection
