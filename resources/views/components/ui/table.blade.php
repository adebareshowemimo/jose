@props([
    'headers' => [],
])

@php
$headerItems = is_array($headers) ? $headers : [];
@endphp

<div class="overflow-x-auto border border-[#E0E0E0] rounded-[12px]">
    <table class="table">
        @if(!empty($headerItems))
            <thead>
                <tr>
                    @foreach($headerItems as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
