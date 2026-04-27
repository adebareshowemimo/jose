@extends('layouts.app')

@section('title', $pageTitle ?? 'Company Directory')

@section('content')
<section class="py-16 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />

        <x-ui.page-header
            label="Public Pages"
            :title="$pageTitle ?? 'Company Directory'"
            :description="$pageDescription ?? null"
        />

        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach(($companies ?? []) as $company)
                <x-ui.company-card :company="$company" />
            @endforeach
        </div>
    </div>
</section>
@endsection
