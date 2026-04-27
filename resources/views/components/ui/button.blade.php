@props([
    'href' => '#',
    'variant' => 'primary',
    'size' => 'md',
])

@php
$variantClasses = [
    'primary' => 'bg-[#1AAD94] text-white hover:brightness-110',
    'dark' => 'bg-[#073057] text-white hover:bg-slate-800',
    'outline' => 'border-2 border-[#073057] text-[#073057] hover:bg-[#073057] hover:text-white',
    'ghost' => 'border border-[#1AAD94] text-[#1AAD94] hover:bg-[#1AAD94] hover:text-white',
];

$sizeClasses = [
    'sm' => 'px-4 py-2 text-xs',
    'md' => 'px-6 py-3 text-sm',
    'lg' => 'px-8 py-4 text-sm',
];

$variantKey = is_string($variant) ? $variant : 'primary';
$sizeKey = is_string($size) ? $size : 'md';

$classes = ($variantClasses[$variantKey] ?? $variantClasses['primary']).' '.($sizeClasses[$sizeKey] ?? $sizeClasses['md']).' rounded-[8px] font-bold uppercase tracking-wider transition-all inline-flex items-center justify-center gap-2';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
