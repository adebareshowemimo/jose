@props([
    // Query-string keys this form owns itself (so we don't double-submit them).
    'except' => [],
])

@php
    // Re-emit the current query string as hidden inputs so each independent filter
    // form preserves the others' state. Arrays (e.g. type[]=, salary[]=) are flattened
    // into bracketed names. 'page' is always dropped so changing a filter resets paging.
    $flatten = function (array $data, ?string $prefix = null) use (&$flatten) {
        $out = [];
        foreach ($data as $key => $value) {
            $name = $prefix ? $prefix.'['.$key.']' : $key;
            if (is_array($value)) {
                $out += $flatten($value, $name);
            } else {
                $out[$name] = $value;
            }
        }
        return $out;
    };

    $skip = array_merge((array) $except, ['page']);
@endphp

@foreach ($flatten(request()->except($skip)) as $name => $value)
    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
@endforeach
