@extends('layouts.app')

@section('title', $pageTitle ?? 'Candidate Directory')

@section('content')
<section class="py-16 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />

        <x-ui.page-header
            label="Public Pages"
            :title="$pageTitle ?? 'Candidate Directory'"
            :description="$pageDescription ?? null"
        />

        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach(($candidates ?? []) as $candidate)
                <x-ui.candidate-card :candidate="$candidate" />
            @endforeach
        </div>
    </div>
</section>
@endsection
