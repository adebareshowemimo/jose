@extends('layouts.app')

@section('title', ($pageTitle ?? 'Invoice').' — JOSEOCEANJOBS')

@section('content')
<section class="py-14 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />
        <x-ui.page-header label="Transactional Pages" :title="$pageTitle ?? 'Invoice'" :description="$pageDescription ?? null" />

        <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-6 mb-6">
            <div class="grid md:grid-cols-3 gap-4">
                @foreach(($summary ?? []) as $label => $value)
                    <div><p class="text-xs text-[#6B7280] uppercase tracking-wider">{{ $label }}</p><p class="text-[#073057] font-bold">{{ $value }}</p></div>
                @endforeach
            </div>
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
    </div>
</section>
@endsection
