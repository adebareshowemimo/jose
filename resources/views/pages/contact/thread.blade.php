@extends('layouts.app')

@section('title', $pageTitle ?? 'Contact Conversation')

@section('content')
<section class="py-16 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />

        <div class="max-w-4xl rounded-[20px] border border-[#E0E0E0] bg-white p-8 shadow-sm">
            <div class="mb-8">
                <span class="inline-flex rounded-full bg-[#1AAD94]/10 px-3 py-1 text-xs font-bold uppercase text-[#1AAD94]">{{ ucfirst(str_replace('_', ' ', $submission->status)) }}</span>
                <h1 class="mt-4 text-3xl font-extrabold text-[#073057]">{{ $submission->subject }}</h1>
                <p class="mt-2 text-sm text-[#6B7280]">Conversation with JCL support for {{ $submission->email }}</p>
            </div>

            @if(session('success'))
                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-800">{{ session('success') }}</div>
            @endif

            <div class="space-y-4">
                @foreach($submission->chronologicalMessages as $message)
                    <div class="rounded-xl border {{ $message->sender_type === 'admin' ? 'border-[#1AAD94]/20 bg-[#1AAD94]/5' : 'border-gray-200 bg-gray-50' }} p-5">
                        <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                            <p class="text-sm font-bold text-[#073057]">{{ $message->sender_name }}</p>
                            <p class="text-xs text-[#6B7280]">{{ $message->created_at?->format('M d, Y g:i A') }}</p>
                        </div>
                        <p class="whitespace-pre-line text-sm leading-relaxed text-[#2C2C2C]">{{ $message->body }}</p>
                    </div>
                @endforeach
            </div>

            @if($submission->status !== 'closed')
                <form method="POST" action="{{ route('contact.thread.reply', $submission->reply_token) }}" class="mt-8 space-y-4">
                    @csrf
                    <label class="text-[11px] font-bold uppercase tracking-widest text-[#073057]/60">Reply</label>
                    <textarea name="message" rows="5" required class="w-full rounded-xl border border-[#E0E0E0] bg-[#F9FAFB] px-5 py-4 focus:border-[#1AAD94] focus:outline-none focus:ring-1 focus:ring-[#1AAD94]">{{ old('message') }}</textarea>
                    <button type="submit" class="rounded-xl bg-[#073057] px-6 py-3 text-sm font-bold uppercase tracking-widest text-white">Send Reply</button>
                </form>
            @else
                <p class="mt-8 rounded-xl bg-gray-100 px-5 py-4 text-sm text-gray-600">This conversation is closed.</p>
            @endif
        </div>
    </div>
</section>
@endsection
