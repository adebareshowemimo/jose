@extends('layouts.dashboard')

@section('title', 'Messages')
@section('page-title', 'Messages')

@section('sidebar-nav')
    @include('pages.dashboard.employer.partials.sidebar')
@endsection

@section('content')
@php
    $selectedId = $selectedConversation?->id;
@endphp

@if($selectedConversation)
    <div data-chat-conversation-id="{{ $selectedConversation->id }}"></div>
@endif

<div x-data="{ actionModal: null }" class="bg-white rounded-xl border border-[#E5E7EB] overflow-hidden">
    <div class="flex h-[calc(100vh-200px)] min-h-[560px]">
        <div class="w-full md:w-80 lg:w-96 border-r border-[#E5E7EB] flex flex-col">
            <div class="p-4 border-b border-[#E5E7EB]">
                <h2 class="font-bold text-[#073057]">Assigned Candidates</h2>
                <p class="text-xs text-[#6B7280] mt-1">Only candidates delivered by admin are available here.</p>
            </div>

            <div class="flex-1 overflow-y-auto">
                @forelse($conversations as $conversation)
                    @php
                        $candidateName = $conversation->candidate?->user?->name ?? 'Candidate';
                        $initials = collect(explode(' ', $candidateName))->map(fn($w) => strtoupper(mb_substr($w, 0, 1)))->take(2)->join('');
                        $unread = $conversation->messages()->where('sender_user_id', '!=', auth()->id())->whereNull('read_at')->count();
                    @endphp
                    <a href="{{ route('employer.chat', ['conversation' => $conversation->id]) }}"
                       class="w-full p-4 flex items-start gap-3 text-left transition border-b border-[#E5E7EB]/60 {{ $selectedId === $conversation->id ? 'bg-[#1AAD94]/5 border-l-2 border-l-[#1AAD94]' : 'hover:bg-[#F9FAFB]' }}">
                        <div class="w-12 h-12 bg-[#073057]/10 rounded-full flex items-center justify-center text-[#073057] font-semibold shrink-0">{{ $initials ?: 'C' }}</div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="font-semibold text-[#073057] truncate">{{ $candidateName }}</h4>
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
                        <div class="w-14 h-14 mx-auto rounded-full bg-[#1AAD94]/10 flex items-center justify-center text-[#1AAD94] mb-4">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.82L3 20l1.3-3.25A7.33 7.33 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <h3 class="font-bold text-[#073057]">No assigned candidates yet</h3>
                        <p class="text-sm text-[#6B7280] mt-2">Candidates will appear after admin attaches platform candidates to your recruitment requests.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="hidden md:flex flex-1 flex-col">
            @if($selectedConversation)
                @php
                    $candidateName = $selectedConversation->candidate?->user?->name ?? 'Candidate';
                    $initials = collect(explode(' ', $candidateName))->map(fn($w) => strtoupper(mb_substr($w, 0, 1)))->take(2)->join('');
                @endphp
                <div class="p-4 border-b border-[#E5E7EB] flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#073057]/10 rounded-full flex items-center justify-center text-[#073057] font-semibold text-sm">{{ $initials ?: 'C' }}</div>
                        <div>
                            <h4 class="font-semibold text-[#073057]">{{ $candidateName }}</h4>
                            <p class="text-xs text-[#1AAD94]">{{ $selectedConversation->contextLabel() }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-4 space-y-4">
                    @forelse($messages as $message)
                        @php $mine = $message->sender_user_id === auth()->id(); @endphp
                        <div class="flex items-end gap-2 max-w-[78%] {{ $mine ? 'ml-auto flex-row-reverse' : '' }}">
                            <div class="w-8 h-8 {{ $mine ? 'bg-[#073057] text-white' : 'bg-[#073057]/10 text-[#073057]' }} rounded-full flex items-center justify-center text-xs font-semibold shrink-0">
                                {{ strtoupper(mb_substr($message->sender?->name ?? $message->sender_role, 0, 1)) }}
                            </div>
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
                    @empty
                        <p class="text-center text-sm text-[#6B7280] mt-12">No messages yet. Start the conversation below.</p>
                    @endforelse
                </div>

                <div class="px-4 py-2 border-t border-[#E5E7EB] flex gap-2 overflow-x-auto">
                    <button type="button" @click="actionModal = 'interview'" class="px-3 py-1.5 bg-[#F3F4F6] hover:bg-[#E5E7EB] text-sm text-[#4B5563] rounded-lg whitespace-nowrap transition">Schedule Interview</button>
                    <button type="button" @click="actionModal = 'documents'" class="px-3 py-1.5 bg-[#F3F4F6] hover:bg-[#E5E7EB] text-sm text-[#4B5563] rounded-lg whitespace-nowrap transition">Request Documents</button>
                    <button type="button" @click="actionModal = 'offer'" class="px-3 py-1.5 bg-[#F3F4F6] hover:bg-[#E5E7EB] text-sm text-[#4B5563] rounded-lg whitespace-nowrap transition">Send Offer</button>
                </div>

                <form method="POST" action="{{ route('employer.chat.messages.store', $selectedConversation) }}" class="p-4 border-t border-[#E5E7EB]">
                    @csrf
                    <div class="flex items-end gap-3">
                        <textarea name="body" rows="1" required placeholder="Type a message..." class="flex-1 px-4 py-3 bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl text-sm focus:ring-2 focus:ring-[#1AAD94] outline-none resize-none"></textarea>
                        <button class="p-3 bg-[#1AAD94] hover:bg-[#158f7a] text-white rounded-xl transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                    </div>
                </form>

                <div x-show="actionModal" x-cloak class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" @click.self="actionModal = null">
                    <div class="bg-white rounded-xl shadow-xl w-full max-w-xl p-6">
                        <form x-show="actionModal === 'interview'" method="POST" action="{{ route('employer.chat.schedule-interview', $selectedConversation) }}" class="space-y-4">
                            @csrf
                            <h3 class="text-lg font-bold text-[#073057]">Schedule Interview</h3>
                            <input name="interview_date" required placeholder="Date / time" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl">
                            <input name="interview_location" required placeholder="Location or meeting link" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl">
                            <textarea name="note" rows="4" placeholder="Optional note" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl"></textarea>
                            <div class="flex justify-end gap-2"><button type="button" @click="actionModal = null" class="px-4 py-2 text-sm text-gray-600">Cancel</button><button class="px-4 py-2 bg-[#1AAD94] text-white rounded-lg font-semibold">Send</button></div>
                        </form>

                        <form x-show="actionModal === 'documents'" method="POST" action="{{ route('employer.chat.request-documents', $selectedConversation) }}" class="space-y-4">
                            @csrf
                            <h3 class="text-lg font-bold text-[#073057]">Request Documents</h3>
                            <textarea name="documents" rows="4" required placeholder="List requested documents..." class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl"></textarea>
                            <textarea name="note" rows="3" placeholder="Optional note" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl"></textarea>
                            <div class="flex justify-end gap-2"><button type="button" @click="actionModal = null" class="px-4 py-2 text-sm text-gray-600">Cancel</button><button class="px-4 py-2 bg-[#1AAD94] text-white rounded-lg font-semibold">Send</button></div>
                        </form>

                        <form x-show="actionModal === 'offer'" method="POST" action="{{ route('employer.chat.send-offer', $selectedConversation) }}" class="space-y-4">
                            @csrf
                            <h3 class="text-lg font-bold text-[#073057]">Send Offer</h3>
                            <input name="offer_title" required placeholder="Offer title" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl">
                            <textarea name="offer_details" rows="5" required placeholder="Offer details..." class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl"></textarea>
                            <label class="flex items-center gap-2 text-sm text-[#4B5563]"><input type="checkbox" name="final_offer" value="1" class="rounded border-gray-300 text-[#1AAD94]"> Mark as final hire decision</label>
                            <div class="flex justify-end gap-2"><button type="button" @click="actionModal = null" class="px-4 py-2 text-sm text-gray-600">Cancel</button><button class="px-4 py-2 bg-[#1AAD94] text-white rounded-lg font-semibold">Send</button></div>
                        </form>
                    </div>
                </div>
            @else
                <div class="flex-1 flex items-center justify-center p-8 text-center">
                    <div>
                        <h3 class="text-xl font-bold text-[#073057] mb-2">Select a conversation</h3>
                        <p class="text-[#6B7280]">Assigned candidates will appear on the left.</p>
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
