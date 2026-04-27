@extends('layouts.dashboard')

@section('title', 'Messages')
@section('page-title', 'Messages')

@section('sidebar-nav')
    @include('pages.dashboard.candidate.partials.sidebar')
@endsection

@section('content')
@php $selectedId = $selectedConversation?->id; @endphp

@if($selectedConversation)
    <div data-chat-conversation-id="{{ $selectedConversation->id }}"></div>
@endif

<div class="bg-white rounded-xl border border-[#E5E7EB] overflow-hidden">
    <div class="flex h-[calc(100vh-200px)] min-h-[560px]">
        <div class="w-full md:w-80 lg:w-96 border-r border-[#E5E7EB] flex flex-col">
            <div class="p-4 border-b border-[#E5E7EB]">
                <h2 class="font-bold text-[#073057]">Messages</h2>
                <p class="text-xs text-[#6B7280] mt-1">Employer and admin conversations assigned to you.</p>
            </div>
            <div class="flex-1 overflow-y-auto">
                @forelse($conversations as $conversation)
                    @php
                        $name = $conversation->type === \App\Models\ChatConversation::TYPE_ADMIN_CANDIDATE ? 'JCL Admin' : ($conversation->company?->name ?? 'Employer');
                        $initial = strtoupper(mb_substr($name, 0, 1));
                        $unread = $conversation->messages()->where('sender_user_id', '!=', auth()->id())->whereNull('read_at')->count();
                    @endphp
                    <a href="{{ route('user.chat', ['conversation' => $conversation->id]) }}"
                       class="w-full p-4 flex items-start gap-3 text-left transition border-b border-[#E5E7EB]/60 {{ $selectedId === $conversation->id ? 'bg-[#1AAD94]/5 border-l-2 border-l-[#1AAD94]' : 'hover:bg-[#F9FAFB]' }}">
                        <div class="w-12 h-12 bg-[#073057]/10 rounded-full flex items-center justify-center text-[#073057] font-semibold shrink-0">{{ $initial }}</div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="font-semibold text-[#073057] truncate">{{ $name }}</h4>
                                <span class="text-xs text-[#9CA3AF] shrink-0">{{ $conversation->last_message_at?->diffForHumans() ?? $conversation->created_at?->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-[#1AAD94] mb-1 truncate">{{ $conversation->contextLabel() }}</p>
                            <p class="text-sm text-[#6B7280] truncate">{{ $conversation->latestMessage?->body ?? 'No messages yet.' }}</p>
                        </div>
                        @if($unread > 0)
                            <span class="w-5 h-5 bg-[#1AAD94] text-white text-xs font-medium rounded-full flex items-center justify-center shrink-0">{{ $unread }}</span>
                        @endif
                    </a>
                @empty
                    <div class="p-8 text-center">
                        <h3 class="font-bold text-[#073057]">No conversations yet</h3>
                        <p class="text-sm text-[#6B7280] mt-2">Messages will appear after an employer or admin starts a conversation with you.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="hidden md:flex flex-1 flex-col">
            @if($selectedConversation)
                <div class="p-4 border-b border-[#E5E7EB]">
                    <h4 class="font-semibold text-[#073057]">{{ $selectedConversation->type === \App\Models\ChatConversation::TYPE_ADMIN_CANDIDATE ? 'JCL Admin' : ($selectedConversation->company?->name ?? 'Employer') }}</h4>
                    <p class="text-xs text-[#1AAD94]">{{ $selectedConversation->contextLabel() }}</p>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-4">
                    @foreach($messages as $message)
                        @php $mine = $message->sender_user_id === auth()->id(); @endphp
                        <div class="flex items-end gap-2 max-w-[78%] {{ $mine ? 'ml-auto flex-row-reverse' : '' }}">
                            <div class="w-8 h-8 {{ $mine ? 'bg-[#073057] text-white' : 'bg-[#073057]/10 text-[#073057]' }} rounded-full flex items-center justify-center text-xs font-semibold shrink-0">{{ strtoupper(mb_substr($message->sender?->name ?? $message->sender_role, 0, 1)) }}</div>
                            <div>
                                <div class="{{ $mine ? 'bg-[#073057] text-white rounded-br-none' : 'bg-[#F3F4F6] text-[#4B5563] rounded-bl-none' }} rounded-2xl px-4 py-3">
                                    @if($message->action_type)
                                        <p class="text-xs font-bold uppercase tracking-wider mb-2 {{ $mine ? 'text-white/70' : 'text-[#1AAD94]' }}">{{ str_replace('_', ' ', $message->action_type) }}</p>
                                    @endif
                                    <p class="text-sm whitespace-pre-line">{{ $message->body }}</p>
                                </div>
                                <span class="text-xs text-[#9CA3AF] {{ $mine ? 'block text-right' : 'ml-2' }}">{{ $message->created_at?->format('g:i A') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <form method="POST" action="{{ route('user.chat.messages.store', $selectedConversation) }}" class="p-4 border-t border-[#E5E7EB]">
                    @csrf
                    <div class="flex items-end gap-3">
                        <textarea name="body" rows="1" required placeholder="Type a reply..." class="flex-1 px-4 py-3 bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none resize-none"></textarea>
                        <button class="p-3 bg-[#1AAD94] hover:bg-[#158f7a] text-white rounded-xl transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                    </div>
                </form>
            @else
                <div class="flex-1 flex items-center justify-center p-8 text-center">
                    <div>
                        <h3 class="text-xl font-bold text-[#073057] mb-2">Select a conversation</h3>
                        <p class="text-[#6B7280]">Your conversations will appear on the left.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/chat-realtime.js')
@endpush
