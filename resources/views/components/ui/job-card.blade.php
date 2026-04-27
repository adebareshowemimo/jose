@props([
    'job' => [],
])

@php
$data = is_array($job) ? $job : [];
$title = $data['title'] ?? 'Untitled Role';
$company = $data['company'] ?? 'Unknown Company';
$location = $data['location'] ?? 'N/A';
$type = $data['type'] ?? 'Open';
$salary = $data['salary'] ?? 'Negotiable';
$slug = $data['slug'] ?? null;
$link = $slug ? route('job.detail', ['slug' => $slug]) : '#';
@endphp

<div {{ $attributes->merge(['class' => 'bg-white border border-[#E0E0E0] rounded-[12px] p-6 hover:shadow-xl transition-all']) }}>
    <div class="flex items-center justify-between mb-4">
        <span class="inline-flex px-3 py-1 rounded-full bg-[#1AAD94]/10 text-[#1AAD94] text-[11px] font-bold uppercase tracking-wider">{{ $type }}</span>
        <span class="text-[#16A34A] font-mono text-sm font-bold">{{ $salary }}</span>
    </div>

    <h3 class="text-[#073057] text-xl font-extrabold mb-1">{{ $title }}</h3>
    <p class="text-[#6B7280] text-sm mb-4">{{ $company }}</p>

    <div class="flex items-center gap-2 text-sm text-[#2C2C2C] mb-5">
        <iconify-icon icon="lucide:map-pin" class="text-[#1AAD94]"></iconify-icon>
        <span>{{ $location }}</span>
    </div>

    <x-ui.button :href="$link" variant="outline" size="sm" class="w-full">View Details</x-ui.button>
</div>
