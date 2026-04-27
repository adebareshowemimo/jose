@props([
    'items' => [],
])

@if(!empty($items))
    <nav {{ $attributes->merge(['class' => 'mb-6']) }} aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-2 text-sm">
            @foreach($items as $item)
                <li class="flex items-center gap-2">
                    @if(!empty($item['url']))
                        <a href="{{ $item['url'] }}" class="hover:text-[#1AAD94] transition-colors">{{ $item['label'] }}</a>
                    @else
                        <span class="font-semibold opacity-80">{{ $item['label'] }}</span>
                    @endif
                    @if(!$loop->last)
                        <span>/</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
