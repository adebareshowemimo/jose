@extends('layouts.app')

@section('title', ($code ?? 'Error').' — '.($pageTitle ?? 'Error'))

@section('content')
<section class="py-20 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6 max-w-3xl">
        <div class="bg-white border border-[#E0E0E0] rounded-[16px] p-10 text-center">
            <div class="inline-flex items-center justify-center text-[#1AAD94] bg-[#1AAD94]/10 w-24 h-24 rounded-full mb-5">
                <span class="text-3xl font-extrabold">{{ $code ?? '!' }}</span>
            </div>

            <h1 class="text-[#073057] text-[38px] font-extrabold mb-3">{{ $pageTitle ?? 'Something went wrong' }}</h1>
            <p class="text-[#6B7280] mb-8">{{ $pageDescription ?? 'An unexpected error occurred.' }}</p>

            <div class="flex flex-wrap justify-center gap-3">
                @if(!empty($primaryAction['url'] ?? null))
                    <x-ui.button :href="$primaryAction['url']" variant="dark">{{ $primaryAction['label'] ?? 'Go Back' }}</x-ui.button>
                @endif

                @if(!empty($secondaryAction['url'] ?? null))
                    <x-ui.button :href="$secondaryAction['url']" variant="outline">{{ $secondaryAction['label'] ?? 'Contact Support' }}</x-ui.button>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
