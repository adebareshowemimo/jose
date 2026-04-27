@props([
    'id' => null,
    'name' => null,
    'label' => null,
    'type' => 'text',
    'placeholder' => '',
    'value' => null,
])

<div>
    @if($label)
        <label @if($id) for="{{ $id }}" @endif class="form-label">{{ $label }}</label>
    @endif

    <input
        @if($id) id="{{ $id }}" @endif
        @if($name) name="{{ $name }}" @endif
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'form-input']) }}
    />
</div>
