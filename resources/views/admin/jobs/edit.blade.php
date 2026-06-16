@extends('admin.layouts.app')

@section('title', 'Edit · ' . $job->title)
@section('page-title', 'Edit Job')

@section('content')
@php
    $inputClass = 'w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#1AAD94] focus:border-[#1AAD94] text-sm';
    $labelClass = 'block text-xs font-semibold text-gray-500 mb-1';
@endphp

<a href="{{ route('admin.jobs.show', $job) }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-800 mb-4">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Back to job
</a>

@if ($errors->any())
    <div class="mb-5 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
        @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.jobs.update', $job) }}" class="space-y-6 max-w-4xl">
    @csrf
    @method('PUT')

    {{-- Basic info --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Job Information</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="{{ $labelClass }}">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $job->title) }}" required class="{{ $inputClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}">Category</label>
                <select name="category_id" class="{{ $inputClass }}">
                    <option value="">— None —</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected((int) old('category_id', $job->category_id) === $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">Job Type</label>
                <select name="job_type_id" class="{{ $inputClass }}">
                    <option value="">— None —</option>
                    @foreach ($jobTypes as $jt)
                        <option value="{{ $jt->id }}" @selected((int) old('job_type_id', $job->job_type_id) === $jt->id)>{{ $jt->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">Country</label>
                <select name="location_id" class="{{ $inputClass }}">
                    <option value="">— None —</option>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->id }}" @selected((int) old('location_id', $job->location_id) === $loc->id)>{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">Address</label>
                <input type="text" name="address" value="{{ old('address', $job->address) }}" class="{{ $inputClass }}">
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $labelClass }}">Description <span class="text-red-500">*</span></label>
                <textarea name="description" rows="8" required class="{{ $inputClass }}">{{ old('description', $job->description) }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $labelClass }}">Requirements / Qualifications <span class="text-gray-400 font-normal">(one per line)</span></label>
                <textarea name="qualification" rows="5" class="{{ $inputClass }}">{{ old('qualification', $job->qualification) }}</textarea>
            </div>
        </div>
    </div>

    {{-- Compensation & details --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Compensation &amp; Details</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="{{ $labelClass }}">Salary min</label>
                <input type="number" step="0.01" min="0" name="salary_min" value="{{ old('salary_min', $job->salary_min) }}" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}">Salary max</label>
                <input type="number" step="0.01" min="0" name="salary_max" value="{{ old('salary_max', $job->salary_max) }}" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}">Salary type</label>
                <select name="salary_type" class="{{ $inputClass }}">
                    <option value="">—</option>
                    @foreach (['hourly', 'monthly', 'yearly'] as $st)
                        <option value="{{ $st }}" @selected(old('salary_type', $job->salary_type) === $st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">Experience required</label>
                <input type="text" name="experience_required" value="{{ old('experience_required', $job->experience_required) }}" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}">Vacancies</label>
                <input type="number" min="1" name="vacancies" value="{{ old('vacancies', $job->vacancies) }}" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}">Deadline</label>
                <input type="date" name="deadline" value="{{ old('deadline', $job->deadline?->format('Y-m-d')) }}" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}">Hours</label>
                <input type="text" name="hours" value="{{ old('hours', $job->hours) }}" placeholder="e.g. 40 hrs/week" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}">Hours type</label>
                <select name="hours_type" class="{{ $inputClass }}">
                    <option value="">—</option>
                    @foreach (['full-time', 'part-time'] as $ht)
                        <option value="{{ $ht }}" @selected(old('hours_type', $job->hours_type) === $ht)>{{ ucfirst(str_replace('-', ' ', $ht)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">Gender preference</label>
                <select name="gender_preference" class="{{ $inputClass }}">
                    @foreach (['any' => 'Any (no preference)', 'male' => 'Male', 'female' => 'Female'] as $gp => $gpLabel)
                        <option value="{{ $gp }}" @selected(old('gender_preference', $job->gender_preference ?? 'any') === $gp)>{{ $gpLabel }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Status & visibility --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Status &amp; Visibility</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="{{ $labelClass }}">Status <span class="text-red-500">*</span></label>
                <select name="status" required class="{{ $inputClass }}">
                    @foreach (['draft', 'pending', 'active', 'paused', 'closed', 'expired'] as $s)
                        <option value="{{ $s }}" @selected(old('status', $job->status) === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col justify-center gap-2 pt-1">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="is_approved" value="1" @checked(old('is_approved', $job->is_approved)) class="rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
                    Approved (visible publicly once active)
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $job->is_featured)) class="rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
                    Featured
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="is_urgent" value="1" @checked(old('is_urgent', $job->is_urgent)) class="rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
                    Urgent
                </label>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="px-5 py-2.5 text-sm font-semibold bg-[#1AAD94] text-white rounded-lg hover:bg-[#1AAD94]/90">Save Changes</button>
        <a href="{{ route('admin.jobs.show', $job) }}" class="px-5 py-2.5 text-sm font-medium border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cancel</a>
    </div>
</form>
@endsection
