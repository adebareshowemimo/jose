@extends('layouts.dashboard')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('sidebar-nav')
    @if($dashboardType === 'employer')
        @include('pages.dashboard.employer.partials.sidebar')
    @else
        @include('pages.dashboard.candidate.partials.sidebar')
    @endif
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#073057]">Notifications</h2>
            <p class="text-[#6B7280]">
                @if($unreadCount > 0)
                    You have <strong>{{ $unreadCount }}</strong> unread {{ \Illuminate\Support\Str::plural('notification', $unreadCount) }}.
                @else
                    You're all caught up.
                @endif
            </p>
        </div>
        @if($unreadCount > 0)
            <form method="POST" action="{{ route($routeNames['readAll']) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-[#1AAD94] hover:bg-[#158f7a] text-white text-sm font-semibold rounded-xl transition">
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl p-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-[#E5E7EB] overflow-hidden">
        @forelse($notifications as $notification)
            @php
                $data = $notification->data ?? [];
                $isUnread = is_null($notification->read_at);
                $title = $data['title'] ?? 'Notification';
                $message = $data['message'] ?? '';
                $url = $data['url'] ?? null;
            @endphp
            <a href="{{ $url ? route($routeNames['read'], $notification->id) : '#' }}"
               class="flex items-start gap-4 p-5 border-b border-[#E5E7EB] last:border-b-0 transition {{ $isUnread ? 'bg-[#F0FBF8]' : 'hover:bg-[#F9FAFB]' }}">
                <div class="shrink-0 w-10 h-10 rounded-full {{ $isUnread ? 'bg-[#1AAD94] text-white' : 'bg-[#E5E7EB] text-[#6B7280]' }} flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-semibold text-[#073057]">{{ $title }}</p>
                        <span class="text-xs text-[#6B7280] shrink-0">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    @if($message)
                        <p class="text-sm text-[#6B7280] mt-1">{{ $message }}</p>
                    @endif
                    @if($isUnread)
                        <span class="inline-block mt-2 px-2 py-0.5 rounded-full bg-[#1AAD94]/10 text-[#1AAD94] text-xs font-semibold">New</span>
                    @endif
                </div>
            </a>
        @empty
            <div class="p-12 text-center">
                <div class="mx-auto w-16 h-16 rounded-full bg-[#F3F4F6] flex items-center justify-center text-[#9CA3AF] mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <h3 class="text-lg font-bold text-[#073057]">No notifications yet</h3>
                <p class="mt-2 text-[#6B7280]">You'll see updates about your account here.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="px-2">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
