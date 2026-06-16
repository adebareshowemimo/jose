@extends('layouts.app')

@section('title', ($job->title ?? 'Contract Role') . ' — Jose Consulting Limited')
@section('meta_description', $pageDescription ?? '')

@section('content')
@php
    // Mirror the regular job-detail page (pages/jobs/detail) so contract roles
    // present identically: title, company • location, type/salary pills, summary,
    // requirements and the apply sidebar.
    $type = $job->jobType?->name ?? ucfirst((string) ($job->hours_type ?: 'Contract'));
    $company = $job->company?->name ?? 'Jose Consulting Limited';
    $location = $job->location?->name ?? $job->address ?? 'Worldwide';
    $salary = ($job->salary_min || $job->salary_max)
        ? trim(($job->salary_min ? number_format((float) $job->salary_min) : '') . ' - ' . ($job->salary_max ? number_format((float) $job->salary_max) : '') . ' ' . ($job->salary_type ?? ''))
        : 'Not disclosed';
    $requirements = array_filter(preg_split('/\r\n|\r|\n/', (string) $job->qualification));
@endphp
<section class="py-16 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />

        <div class="grid lg:grid-cols-[1fr_360px] gap-6">
            <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-8">
                <h1 class="text-[38px] font-extrabold text-[#073057] leading-tight mb-3">{{ $job->title }}</h1>
                <p class="text-[#6B7280] mb-6">{{ $company }} • {{ $location }}</p>

                <div class="flex flex-wrap gap-3 mb-6">
                    <span class="px-3 py-1 rounded-full bg-[#1AAD94]/10 text-[#1AAD94] text-xs font-bold uppercase">{{ $type }}</span>
                    <span class="px-3 py-1 rounded-full bg-[#16A34A]/10 text-[#16A34A] text-xs font-bold">{{ $salary }}</span>
                </div>

                <h2 class="text-[#073057] text-xl font-bold mb-3">Role Summary</h2>
                <div class="text-[#2C2C2C] leading-relaxed mb-8 prose prose-slate max-w-none">{!! $job->description !!}</div>

                @if(!empty($requirements))
                    <h2 class="text-[#073057] text-xl font-bold mb-3">Requirements</h2>
                    <ul class="space-y-2 mb-8">
                        @foreach($requirements as $requirement)
                            <li class="flex items-start gap-2 text-[#2C2C2C]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1"></iconify-icon><span>{{ $requirement }}</span></li>
                        @endforeach
                    </ul>
                @endif

                <div class="flex flex-wrap gap-3">
                    <x-ui.button :href="route('services.contract-staffing.jobs')" variant="outline">Back to Roles</x-ui.button>
                </div>
            </div>

            <div class="space-y-4 h-fit">
                <x-jobs.application-form :job="$job" />
                <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-6">
                    <h3 class="text-[#073057] text-base font-bold mb-3">Need help?</h3>
                    <x-ui.button :href="route('contact.index')" variant="outline" class="w-full">Ask Recruiter</x-ui.button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
