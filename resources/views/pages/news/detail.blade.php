@extends('layouts.app')

@section('title', ($article['title'] ?? 'News Detail').' — JOSEOCEANJOBS')

@section('content')
<section class="py-16 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />

        <article class="bg-white border border-[#E0E0E0] rounded-[12px] p-8 max-w-4xl">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-3 py-1 rounded-full bg-[#1AAD94]/10 text-[#1AAD94] text-xs font-bold uppercase">{{ $article['category'] ?? 'News' }}</span>
                <span class="text-sm text-[#6B7280]">{{ $article['date'] ?? '' }}</span>
                <span class="text-sm text-[#6B7280]">By {{ $article['author'] ?? 'Editorial' }}</span>
            </div>

            <h1 class="text-[38px] font-extrabold text-[#073057] leading-tight mb-6">{{ $article['title'] ?? 'News Detail' }}</h1>

            <div class="space-y-5 text-[#2C2C2C] leading-relaxed">
                @foreach(($article['content'] ?? []) as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </div>

            <div class="mt-8">
                <x-ui.button :href="route('news.index')" variant="outline">Back to News</x-ui.button>
            </div>
        </article>
    </div>
</section>
@endsection
