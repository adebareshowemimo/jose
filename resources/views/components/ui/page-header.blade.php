@props([
    'label' => null,
    'title' => 'Page Title',
    'description' => null,
])

<div class="mb-10">
    @if($label)
        <div class="inline-block px-4 py-1 bg-[#1AAD94]/10 text-[#1AAD94] rounded-full text-[12px] font-bold uppercase tracking-widest mb-4">
            {{ $label }}
        </div>
    @endif

    <h1 class="text-[40px] font-extrabold text-[#073057] leading-tight">{{ $title }}</h1>

    @if($description)
        <p class="text-[#6B7280] mt-3 max-w-3xl">{{ $description }}</p>
    @endif
</div>
