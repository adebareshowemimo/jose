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

        @if(empty($jobs))
            <div class="bg-white rounded-[12px] border border-dashed border-[#D1D5DB] p-16 text-center">
                <iconify-icon icon="lucide:briefcase" class="text-5xl text-[#D1D5DB] mb-3"></iconify-icon>
                <p class="text-[#6B7280] mb-4">No open roles in this category right now.</p>
                <x-ui.button :href="route('job.index')" variant="outline" size="sm">Browse all jobs</x-ui.button>
            </div>
        @else
            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($jobs as $job)
                    <x-ui.job-card :job="$job" />
                @endforeach
            </div>

            @isset($paginator)
                <div class="mt-10">
                    {{ $paginator->links() }}
                </div>
            @endisset
        @endif
    </div>
</section>
@endsection
