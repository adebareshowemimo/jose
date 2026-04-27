@extends('admin.layouts.app')

@section('title', 'Contact Submissions')
@section('page-title', 'Contact Submissions')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Contact Submissions</h2>
            <p class="text-sm text-gray-500">Manage website enquiries and visitor conversations.</p>
        </div>
        <a href="{{ route('admin.contacts.create') }}" class="px-4 py-2 bg-[#1AAD94] text-white text-sm font-semibold rounded-lg hover:bg-[#158f7a]">Add Contact</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="text-xs font-medium text-gray-500 mb-1 block">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, subject..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                    <option value="">All</option>
                    @foreach(['new', 'in_progress', 'customer_replied', 'resolved', 'closed', 'spam'] as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Priority</label>
                <select name="priority" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                    <option value="">All</option>
                    @foreach(['low', 'normal', 'high', 'urgent'] as $priority)
                        <option value="{{ $priority }}" {{ request('priority') === $priority ? 'selected' : '' }}>{{ ucfirst($priority) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-[#073057] text-white text-sm font-medium rounded-lg hover:bg-[#073057]/90">Filter</button>
            @if(request()->hasAny(['search', 'status', 'priority']))
                <a href="{{ route('admin.contacts.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Contact</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Subject</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Status</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Priority</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Received</th>
                        <th class="text-right px-5 py-3 font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($contacts as $contact)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-900">{{ $contact->name }}</p>
                                <p class="text-xs text-gray-500">{{ $contact->email }} @if($contact->phone) · {{ $contact->phone }} @endif</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-gray-800">{{ $contact->subject }}</p>
                                <p class="text-xs text-gray-500">{{ $contact->category }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ in_array($contact->status, ['new', 'customer_replied']) ? 'bg-green-100 text-green-700' : ($contact->status === 'closed' ? 'bg-gray-100 text-gray-600' : 'bg-amber-100 text-amber-700') }}">
                                    {{ ucfirst(str_replace('_', ' ', $contact->status)) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-gray-600">{{ ucfirst($contact->priority) }}</td>
                            <td class="px-5 py-4 text-gray-500">{{ $contact->created_at?->format('M d, Y') }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.contacts.show', $contact) }}" class="text-[#1AAD94] hover:underline">Open</a>
                                    <a href="{{ route('admin.contacts.edit', $contact) }}" class="text-gray-500 hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.contacts.destroy', $contact) }}" onsubmit="return confirm('Delete this contact submission?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-500 hover:underline">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No contact submissions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($contacts->hasPages())
            <div class="px-5 py-3 border-t border-gray-200">{{ $contacts->links() }}</div>
        @endif
    </div>
@endsection
