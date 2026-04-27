@extends('layouts.app')

@section('title', $pageTitle ?? 'JOSEOCEANJOBS')

@section('content')
<section class="py-20 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />

        <x-ui.page-header
            :label="$section ?? 'Page'"
            :title="$pageTitle ?? 'Untitled Page'"
            :description="$pageDescription ?? null"
        />

        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <x-ui.info-card title="Status" value="Controller Driven">
                This page is rendered from a dedicated controller action.
            </x-ui.info-card>

            <x-ui.info-card title="Component Strategy" value="Reusable">
                Shared Blade components are used for repeated UI patterns.
            </x-ui.info-card>

            <x-ui.info-card title="Next Step" value="Page Implementation">
                Replace this scaffold with page-specific UI and data bindings.
            </x-ui.info-card>
        </div>

        <div class="flex flex-wrap gap-4">
            <x-ui.button :href="url('/')" variant="dark">Back Home</x-ui.button>
            <x-ui.button :href="url('/contact')" variant="outline">Contact Us</x-ui.button>
        </div>
    </div>
</section>
@endsection
