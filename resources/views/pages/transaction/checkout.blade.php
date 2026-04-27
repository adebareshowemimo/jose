@extends('layouts.app')

@section('title', ($pageTitle ?? 'Checkout').' — JOSEOCEANJOBS')

@section('content')
<section class="py-14 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />

        <x-ui.page-header label="Transactional Pages" :title="$pageTitle ?? 'Checkout'" :description="$pageDescription ?? null" />

        <div class="grid lg:grid-cols-[1fr_380px] gap-6">
            <div>
                <x-ui.table :headers="$headers ?? []">
                    @foreach(($rows ?? []) as $row)
                        <tr>
                            @foreach($row as $value)
                                <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </x-ui.table>
            </div>

            <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-6 h-fit">
                <h3 class="text-[#073057] font-extrabold text-lg mb-4">Order Summary</h3>
                <div class="space-y-3 text-sm">
                    @foreach(($summary ?? []) as $label => $value)
                        <div class="flex justify-between"><span class="text-[#6B7280]">{{ $label }}</span><span class="font-semibold text-[#2C2C2C]">{{ $value }}</span></div>
                    @endforeach
                </div>
                <x-ui.button href="#" variant="dark" class="w-full mt-6">Proceed to Payment</x-ui.button>
            </div>
        </div>
    </div>
</section>
@endsection
