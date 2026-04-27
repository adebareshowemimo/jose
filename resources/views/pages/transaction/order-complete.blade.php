@extends('layouts.app')

@section('title', ($pageTitle ?? 'Order Complete').' — JOSEOCEANJOBS')

@section('content')
<section class="py-20 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6 max-w-4xl">
        <div class="bg-white border border-[#E0E0E0] rounded-[16px] p-10 text-center">
            <div class="w-20 h-20 rounded-full bg-[#1AAD94]/10 text-[#1AAD94] inline-flex items-center justify-center mb-5">
                <iconify-icon icon="lucide:check-circle-2" width="36"></iconify-icon>
            </div>
            <h1 class="text-[#073057] text-[36px] font-extrabold mb-3">{{ $pageTitle ?? 'Order Complete' }}</h1>
            <p class="text-[#6B7280] mb-8">{{ $pageDescription ?? '' }}</p>

            <div class="grid md:grid-cols-3 gap-4 text-left mb-8">
                @foreach(($summary ?? []) as $label => $value)
                    <x-ui.info-card :title="$label" :value="$value">Completion detail.</x-ui.info-card>
                @endforeach
            </div>

            <div class="flex justify-center gap-3">
                <x-ui.button :href="route('order.history')" variant="dark">View Orders</x-ui.button>
                <x-ui.button :href="route('home')" variant="outline">Back Home</x-ui.button>
            </div>
        </div>
    </div>
</section>
@endsection
