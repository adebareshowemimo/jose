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
$imageUrl = $data['image_url'] ?? null;
$link = $slug ? route('news.detail', ['slug' => $slug]) : '#';
@endphp

<article {{ $attributes->merge(['class' => 'group bg-white border border-[#E0E0E0] rounded-[12px] overflow-hidden hover:shadow-xl hover:-translate-y-0.5 transition-all flex flex-col']) }}>
    <a href="{{ $link }}" class="block aspect-[16/9] overflow-hidden relative {{ $imageUrl ? 'bg-gray-100' : 'bg-gradient-to-br from-[#073057] via-[#0a4275] to-[#1AAD94]' }}">
        @if ($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ $title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
        @else
            {{-- Branded placeholder when no cover image is set --}}
            <div class="absolute inset-0 opacity-25 mix-blend-overlay" style="background-image: radial-gradient(circle at 25% 30%, rgba(255,255,255,0.4), transparent 40%), radial-gradient(circle at 80% 80%, rgba(26,173,148,0.6), transparent 50%);"></div>
            <div class="absolute inset-0 flex items-center justify-center px-6">
                <span class="text-center font-extrabold text-white/90 text-2xl md:text-3xl leading-tight tracking-tight line-clamp-3">{{ $title }}</span>
            </div>
            <svg class="absolute -bottom-6 -right-6 w-24 h-24 text-white/10" fill="currentColor" viewBox="0 0 24 24"><path d="M3 12c0 5 4 9 9 9s9-4 9-9c0-3.5-2-6.5-5-8 1 1 2 3 2 5 0 3-2 5-5 5s-5-2-5-5c0-2 1-4 2-5-3 1.5-5 4.5-5 8z"/></svg>
        @endif
        <span class="absolute top-3 left-3 inline-flex px-3 py-1 rounded-full {{ $imageUrl ? 'bg-white/95 text-[#1AAD94]' : 'bg-[#1AAD94] text-white' }} backdrop-blur text-[11px] font-bold uppercase tracking-wider shadow">{{ $category }}</span>
    </a>

    <div class="p-6 flex-1 flex flex-col">
        <span class="text-[#6B7280] text-xs mb-2">{{ $date }} · By {{ $author }}</span>
        <h3 class="text-[#073057] text-xl font-extrabold mb-3 group-hover:text-[#1AAD94] transition-colors">
            <a href="{{ $link }}">{{ $title }}</a>
        </h3>
        <p class="text-[#2C2C2C] text-sm leading-relaxed mb-5 line-clamp-3">{{ $excerpt }}</p>

        <div class="mt-auto">
            <x-ui.button :href="$link" variant="outline" size="sm">Read Article</x-ui.button>
        </div>
    </div>
</article>
