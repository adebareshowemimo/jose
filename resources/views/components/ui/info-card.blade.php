@props([
    'title' => 'Card Title',
    'value' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white border border-[#E0E0E0] rounded-[12px] p-6 hover:shadow-xl transition-all']) }}>
    <p class="text-[#6B7280] text-sm mb-2">{{ $title }}</p>
    @if($value)
        <p class="text-[#073057] text-2xl font-extrabold">{{ $value }}</p>
    @endif
    <div class="text-[#2C2C2C] text-sm mt-3">{{ $slot }}</div>
</div>
