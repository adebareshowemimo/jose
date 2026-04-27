@extends('layouts.app')

@section('title', $pageTitle ?? 'Plans & Pricing')

@section('content')
<section class="py-16 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />

        <x-ui.page-header
            label="Public Pages"
            :title="$pageTitle ?? 'Plans & Pricing'"
            :description="$pageDescription ?? null"
        />

        <div class="grid md:grid-cols-3 gap-6">
            @foreach(($plans ?? []) as $plan)
                <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-6 hover:shadow-xl transition-all">
                    <h3 class="text-[#073057] text-2xl font-extrabold mb-2">{{ $plan['name'] }}</h3>
                    <p class="text-[#1AAD94] text-2xl font-bold mb-5">{{ $plan['price'] }}</p>

                    <ul class="space-y-2 mb-6 text-sm text-[#2C2C2C]">
                        @foreach(($plan['features'] ?? []) as $feature)
                            <li class="flex items-start gap-2"><iconify-icon icon="lucide:check-circle-2" class="text-[#1AAD94] mt-1"></iconify-icon><span>{{ $feature }}</span></li>
                        @endforeach
                    </ul>

                    <x-ui.button href="#" variant="dark" class="w-full">Choose Plan</x-ui.button>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
