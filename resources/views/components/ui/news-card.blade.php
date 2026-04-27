@props([
    'article' => [],
])

@php
$data = is_array($article) ? $article : [];
$title = $data['title'] ?? 'Untitled Article';
$excerpt = $data['excerpt'] ?? '';
$author = $data['author'] ?? 'Editorial';
$date = $data['date'] ?? 'N/A';
$category = $data['category'] ?? 'News';
$slug = $data['slug'] ?? null;
$link = $slug ? route('news.detail', ['slug' => $slug]) : '#';
@endphp

<article {{ $attributes->merge(['class' => 'bg-white border border-[#E0E0E0] rounded-[12px] p-6 hover:shadow-xl transition-all']) }}>
    <div class="flex items-center justify-between mb-3">
        <span class="inline-flex px-3 py-1 rounded-full bg-[#1AAD94]/10 text-[#1AAD94] text-[11px] font-bold uppercase tracking-wider">{{ $category }}</span>
        <span class="text-[#6B7280] text-xs">{{ $date }}</span>
    </div>

    <h3 class="text-[#073057] text-xl font-extrabold mb-3">{{ $title }}</h3>
    <p class="text-[#2C2C2C] text-sm leading-relaxed mb-4">{{ $excerpt }}</p>
    <p class="text-[#6B7280] text-xs mb-5">By {{ $author }}</p>

    <x-ui.button :href="$link" variant="outline" size="sm">Read Article</x-ui.button>
</article>
