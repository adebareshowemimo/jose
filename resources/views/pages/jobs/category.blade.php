@extends('layouts.app')

@section('title', ($category ?? 'Category').' Jobs — JOSEOCEANJOBS')

@section('content')
<section class="py-16 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />

        <x-ui.page-header
            label="Category"
            :title="($category ?? 'Category').' Jobs'"
            :description="$pageDescription ?? null"
        />

        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach(($jobs ?? []) as $job)
                <x-ui.job-card :job="$job" />
            @endforeach
        </div>
    </div>
</section>
@endsection
