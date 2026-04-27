@extends('layouts.dashboard')

@section('title', 'Edit Job')
@section('page-title', 'Edit Job')

@section('sidebar-nav')
    @include('pages.dashboard.employer.partials.sidebar')
@endsection

@section('content')
<form method="POST" action="{{ route('employer.edit-job.update', $job->id) }}" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#073057]">Edit Job</h2>
            <p class="text-[#6B7280]">Update the listing details. Changes save immediately.</p>
        </div>
        @php
            $statusColors = [
                'active' => 'bg-emerald-100 text-emerald-700',
                'pending' => 'bg-amber-100 text-amber-700',
                'draft' => 'bg-gray-100 text-gray-700',
                'expired' => 'bg-red-100 text-red-700',
                'closed' => 'bg-slate-100 text-slate-700',
            ];
        @endphp
        <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $statusColors[$job->status] ?? 'bg-gray-100 text-gray-700' }}">
            Status: {{ ucfirst($job->status) }}
        </span>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl p-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm">
            <p class="font-semibold mb-2">Please fix the errors below.</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-[#E5E7EB] p-6">
        <h3 class="font-semibold text-[#073057] text-lg mb-4">Job Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-[#073057] mb-2">Job Title *</label>
                <input type="text" name="title" value="{{ old('title', $job->title) }}" required class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#073057] mb-2">Category</label>
                <select name="category_id" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('category_id', $job->category_id) === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#073057] mb-2">Job Type</label>
                <select name="job_type_id" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                    <option value="">Select Type</option>
                    @foreach($jobTypes as $type)
                        <option value="{{ $type->id }}" @selected((string) old('job_type_id', $job->job_type_id) === (string) $type->id)>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#073057] mb-2">Location</label>
                <select name="location_id" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                    <option value="">Select Location</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}" @selected((string) old('location_id', $job->location_id) === (string) $location->id)>{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#073057] mb-2">Address / Joining Port</label>
                <input type="text" name="address" value="{{ old('address', $job->address) }}" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-[#073057] mb-2">Job Description *</label>
                <textarea name="description" rows="6" required class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none resize-none">{{ old('description', $job->description) }}</textarea>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-[#E5E7EB] p-6">
        <h3 class="font-semibold text-[#073057] text-lg mb-4">Qualifications & Requirements</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-[#073057] mb-2">Minimum Experience</label>
                <input type="text" name="experience_required" value="{{ old('experience_required', $job->experience_required) }}" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#073057] mb-2">Vacancies</label>
                <input type="number" name="vacancies" value="{{ old('vacancies', $job->vacancies) }}" min="1" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-[#073057] mb-2">Qualification / Requirements</label>
                <textarea name="qualification" rows="5" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none resize-none">{{ old('qualification', $job->qualification) }}</textarea>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-[#E5E7EB] p-6">
        <h3 class="font-semibold text-[#073057] text-lg mb-4">Compensation & Application</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-[#073057] mb-2">Salary Range</label>
                <div class="flex gap-4">
                    <input type="number" name="salary_min" value="{{ old('salary_min', $job->salary_min) }}" min="0" placeholder="Min" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                    <input type="number" name="salary_max" value="{{ old('salary_max', $job->salary_max) }}" min="0" placeholder="Max" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#073057] mb-2">Salary Type</label>
                <select name="salary_type" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                    <option value="">Select</option>
                    @foreach(['hourly' => 'Hourly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('salary_type', $job->salary_type) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#073057] mb-2">Hours</label>
                <input type="text" name="hours" value="{{ old('hours', $job->hours) }}" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#073057] mb-2">Hours Type</label>
                <select name="hours_type" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                    <option value="">Select</option>
                    <option value="full-time" @selected(old('hours_type', $job->hours_type) === 'full-time')>Full-time</option>
                    <option value="part-time" @selected(old('hours_type', $job->hours_type) === 'part-time')>Part-time</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#073057] mb-2">Application Deadline</label>
                <input type="date" name="deadline" value="{{ old('deadline', optional($job->deadline)->toDateString()) }}" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
            </div>
            <div class="md:col-span-2 p-4 rounded-xl bg-[#F0FBF8] border border-[#1AAD94]/30 text-sm text-[#073057]">
                Candidates apply directly through the JoseMaritime platform. You'll review applications from your dashboard.
            </div>
            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-3">
                <label class="flex items-center gap-3 p-4 border border-[#E5E7EB] rounded-xl cursor-pointer hover:border-[#1AAD94] transition">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $job->is_featured)) class="w-5 h-5 text-[#1AAD94] rounded border-[#E5E7EB] focus:ring-[#1AAD94]">
                    <span class="font-medium text-[#073057]">Request Featured Job</span>
                </label>
                <label class="flex items-center gap-3 p-4 border border-[#E5E7EB] rounded-xl cursor-pointer hover:border-[#1AAD94] transition">
                    <input type="checkbox" name="is_urgent" value="1" @checked(old('is_urgent', $job->is_urgent)) class="w-5 h-5 text-[#1AAD94] rounded border-[#E5E7EB] focus:ring-[#1AAD94]">
                    <span class="font-medium text-[#073057]">Mark as Urgent</span>
                </label>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap justify-between gap-4">
        <a href="{{ route('employer.manage-jobs') }}" class="px-6 py-3 border border-[#E5E7EB] text-[#4B5563] font-semibold rounded-xl hover:bg-[#F9FAFB] transition">Back to Jobs</a>
        <div class="flex gap-3">
            @if($job->slug)
                <a href="{{ route('job.detail', $job->slug) }}" target="_blank" class="px-6 py-3 border border-[#E5E7EB] text-[#4B5563] font-semibold rounded-xl hover:bg-[#F9FAFB] transition">Preview</a>
            @endif
            <button type="submit" class="px-6 py-3 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition">Save Changes</button>
        </div>
    </div>
</form>
@endsection
