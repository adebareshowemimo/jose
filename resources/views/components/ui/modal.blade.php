@props([
    'id' => 'ui-modal',
    'title' => 'Modal Title',
])

<div id="{{ $id }}" class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/40 p-6" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
    <div class="w-full max-w-lg bg-white rounded-[12px] border border-[#E0E0E0] p-6 shadow-[0_25px_50px_rgba(0,0,0,0.15)]">
        <div class="flex items-center justify-between mb-4">
            <h3 id="{{ $id }}-title" class="text-[#073057] font-bold text-xl">{{ $title }}</h3>
            <button type="button" class="text-[#6B7280] hover:text-[#073057]" data-close="{{ $id }}">✕</button>
        </div>
        <div class="text-[#2C2C2C]">
            {{ $slot }}
        </div>
    </div>
</div>
