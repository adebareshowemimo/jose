@extends('layouts.app')

@section('title', ($pageTitle ?? 'Page').' — JOSEOCEANJOBS')

@section('content')
<section class="py-16 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />

        <article class="bg-white border border-[#E0E0E0] rounded-[12px] p-8 max-w-4xl">
            <h1 class="text-[38px] font-extrabold text-[#073057] leading-tight mb-4">{{ $pageTitle ?? 'CMS Page' }}</h1>
            <p class="text-[#6B7280] mb-8">Slug: <span class="font-mono">{{ $slug ?? '' }}</span></p>

            <div class="space-y-5 text-[#2C2C2C] leading-relaxed">
                @foreach(($contentBlocks ?? []) as $block)
                    <p>{{ $block }}</p>
                @endforeach
            </div>
        </article>
    </div>
</section>
@endsection
