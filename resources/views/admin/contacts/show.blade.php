@extends('admin.layouts.app')

@section('title', 'Contact Detail')
@section('page-title', 'Contact Detail')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">{{ $contact->subject }}</h2>
            <p class="text-sm text-gray-500">From {{ $contact->name }} · {{ $contact->email }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('contact.thread', $contact->reply_token) }}" target="_blank" class="px-4 py-2 text-sm font-medium text-[#073057] border border-gray-300 rounded-lg hover:bg-gray-50">Public Thread</a>
            <a href="{{ route('admin.contacts.edit', $contact) }}" class="px-4 py-2 text-sm font-medium text-white bg-[#073057] rounded-lg hover:bg-[#073057]/90">Edit</a>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1fr_380px]">
        <div class="space-y-6">
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Conversation</h3>
                <div class="space-y-4">
                    @foreach($contact->chronologicalMessages as $message)
                        <div class="rounded-xl border {{ $message->sender_type === 'admin' ? 'border-[#1AAD94]/20 bg-[#1AAD94]/5' : 'border-gray-200 bg-gray-50' }} p-5">
                            <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $message->sender_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $message->sender_email }} · {{ ucfirst($message->sender_type) }}</p>
                                </div>
                                <p class="text-xs text-gray-500">{{ $message->created_at?->format('M d, Y g:i A') }}</p>
                            </div>
                            <p class="whitespace-pre-line text-sm leading-relaxed text-gray-700">{{ $message->body }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Respond to User</h3>
                <form method="POST" action="{{ route('admin.contacts.respond', $contact) }}" class="space-y-4">
                    @csrf
                    <textarea name="message" rows="7" required placeholder="Write your response..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-[#1AAD94]">{{ old('message') }}</textarea>
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#1AAD94]">
                            <option value="in_progress" {{ old('status', 'in_progress') === 'in_progress' ? 'selected' : '' }}>Keep in progress</option>
                            <option value="resolved" {{ old('status') === 'resolved' ? 'selected' : '' }}>Mark resolved</option>
                            <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Close conversation</option>
                        </select>
                        <button type="submit" class="px-4 py-2 bg-[#1AAD94] text-white text-sm font-semibold rounded-lg hover:bg-[#158f7a]">Send Response</button>
                    </div>
                </form>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Details</h3>
                <dl class="space-y-3 text-sm">
                    <div><dt class="text-gray-500">Status</dt><dd class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $contact->status)) }}</dd></div>
                    <div><dt class="text-gray-500">Priority</dt><dd class="font-medium text-gray-900">{{ ucfirst($contact->priority) }}</dd></div>
                    <div><dt class="text-gray-500">Category</dt><dd class="font-medium text-gray-900">{{ $contact->category }}</dd></div>
                    <div><dt class="text-gray-500">Phone</dt><dd class="font-medium text-gray-900">{{ $contact->phone ?: '—' }}</dd></div>
                    <div><dt class="text-gray-500">Received</dt><dd class="font-medium text-gray-900">{{ $contact->created_at?->format('M d, Y g:i A') }}</dd></div>
                    <div><dt class="text-gray-500">Last Response</dt><dd class="font-medium text-gray-900">{{ $contact->last_responded_at?->format('M d, Y g:i A') ?? '—' }}</dd></div>
                </dl>
            </div>

            <div class="rounded-xl border border-red-200 bg-white p-6">
                <h3 class="mb-3 text-sm font-semibold text-red-700">Delete Submission</h3>
                <p class="mb-4 text-sm text-gray-500">This removes the submission and all conversation messages.</p>
                <form method="POST" action="{{ route('admin.contacts.destroy', $contact) }}" onsubmit="return confirm('Delete this contact submission?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Delete</button>
                </form>
            </div>
        </aside>
    </div>
@endsection
