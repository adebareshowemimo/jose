@extends('admin.layouts.app')

@section('title', 'Manage Users')
@section('page-title', 'Users')

@section('content')
    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="text-xs font-medium text-gray-500 mb-1 block">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Role</label>
                <select name="role" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                    <option value="">All</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Banned</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-[#073057] text-white text-sm font-medium rounded-lg hover:bg-[#073057]/90">
                Filter
            </button>
            @if(request()->hasAny(['search', 'role', 'status']))
                <a href="{{ route('admin.users') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Clear</a>
            @endif
        </form>
    </div>

    {{-- Users Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3 font-medium text-gray-500">User</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Role</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Status</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Verified</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Joined</th>
                        <th class="text-right px-5 py-3 font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-gray-200 rounded-full flex items-center justify-center text-sm font-semibold text-gray-600 shrink-0">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-xs px-2 py-1 rounded-full
                                    {{ $user->role?->name === 'admin' ? 'bg-red-100 text-red-700' : ($user->role?->name === 'employer' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                    {{ ucfirst($user->role?->name ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-xs px-2 py-1 rounded-full
                                    {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : ($user->status === 'banned' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                                    {{ ucfirst($user->status ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                @if($user->is_verified)
                                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-500">{{ $user->created_at?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center gap-1 text-[#1AAD94] hover:underline text-sm">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-gray-400">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-5 py-3 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
