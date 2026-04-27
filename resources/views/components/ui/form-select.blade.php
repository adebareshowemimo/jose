@props([
    'id' => null,
    'name' => null,
    'label' => null,
    'options' => [],
    'selected' => null,
])

<div>
    @if($label)
        <label @if($id) for="{{ $id }}" @endif class="form-label">{{ $label }}</label>
    @endif

    <select
        @if($id) id="{{ $id }}" @endif
        @if($name) name="{{ $name }}" @endif
        {{ $attributes->merge(['class' => 'form-input']) }}
    >
        @foreach($options as $value => $text)
            <option value="{{ $value }}" @selected((string) old($name, $selected) === (string) $value)>{{ $text }}</option>
        @endforeach
    </select>
</div>
