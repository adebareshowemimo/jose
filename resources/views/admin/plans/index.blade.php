@extends('admin.layouts.app')

@section('title', 'Manage Plans')
@section('page-title', 'Plans')

@section('content')
    {{-- Create New Plan --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6" x-data="{ open: false }">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Pricing Plans</h3>
            <button @click="open = !open" class="px-4 py-2 bg-[#1AAD94] text-white text-sm font-medium rounded-lg hover:bg-[#1AAD94]/90">
                <span x-text="open ? 'Cancel' : '+ New Plan'"></span>
            </button>
        </div>

        <form x-show="open" x-cloak method="POST" action="{{ route('admin.plans.store') }}" class="mt-6 border-t border-gray-100 pt-6">
            @csrf
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-medium text-gray-600 mb-1 block">Plan Name *</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600 mb-1 block">Monthly Price *</label>
                    <input type="number" name="monthly_price" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600 mb-1 block">Annual Price *</label>
                    <input type="number" name="annual_price" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600 mb-1 block">Max Job Posts *</label>
                    <input type="number" name="max_job_posts" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600 mb-1 block">Max Featured Jobs *</label>
                    <input type="number" name="max_featured_jobs" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600 mb-1 block">For Role *</label>
                    <select name="role_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600 mb-1 block">Sort Order</label>
                    <input type="number" name="sort_order" value="0" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                </div>
                <div class="flex items-center gap-4 pt-5">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="resume_access" value="1" class="rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
                        <span class="text-sm text-gray-700">Resume Access</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_recommended" value="1" class="rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
                        <span class="text-sm text-gray-700">Recommended</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
                        <span class="text-sm text-gray-700">Active</span>
                    </label>
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="text-xs font-medium text-gray-600 mb-1 block">Description</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent"></textarea>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit" class="px-5 py-2 bg-[#073057] text-white text-sm font-medium rounded-lg hover:bg-[#073057]/90">Create Plan</button>
            </div>
        </form>
    </div>

    {{-- Plans Grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($plans as $plan)
            <div class="bg-white rounded-xl border border-gray-200 {{ $plan->is_recommended ? 'ring-2 ring-[#1AAD94]' : '' }}" x-data="{ editing: false }">
                @if($plan->is_recommended)
                    <div class="bg-[#1AAD94] text-white text-xs font-semibold text-center py-1 rounded-t-xl">RECOMMENDED</div>
                @endif
                <div class="p-6">
                    {{-- Display Mode --}}
                    <div x-show="!editing">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $plan->name }}</h3>
                                <span class="text-xs text-gray-500">{{ ucfirst($plan->role?->name ?? '') }}</span>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full {{ $plan->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $plan->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="mb-4">
                            <p class="text-3xl font-bold text-[#073057]">${{ number_format($plan->monthly_price, 2) }}<span class="text-sm font-normal text-gray-500">/mo</span></p>
                            <p class="text-sm text-gray-500">${{ number_format($plan->annual_price, 2) }}/year</p>
                        </div>
                        <ul class="space-y-2 text-sm text-gray-600 mb-4">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#1AAD94]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                {{ $plan->max_job_posts }} Job Posts
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#1AAD94]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                {{ $plan->max_featured_jobs }} Featured Jobs
                            </li>
                            <li class="flex items-center gap-2">
                                @if($plan->resume_access)
                                    <svg class="w-4 h-4 text-[#1AAD94]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                @else
                                    <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                @endif
                                Resume Access
                            </li>
                        </ul>
                        <p class="text-xs text-gray-400 mb-4">{{ $plan->subscriptions_count }} subscriber(s)</p>
                        @if($plan->description)
                            <p class="text-sm text-gray-500 mb-4">{{ $plan->description }}</p>
                        @endif
                        <div class="flex gap-2">
                            <button @click="editing = true" class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Edit</button>
                            @if($plan->subscriptions_count === 0)
                                <form method="POST" action="{{ route('admin.plans.delete', $plan) }}" onsubmit="return confirm('Delete this plan?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-2 text-sm text-red-600 border border-red-200 rounded-lg hover:bg-red-50">Delete</button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- Edit Mode --}}
                    <form x-show="editing" x-cloak method="POST" action="{{ route('admin.plans.update', $plan) }}">
                        @csrf @method('PUT')
                        <div class="space-y-3">
                            <input type="text" name="name" value="{{ $plan->name }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs text-gray-500">Monthly $</label>
                                    <input type="number" name="monthly_price" value="{{ $plan->monthly_price }}" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Annual $</label>
                                    <input type="number" name="annual_price" value="{{ $plan->annual_price }}" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs text-gray-500">Max Jobs</label>
                                    <input type="number" name="max_job_posts" value="{{ $plan->max_job_posts }}" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Max Featured</label>
                                    <input type="number" name="max_featured_jobs" value="{{ $plan->max_featured_jobs }}" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                </div>
                            </div>
                            <select name="role_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ $plan->role_id == $role->id ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                            <input type="number" name="sort_order" value="{{ $plan->sort_order }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Sort order">
                            <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Description">{{ $plan->description }}</textarea>
                            <div class="flex flex-wrap gap-3">
                                <label class="flex items-center gap-1.5"><input type="checkbox" name="resume_access" value="1" {{ $plan->resume_access ? 'checked' : '' }} class="rounded border-gray-300 text-[#1AAD94]"><span class="text-xs">Resume Access</span></label>
                                <label class="flex items-center gap-1.5"><input type="checkbox" name="is_recommended" value="1" {{ $plan->is_recommended ? 'checked' : '' }} class="rounded border-gray-300 text-[#1AAD94]"><span class="text-xs">Recommended</span></label>
                                <label class="flex items-center gap-1.5"><input type="checkbox" name="is_active" value="1" {{ $plan->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-[#1AAD94]"><span class="text-xs">Active</span></label>
                            </div>
                        </div>
                        <div class="flex gap-2 mt-4">
                            <button type="submit" class="flex-1 px-3 py-2 text-sm bg-[#1AAD94] text-white rounded-lg hover:bg-[#1AAD94]/90">Save</button>
                            <button type="button" @click="editing = false" class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="sm:col-span-2 lg:col-span-3 bg-white rounded-xl border border-gray-200 p-10 text-center text-gray-400">
                No plans created yet. Click "+ New Plan" above to create one.
            </div>
        @endforelse
    </div>
@endsection
