@extends('admin.layouts.app')

@section('title', 'Chat')
@section('page-title', 'Candidate Chat')

@section('content')
@if($selectedConversation)
    <div data-chat-conversation-id="{{ $selectedConversation->id }}"></div>
@endif

<div class="grid gap-6 xl:grid-cols-[360px_1fr]">
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-4"
             x-data="{
                 q: '',
                 results: [],
                 loading: false,
                 hasSearched: false,
                 timer: null,
                 init() { this.fetch(); },
                 onInput() {
                     clearTimeout(this.timer);
                     this.timer = setTimeout(() => this.fetch(), 250);
                 },
                 async fetch() {
                     this.loading = true;
                     try {
                         const url = new URL('{{ route('admin.chat.candidates.search') }}', window.location.origin);
                         if (this.q) url.searchParams.set('q', this.q);
                         const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                         const data = await res.json();
                         this.results = data.results || [];
                     } catch (e) {
                         this.results = [];
                     } finally {
                         this.loading = false;
                         this.hasSearched = true;
                     }
                 },
                 gotoCandidate(id) {
                     window.location.href = '{{ route('admin.chat.index') }}?candidate_id=' + id;
                 }
             }">
            <h2 class="font-semibold text-gray-900 mb-3">Start Candidate Chat</h2>
            <div class="relative mb-3">
                <input type="text" x-model="q" @input="onInput()" placeholder="Type a name or email..."
                    class="w-full px-3 py-2 pr-9 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                <span x-show="loading" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">…</span>
            </div>
            <div class="space-y-2 max-h-72 overflow-y-auto">
                <template x-for="c in results" :key="c.id">
                    <button type="button" @click="gotoCandidate(c.id)"
                            class="w-full text-left rounded-lg border border-gray-100 p-3 hover:bg-gray-50 transition">
                        <p class="text-sm font-semibold text-gray-900" x-text="c.name"></p>
                        <p class="text-xs text-gray-500" x-text="c.email"></p>
                    </button>
                </template>
                <p x-show="hasSearched && !loading && results.length === 0"
                   class="text-sm text-gray-400 text-center py-6"
                   x-text="q ? 'No matches for &quot;' + q + '&quot;.' : 'No candidates found.'"></p>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <h2 class="font-semibold text-gray-900">Admin Conversations</h2>
            </div>
            <div class="max-h-[460px] overflow-y-auto">
                @forelse($conversations as $conversation)
                    @php
                        $candidateName = $conversation->candidate?->user?->name ?? 'Candidate';
                        $unread = $conversation->messages()->where('sender_user_id', '!=', auth()->id())->whereNull('read_at')->count();
                    @endphp
                    <a href="{{ route('admin.chat.index', ['conversation' => $conversation->id]) }}" class="flex gap-3 p-4 border-b border-gray-100 hover:bg-gray-50 {{ $selectedConversation?->id === $conversation->id ? 'bg-[#1AAD94]/5' : '' }}">
                        <div class="w-10 h-10 rounded-full bg-[#073057]/10 text-[#073057] flex items-center justify-center font-semibold">{{ strtoupper(mb_substr($candidateName, 0, 1)) }}</div>
                        <div class="min-w-0 flex-1">
                            <div class="flex justify-between gap-2">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $candidateName }}</p>
                                @if($unread > 0)<span class="text-xs bg-[#1AAD94] text-white rounded-full px-2 py-0.5">{{ $unread }}</span>@endif
                            </div>
                            <p class="text-xs text-gray-500 truncate">{{ $conversation->latestMessage?->body ?? 'No messages yet.' }}</p>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-gray-400 text-center py-8">No admin chats yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white overflow-hidden min-h-[620px] flex flex-col">
        @if($selectedConversation)
            <div class="p-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">{{ $selectedConversation->candidate?->user?->name ?? 'Candidate' }}</h3>
                <p class="text-xs text-gray-500">{{ $selectedConversation->candidate?->user?->email }}</p>
            </div>
            <div class="flex-1 overflow-y-auto p-5 space-y-4">
                @foreach($messages as $message)
                    @php $mine = $message->sender_user_id === auth()->id(); @endphp
                    <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[75%] rounded-2xl px-4 py-3 {{ $mine ? 'bg-[#073057] text-white rounded-br-none' : 'bg-gray-100 text-gray-700 rounded-bl-none' }}">
                            <p class="text-sm whitespace-pre-line">{{ $message->body }}</p>
                            <p class="text-[11px] mt-1 {{ $mine ? 'text-white/60' : 'text-gray-400' }}">{{ $message->created_at?->format('M d, g:i A') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <form method="POST" action="{{ route('admin.chat.messages.store', $selectedConversation) }}" class="p-4 border-t border-gray-200">
                @csrf
                <div class="flex gap-3">
                    <textarea name="body" rows="2" required placeholder="Type a message..." class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent"></textarea>
                    <button class="self-end px-4 py-2 bg-[#1AAD94] text-white text-sm font-semibold rounded-lg hover:bg-[#158f7a]">Send</button>
                </div>
            </form>
        @else
            <div class="flex-1 flex items-center justify-center text-center p-8">
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">Select a candidate or conversation</h3>
                    <p class="text-sm text-gray-500">Admin chats are private and not visible to employers.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/chat-realtime.js')
@endpush
