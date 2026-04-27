@extends('layouts.app')

@section('title', ($pageTitle ?? 'Booking Checkout').' — JOSEOCEANJOBS')

@section('content')
<section class="py-14 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />
        <x-ui.page-header label="Transactional Pages" :title="$pageTitle ?? 'Booking Checkout'" :description="$pageDescription ?? null" />

        <div class="grid lg:grid-cols-[1fr_340px] gap-6">
            <x-ui.table :headers="$headers ?? []">
                @foreach(($rows ?? []) as $row)
                    <tr>
                        @foreach($row as $value)
                            <td>{{ $value }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </x-ui.table>

            <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-6">
                <h3 class="text-[#073057] font-bold mb-4">Booking Snapshot</h3>
                <div class="space-y-3 text-sm">
                    @foreach(($summary ?? []) as $label => $value)
                        <div class="flex justify-between"><span class="text-[#6B7280]">{{ $label }}</span><span class="font-semibold">{{ $value }}</span></div>
                    @endforeach
                </div>
                <x-ui.button href="#" variant="dark" class="w-full mt-6">Confirm Booking</x-ui.button>
            </div>
        </div>
    </div>
</section>
@endsection
