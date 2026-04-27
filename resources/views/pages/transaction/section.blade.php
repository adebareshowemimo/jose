@extends('layouts.app')

@section('title', ($pageTitle ?? 'Transaction').' — JOSEOCEANJOBS')

@section('content')
<section class="py-14 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />

        <x-ui.page-header
            label="Transactional Pages"
            :title="$pageTitle ?? 'Transaction'"
            :description="$pageDescription ?? null"
        />

        <div class="grid md:grid-cols-3 gap-6 mb-8">
            @foreach(($summary ?? []) as $label => $value)
                <x-ui.info-card :title="$label" :value="$value">
                    Transactional summary point.
                </x-ui.info-card>
            @endforeach
        </div>

        <x-ui.table :headers="$headers ?? []">
            @foreach(($rows ?? []) as $row)
                <tr>
                    @foreach($row as $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            @endforeach
        </x-ui.table>

        <div class="flex flex-wrap gap-3 mt-6">
            <x-ui.button :href="route('checkout')" variant="dark">Checkout</x-ui.button>
            <x-ui.button :href="route('cart')" variant="outline">Cart</x-ui.button>
            <x-ui.button :href="route('order.history')" variant="outline">Order History</x-ui.button>
        </div>
    </div>
</section>
@endsection
