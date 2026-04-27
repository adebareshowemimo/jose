@extends('layouts.app')

@section('title', ($pageTitle ?? 'Order History').' — JOSEOCEANJOBS')

@section('content')
<section class="py-14 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />
        <x-ui.page-header label="Transactional Pages" :title="$pageTitle ?? 'Order History'" :description="$pageDescription ?? null" />

        <x-ui.table :headers="$headers ?? []">
            @foreach(($rows ?? []) as $row)
                <tr>
                    @foreach($row as $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            @endforeach
        </x-ui.table>

        <x-ui.pagination :page="1" :prevUrl="'#'" :nextUrl="'#'" />
    </div>
</section>
@endsection
