@extends('admin.layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900">Needs attention</h2>
        <p class="text-sm text-gray-500">
            @if($total > 0)
                You have <strong>{{ $total }}</strong> {{ \Illuminate\Support\Str::plural('item', $total) }} that need action. Items clear automatically once handled.
            @else
                You're all caught up — nothing needs action right now.
            @endif
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($feed as $category)
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-semibold text-gray-900">{{ $category['label'] }}</h3>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $category['count'] > 0 ? 'bg-[#1AAD94]/10 text-[#1AAD94]' : 'bg-gray-100 text-gray-500' }}">{{ $category['count'] }}</span>
                    </div>
                    @if($category['count'] > 0)
                        <a href="{{ $category['url'] }}" class="text-xs font-medium text-[#1AAD94] hover:underline">View all →</a>
                    @endif
                </div>

                @forelse($category['items'] as $item)
                    <a href="{{ $item['url'] }}" class="flex items-start justify-between gap-3 px-5 py-3 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $item['title'] }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $item['meta'] }}</p>
                        </div>
                        <span class="text-xs text-gray-400 shrink-0">{{ $item['time']?->diffForHumans() ?? '' }}</span>
                    </a>
                @empty
                    <p class="px-5 py-6 text-center text-sm text-gray-400">Nothing pending.</p>
                @endforelse
            </div>
        @endforeach
    </div>
@endsection
