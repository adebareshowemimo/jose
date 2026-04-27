@props([
    'company' => [],
])

@php
$data = is_array($company) ? $company : [];
$name = $data['name'] ?? 'Unknown Company';
$location = $data['location'] ?? 'N/A';
$sector = $data['sector'] ?? 'Maritime';
$openRoles = $data['open_roles'] ?? 0;
$slug = $data['slug'] ?? null;
$link = $slug ? route('companies.detail', ['slug' => $slug]) : '#';
@endphp

<div {{ $attributes->merge(['class' => 'bg-white border border-[#E0E0E0] rounded-[12px] p-6 hover:shadow-xl transition-all']) }}>
    <h3 class="text-[#073057] text-xl font-extrabold mb-1">{{ $name }}</h3>
    <p class="text-[#6B7280] text-sm mb-4">{{ $sector }}</p>

    <div class="space-y-2 mb-5 text-sm text-[#2C2C2C]">
        <p><span class="font-semibold">Location:</span> {{ $location }}</p>
        <p><span class="font-semibold">Open Roles:</span> <span class="text-[#1AAD94] font-semibold">{{ $openRoles }}</span></p>
    </div>

    <x-ui.button :href="$link" variant="outline" size="sm" class="w-full">View Company</x-ui.button>
</div>
