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
$isFeatured = ! empty($data['is_featured']);
$link = $slug ? route('candidate.detail', ['slug' => $slug]) : '#';
@endphp

<div {{ $attributes->merge(['class' => 'relative bg-white border ' . ($isFeatured ? 'border-[#1AAD94]/40 ring-2 ring-[#1AAD94]/20' : 'border-[#E0E0E0]') . ' rounded-[12px] p-6 hover:shadow-xl transition-all']) }}>
    @if ($isFeatured)
        <span class="absolute -top-2.5 left-4 inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-400 text-amber-900 text-[10px] font-bold uppercase tracking-wider shadow">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.539 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.462a1 1 0 00.95-.69l1.07-3.292z"/></svg>
            Featured
        </span>
    @endif

    <h3 class="text-[#073057] text-xl font-extrabold mb-1">{{ $name }}</h3>
    <p class="text-[#6B7280] text-sm mb-4">{{ $role }}</p>

    <div class="space-y-2 mb-5 text-sm text-[#2C2C2C]">
        <p><span class="font-semibold">Location:</span> {{ $location }}</p>
        <p><span class="font-semibold">Experience:</span> {{ $experience }}</p>
        <p><span class="font-semibold">Availability:</span> <span class="text-[#16A34A] font-semibold">{{ $availability }}</span></p>
    </div>

    <x-ui.button :href="$link" variant="outline" size="sm" class="w-full">View Profile</x-ui.button>
</div>
