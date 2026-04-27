@extends('layouts.app')

@section('title', ($company['name'] ?? 'Company Profile').' — JOSEOCEANJOBS')

@section('content')
<section class="py-16 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />

        <div class="grid lg:grid-cols-[1fr_320px] gap-6">
            <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-8">
                <h1 class="text-[38px] font-extrabold text-[#073057] leading-tight mb-3">{{ $company['name'] ?? 'Company Profile' }}</h1>
                <p class="text-[#6B7280] mb-6">{{ $company['sector'] ?? '' }} • {{ $company['location'] ?? '' }}</p>

                <div class="flex flex-wrap gap-3 mb-6">
                    <span class="px-3 py-1 rounded-full bg-[#1AAD94]/10 text-[#1AAD94] text-xs font-bold uppercase">{{ $company['sector'] ?? 'Sector' }}</span>
                    <span class="px-3 py-1 rounded-full bg-[#16A34A]/10 text-[#16A34A] text-xs font-bold">Open Roles: {{ $company['open_roles'] ?? 0 }}</span>
                </div>

                <h2 class="text-[#073057] text-xl font-bold mb-3">About Company</h2>
                <p class="text-[#2C2C2C] leading-relaxed mb-8">{{ $company['about'] ?? '' }}</p>

                <h2 class="text-[#073057] text-xl font-bold mb-3">Benefits</h2>
                <ul class="space-y-2 mb-8">
                    @foreach(($company['benefits'] ?? []) as $benefit)
                        <li class="flex items-start gap-2 text-[#2C2C2C]"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1"></iconify-icon><span>{{ $benefit }}</span></li>
                    @endforeach
                </ul>

                <div class="flex flex-wrap gap-3">
                    <x-ui.button href="#" variant="dark">View Open Jobs</x-ui.button>
                    <x-ui.button :href="route('companies.index')" variant="outline">Back to Companies</x-ui.button>
                </div>
            </div>

            <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-6 h-fit">
                <h3 class="text-[#073057] text-lg font-bold mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <x-ui.button :href="route('job.index')" variant="primary" class="w-full">Browse Jobs</x-ui.button>
                    <x-ui.button :href="route('contact.index')" variant="outline" class="w-full">Contact Company</x-ui.button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
