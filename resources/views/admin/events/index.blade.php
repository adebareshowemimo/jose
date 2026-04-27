@extends('admin.layouts.app')

@section('title', 'Events')
@section('page-title', 'Events')

@section('content')
    <div class="grid gap-6 xl:grid-cols-[420px_1fr]">
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Add Event</h2>
                <form method="POST" action="{{ route('admin.events.store') }}" class="space-y-4">
                    @csrf
                    @include('admin.events.partials.form', ['event' => null])
                    <button type="submit" class="w-full px-4 py-2.5 bg-[#1AAD94] text-white text-sm font-semibold rounded-lg hover:bg-[#158f7a]">
                        Create Event
                    </button>
                </form>
            </div>
        </div>

        <div class="space-y-6">
            @if($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-semibold mb-1">Please fix the highlighted event details.</p>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <form method="GET" class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[180px]">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Title, type, location..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Category</label>
                        <select name="category" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                            <option value="">All</option>
                            <option value="hosted" {{ request('category') === 'hosted' ? 'selected' : '' }}>JCL hosted</option>
                            <option value="industry" {{ request('category') === 'industry' ? 'selected' : '' }}>Industry</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
                        <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                            <option value="">All</option>
                            @foreach(['upcoming', 'active', 'completed', 'draft', 'cancelled'] as $status)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-[#073057] text-white text-sm font-medium rounded-lg hover:bg-[#073057]/90">Filter</button>
                    @if(request()->hasAny(['search', 'category', 'status']))
                        <a href="{{ route('admin.events.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Clear</a>
                    @endif
                </form>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="text-left px-5 py-3 font-medium text-gray-500">Event</th>
                                <th class="text-left px-5 py-3 font-medium text-gray-500">Category</th>
                                <th class="text-left px-5 py-3 font-medium text-gray-500">Date</th>
                                <th class="text-left px-5 py-3 font-medium text-gray-500">Status</th>
                                <th class="text-right px-5 py-3 font-medium text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($events as $event)
                                <tr class="align-top hover:bg-gray-50" x-data="{ editing: false }">
                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-gray-900">{{ $event->title }}</p>
                                        <p class="mt-1 text-xs text-gray-500">{{ $event->type }} · {{ $event->location }}</p>
                                        <p class="mt-2 max-w-xl text-xs leading-relaxed text-gray-500">{{ $event->description }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $event->category === 'hosted' ? 'bg-[#1AAD94]/10 text-[#158f7a]' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $event->category === 'hosted' ? 'JCL hosted' : 'Industry' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-gray-600">{{ $event->display_date }}</td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium
                                            {{ in_array($event->status, ['upcoming', 'active']) ? 'bg-green-100 text-green-700' : ($event->status === 'draft' ? 'bg-gray-100 text-gray-600' : ($event->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')) }}">
                                            {{ ucfirst($event->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <button type="button" @click="editing = !editing" class="text-[#1AAD94] hover:underline">Edit</button>
                                            <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Delete this event?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:underline">Delete</button>
                                            </form>
                                        </div>
                                        <div x-show="editing" x-cloak class="mt-4 text-left rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                            <form method="POST" action="{{ route('admin.events.update', $event) }}" class="space-y-4">
                                                @csrf
                                                @method('PUT')
                                                @include('admin.events.partials.form', ['event' => $event])
                                                <div class="flex justify-end gap-2">
                                                    <button type="button" @click="editing = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 bg-[#073057] text-white text-sm font-semibold rounded-lg hover:bg-[#073057]/90">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-gray-400">No events found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($events->hasPages())
                    <div class="px-5 py-3 border-t border-gray-200">{{ $events->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
