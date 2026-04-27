@extends('layouts.app')

@section('title', ($job['title'] ?? 'Job Detail').' — JOSEOCEANJOBS')

@section('content')
<section class="py-16 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />

        @isset($previewStatus)
            <div class="mb-6 rounded-xl border border-amber-300 bg-amber-50 p-4 flex items-start gap-3">
                <iconify-icon icon="lucide:eye" class="text-amber-600 text-xl mt-0.5"></iconify-icon>
                <div class="text-sm text-amber-800">
                    <p class="font-semibold">Preview mode — this listing is not public yet.</p>
                    <p>Status: <strong class="capitalize">{{ $previewStatus }}</strong>. Only you (and admins) can see this page until it's approved and active.</p>
                </div>
            </div>
        @endisset

        <div class="grid lg:grid-cols-[1fr_320px] gap-6">
            <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-8">
                <h1 class="text-[38px] font-extrabold text-[#073057] leading-tight mb-3">{{ $job['title'] ?? 'Job Detail' }}</h1>
                <p class="text-[#6B7280] mb-6">{{ $job['company'] ?? '' }} • {{ $job['location'] ?? '' }}</p>

                <div class="flex flex-wrap gap-3 mb-6">
                    <span class="px-3 py-1 rounded-full bg-[#1AAD94]/10 text-[#1AAD94] text-xs font-bold uppercase">{{ $job['type'] ?? 'Open' }}</span>
                    <span class="px-3 py-1 rounded-full bg-[#16A34A]/10 text-[#16A34A] text-xs font-bold">{{ $job['salary'] ?? 'Negotiable' }}</span>
                </div>

                <h2 class="text-[#073057] text-xl font-bold mb-3">Role Summary</h2>
                <p class="text-[#2C2C2C] leading-relaxed mb-8">{{ $job['description'] ?? '' }}</p>

                @if(!empty($job['requirements']))
                    <h2 class="text-[#073057] text-xl font-bold mb-3">Requirements</h2>
                    <ul class="space-y-2 mb-8">
                        @foreach($job['requirements'] as $requirement)
                            <li class="flex items-start gap-2 text-[#2C2C2C]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1"></iconify-icon><span>{{ $requirement }}</span></li>
                        @endforeach
                    </ul>
                @endif

                <div class="flex flex-wrap gap-3">
                    <x-ui.button href="#" variant="dark">Apply Now</x-ui.button>
                    <x-ui.button :href="route('job.index')" variant="outline">Back to Jobs</x-ui.button>
                </div>
            </div>

            <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-6 h-fit">
                <h3 class="text-[#073057] text-lg font-bold mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <x-ui.button href="#" variant="primary" class="w-full">Save Job</x-ui.button>
                    <x-ui.button :href="route('contact.index')" variant="outline" class="w-full">Ask Recruiter</x-ui.button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
