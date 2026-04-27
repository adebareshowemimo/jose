@extends('layouts.app')

@section('title', ($pageTitle ?? 'Dashboard').' — JOSEOCEANJOBS')

@section('content')
<section class="py-14 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />

        <x-ui.page-header
            :label="$section ?? 'Dashboard'"
            :title="$pageTitle ?? 'Dashboard Section'"
            :description="$pageDescription ?? null"
        />

        <div class="grid md:grid-cols-3 gap-6 mb-8">
            @foreach(($stats ?? []) as $item)
                <x-ui.info-card :title="$item['title'] ?? 'Metric'" :value="$item['value'] ?? null">
                    {{ $item['note'] ?? '' }}
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
            @if(!empty($primaryAction['url'] ?? null))
                <x-ui.button :href="$primaryAction['url']" variant="dark">{{ $primaryAction['label'] ?? 'Primary Action' }}</x-ui.button>
            @endif
            @if(!empty($secondaryAction['url'] ?? null))
                <x-ui.button :href="$secondaryAction['url']" variant="outline">{{ $secondaryAction['label'] ?? 'Secondary Action' }}</x-ui.button>
            @endif
        </div>
    </div>
</section>
@endsection
