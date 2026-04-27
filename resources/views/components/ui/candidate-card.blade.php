@props([
    'candidate' => [],
])

@php
$data = is_array($candidate) ? $candidate : [];
$name = $data['name'] ?? 'Unnamed Candidate';
$role = $data['role'] ?? 'Marine Professional';
$location = $data['location'] ?? 'N/A';
$experience = $data['experience'] ?? 'N/A';
$availability = $data['availability'] ?? 'N/A';
$slug = $data['slug'] ?? null;
$link = $slug ? route('candidate.detail', ['slug' => $slug]) : '#';
@endphp

<div {{ $attributes->merge(['class' => 'bg-white border border-[#E0E0E0] rounded-[12px] p-6 hover:shadow-xl transition-all']) }}>
    <h3 class="text-[#073057] text-xl font-extrabold mb-1">{{ $name }}</h3>
    <p class="text-[#6B7280] text-sm mb-4">{{ $role }}</p>

    <div class="space-y-2 mb-5 text-sm text-[#2C2C2C]">
        <p><span class="font-semibold">Location:</span> {{ $location }}</p>
        <p><span class="font-semibold">Experience:</span> {{ $experience }}</p>
        <p><span class="font-semibold">Availability:</span> <span class="text-[#16A34A] font-semibold">{{ $availability }}</span></p>
    </div>

    <x-ui.button :href="$link" variant="outline" size="sm" class="w-full">View Profile</x-ui.button>
</div>
