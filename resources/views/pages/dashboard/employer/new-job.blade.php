@extends('layouts.dashboard')

@section('title', 'Post New Job')
@section('page-title', 'Post New Job')

@section('sidebar-nav')
    @include('pages.dashboard.employer.partials.sidebar')
@endsection

@section('content')
<form method="POST" action="{{ route('employer.new-job.store') }}"
    x-data="{
        step: 1,
        action: 'submit',
        summary: {},
        buildSummary() {
            const form = this.$root;
            const get = (name) => {
                const el = form.elements[name];
                if (!el) return '';
                if (el.tagName === 'SELECT') return el.value ? (el.options[el.selectedIndex]?.text || '') : '';
                if (el.type === 'checkbox') return el.checked;
                return el.value;
            };
            this.summary = {
                title: get('title'),
                category: get('category_id'),
                job_type: get('job_type_id'),
                location: get('location_id'),
                address: get('address'),
                description: get('description'),
                experience: get('experience_required'),
                vacancies: get('vacancies'),
                qualification: get('qualification'),
                salary_min: get('salary_min'),
                salary_max: get('salary_max'),
                salary_type: get('salary_type'),
                hours: get('hours'),
                hours_type: get('hours_type'),
                deadline: get('deadline'),
                is_featured: get('is_featured'),
                is_urgent: get('is_urgent'),
            };
        },
        salaryRange() {
            const min = this.summary.salary_min, max = this.summary.salary_max;
            if (min && max) return min + ' – ' + max;
            if (min) return 'From ' + min;
            if (max) return 'Up to ' + max;
            return '';
        }
    }"
    x-init="$watch('step', v => { if (v === 4) buildSummary(); })"
    class="space-y-6">
    @csrf
    <input type="hidden" name="submit_action" :value="action">

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#073057]">Post a New Job</h2>
            <p class="text-[#6B7280]">Submitted jobs require admin review before they go live.</p>
        </div>
        <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-sm font-semibold">Admin review required</span>
    </div>

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
        <div class="flex items-center justify-between relative">
            <div class="absolute top-5 left-0 right-0 h-0.5 bg-[#E5E7EB] -z-10"></div>
            <div class="absolute top-5 left-0 h-0.5 bg-[#1AAD94] -z-10 transition-all" :style="'width: ' + ((step - 1) * 33.33) + '%'"></div>

            @foreach([1 => 'Job Details', 2 => 'Requirements', 3 => 'Compensation', 4 => 'Review'] as $number => $label)
                <button type="button" class="flex flex-col items-center" @click="step = {{ $number }}">
                    <span :class="step >= {{ $number }} ? 'bg-[#1AAD94] text-white' : 'bg-[#E5E7EB] text-[#6B7280]'" class="w-10 h-10 rounded-full flex items-center justify-center font-semibold transition">{{ $number }}</span>
                    <span class="text-sm mt-2" :class="step >= {{ $number }} ? 'text-[#073057] font-medium' : 'text-[#6B7280]'">{{ $label }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <div x-show="step === 1" class="space-y-6">
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-6">
            <h3 class="font-semibold text-[#073057] text-lg mb-4">Job Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-[#073057] mb-2">Job Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g. Chief Officer - Container Vessel" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#073057] mb-2">Category</label>
                    <select name="category_id" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#073057] mb-2">Job Type</label>
                    <select name="job_type_id" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                        <option value="">Select Type</option>
                        @foreach($jobTypes as $type)
                            <option value="{{ $type->id }}" @selected((string) old('job_type_id') === (string) $type->id)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#073057] mb-2">Location</label>
                    <select name="location_id" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                        <option value="">Select Location</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" @selected((string) old('location_id') === (string) $location->id)>{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#073057] mb-2">Address / Joining Port</label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="e.g. Rotterdam, Netherlands" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-[#073057] mb-2">Job Description *</label>
                    <textarea name="description" rows="6" required placeholder="Describe the role and responsibilities..." class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none resize-none">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>
        <div class="flex justify-end">
            <button type="button" @click="step = 2" class="px-6 py-3 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition">Next: Requirements</button>
        </div>
    </div>

    <div x-show="step === 2" class="space-y-6">
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-6">
            <h3 class="font-semibold text-[#073057] text-lg mb-4">Qualifications & Requirements</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-[#073057] mb-2">Minimum Experience</label>
                    <input type="text" name="experience_required" value="{{ old('experience_required') }}" placeholder="e.g. 5+ years, STCW required" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#073057] mb-2">Vacancies</label>
                    <input type="number" name="vacancies" value="{{ old('vacancies') }}" min="1" placeholder="1" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-[#073057] mb-2">Qualification / Requirements</label>
                    <textarea name="qualification" rows="5" placeholder="List certifications, licenses, and required skills..." class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none resize-none">{{ old('qualification') }}</textarea>
                </div>
            </div>
        </div>
        <div class="flex justify-between gap-4">
            <button type="button" @click="step = 1" class="px-6 py-3 border border-[#E5E7EB] text-[#4B5563] font-semibold rounded-xl hover:bg-[#F9FAFB] transition">Back</button>
            <button type="button" @click="step = 3" class="px-6 py-3 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition">Next: Compensation</button>
        </div>
    </div>

    <div x-show="step === 3" class="space-y-6">
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-6">
            <h3 class="font-semibold text-[#073057] text-lg mb-4">Compensation & Application</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-[#073057] mb-2">Salary Range</label>
                    <div class="flex gap-4">
                        <input type="number" name="salary_min" value="{{ old('salary_min') }}" min="0" placeholder="Min" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                        <input type="number" name="salary_max" value="{{ old('salary_max') }}" min="0" placeholder="Max" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#073057] mb-2">Salary Type</label>
                    <select name="salary_type" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                        <option value="">Select</option>
                        @foreach(['hourly' => 'Hourly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('salary_type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#073057] mb-2">Hours</label>
                    <input type="text" name="hours" value="{{ old('hours') }}" placeholder="e.g. 40 hours/week or 4 months on / 2 off" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#073057] mb-2">Hours Type</label>
                    <select name="hours_type" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                        <option value="">Select</option>
                        <option value="full-time" @selected(old('hours_type') === 'full-time')>Full-time</option>
                        <option value="part-time" @selected(old('hours_type') === 'part-time')>Part-time</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#073057] mb-2">Application Deadline</label>
                    <input type="date" name="deadline" value="{{ old('deadline') }}" min="{{ now()->toDateString() }}" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                </div>
                <div class="md:col-span-2 p-4 rounded-xl bg-[#F0FBF8] border border-[#1AAD94]/30 text-sm text-[#073057]">
                    Candidates apply directly through the JoseMaritime platform. You'll review applications from your dashboard.
                </div>
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 p-4 border border-[#E5E7EB] rounded-xl cursor-pointer hover:border-[#1AAD94] transition">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured')) class="w-5 h-5 text-[#1AAD94] rounded border-[#E5E7EB] focus:ring-[#1AAD94]">
                        <span class="font-medium text-[#073057]">Request Featured Job</span>
                    </label>
                    <label class="flex items-center gap-3 p-4 border border-[#E5E7EB] rounded-xl cursor-pointer hover:border-[#1AAD94] transition">
                        <input type="checkbox" name="is_urgent" value="1" @checked(old('is_urgent')) class="w-5 h-5 text-[#1AAD94] rounded border-[#E5E7EB] focus:ring-[#1AAD94]">
                        <span class="font-medium text-[#073057]">Mark as Urgent</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="flex justify-between gap-4">
            <button type="button" @click="step = 2" class="px-6 py-3 border border-[#E5E7EB] text-[#4B5563] font-semibold rounded-xl hover:bg-[#F9FAFB] transition">Back</button>
            <button type="button" @click="step = 4" class="px-6 py-3 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition">Next: Review</button>
        </div>
    </div>

    <div x-show="step === 4" class="space-y-6">
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-6 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-[#073057] text-lg">Review & Submit</h3>
                <p class="text-sm text-[#6B7280]">Confirm the details before submitting.</p>
            </div>

            <section>
                <h4 class="text-xs font-semibold text-[#1AAD94] uppercase tracking-wide mb-3">Job Details</h4>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-xs text-[#6B7280] mb-1">Job Title</dt>
                        <dd class="text-[#073057] font-medium" x-text="summary.title || '—'"></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-[#6B7280] mb-1">Category</dt>
                        <dd class="text-[#073057] font-medium" x-text="summary.category || '—'"></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-[#6B7280] mb-1">Job Type</dt>
                        <dd class="text-[#073057] font-medium" x-text="summary.job_type || '—'"></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-[#6B7280] mb-1">Location</dt>
                        <dd class="text-[#073057] font-medium" x-text="summary.location || '—'"></dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-xs text-[#6B7280] mb-1">Address / Joining Port</dt>
                        <dd class="text-[#073057] font-medium" x-text="summary.address || '—'"></dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-xs text-[#6B7280] mb-1">Description</dt>
                        <dd class="text-[#073057] whitespace-pre-line" x-text="summary.description || '—'"></dd>
                    </div>
                </dl>
            </section>

            <section class="border-t border-[#E5E7EB] pt-6">
                <h4 class="text-xs font-semibold text-[#1AAD94] uppercase tracking-wide mb-3">Requirements</h4>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-xs text-[#6B7280] mb-1">Minimum Experience</dt>
                        <dd class="text-[#073057] font-medium" x-text="summary.experience || '—'"></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-[#6B7280] mb-1">Vacancies</dt>
                        <dd class="text-[#073057] font-medium" x-text="summary.vacancies || '—'"></dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-xs text-[#6B7280] mb-1">Qualifications</dt>
                        <dd class="text-[#073057] whitespace-pre-line" x-text="summary.qualification || '—'"></dd>
                    </div>
                </dl>
            </section>

            <section class="border-t border-[#E5E7EB] pt-6">
                <h4 class="text-xs font-semibold text-[#1AAD94] uppercase tracking-wide mb-3">Compensation & Application</h4>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-xs text-[#6B7280] mb-1">Salary Range</dt>
                        <dd class="text-[#073057] font-medium" x-text="salaryRange() || '—'"></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-[#6B7280] mb-1">Salary Type</dt>
                        <dd class="text-[#073057] font-medium" x-text="summary.salary_type || '—'"></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-[#6B7280] mb-1">Hours</dt>
                        <dd class="text-[#073057] font-medium" x-text="summary.hours || '—'"></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-[#6B7280] mb-1">Hours Type</dt>
                        <dd class="text-[#073057] font-medium" x-text="summary.hours_type || '—'"></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-[#6B7280] mb-1">Application Deadline</dt>
                        <dd class="text-[#073057] font-medium" x-text="summary.deadline || '—'"></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-[#6B7280] mb-1">Application Method</dt>
                        <dd class="text-[#073057] font-medium">Platform applications only</dd>
                    </div>
                    <div class="md:col-span-2 flex flex-wrap gap-2 pt-1" x-show="summary.is_featured || summary.is_urgent">
                        <span x-show="summary.is_featured" class="inline-flex items-center px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold">Featured Job (requested)</span>
                        <span x-show="summary.is_urgent" class="inline-flex items-center px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">Urgent</span>
                    </div>
                </dl>
            </section>

            <div class="bg-[#F9FAFB] rounded-xl p-4 border border-[#E5E7EB]">
                <p class="text-sm text-[#6B7280]">New jobs submitted from this page are saved as <strong>Pending</strong> and are not shown publicly until an admin approves them.</p>
            </div>
        </div>

        <div class="flex justify-between gap-4">
            <button type="button" @click="step = 3" class="px-6 py-3 border border-[#E5E7EB] text-[#4B5563] font-semibold rounded-xl hover:bg-[#F9FAFB] transition">Back</button>
            <div class="flex gap-3">
                <button type="submit" @click="action = 'draft'" class="px-6 py-3 border border-[#E5E7EB] text-[#4B5563] font-semibold rounded-xl hover:bg-[#F9FAFB] transition">Save as Draft</button>
                <button type="submit" @click="action = 'submit'" class="px-6 py-3 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition">Submit for Review</button>
            </div>
        </div>
    </div>
</form>
@endsection
