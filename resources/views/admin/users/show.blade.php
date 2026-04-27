@extends('admin.layouts.app')

@section('title', 'User: ' . $user->name)
@section('page-title', 'User Details')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.users') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Users
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- User Info Card --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 bg-[#073057] rounded-full mx-auto flex items-center justify-center text-white text-2xl font-bold mb-3">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <span class="inline-block mt-2 text-xs px-2 py-1 rounded-full
                        {{ $user->role?->name === 'admin' ? 'bg-red-100 text-red-700' : ($user->role?->name === 'employer' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                        {{ ucfirst($user->role?->name ?? 'N/A') }}
                    </span>
                </div>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd><span class="px-2 py-0.5 rounded-full text-xs {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ ucfirst($user->status ?? 'N/A') }}</span></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Verified</dt>
                        <dd>{{ $user->is_verified ? 'Yes' : 'No' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Phone</dt>
                        <dd>{{ $user->phone ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Joined</dt>
                        <dd>{{ $user->created_at?->format('M d, Y') ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Company (if employer) --}}
            @if($user->company)
                <div class="bg-white rounded-xl border border-gray-200 p-6 mt-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Company</h3>
                    <a href="{{ route('admin.companies.show', $user->company) }}" class="text-[#1AAD94] hover:underline font-medium">
                        {{ $user->company->name }}
                    </a>
                    <p class="text-xs text-gray-500 mt-1">{{ $user->company->email ?? '—' }}</p>
                </div>
            @endif
        </div>

        {{-- Edit Form --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Update User --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Edit User</h3>
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf @method('PUT')
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Role</label>
                            <select name="role_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                                @foreach(\App\Models\Role::all() as $role)
                                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                                @foreach(['active', 'inactive', 'banned'] as $s)
                                    <option value="{{ $s }}" {{ $user->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2 flex items-center gap-2">
                            <input type="checkbox" name="is_verified" value="1" {{ $user->is_verified ? 'checked' : '' }}
                                class="rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]" id="is_verified">
                            <label for="is_verified" class="text-sm text-gray-700">Email Verified</label>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-5 py-2 bg-[#1AAD94] text-white text-sm font-medium rounded-lg hover:bg-[#1AAD94]/90">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            {{-- Reset Password --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Reset Password</h3>
                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">New Password</label>
                            <input type="password" name="password" required minlength="8"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Confirm Password</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-5 py-2 bg-amber-500 text-white text-sm font-medium rounded-lg hover:bg-amber-600">
                            Reset Password
                        </button>
                    </div>
                </form>
            </div>

            {{-- Subscriptions --}}
            @if($user->subscriptions->isNotEmpty())
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Subscriptions</h3>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-2 font-medium text-gray-500">Plan</th>
                                <th class="text-left py-2 font-medium text-gray-500">Cycle</th>
                                <th class="text-left py-2 font-medium text-gray-500">Period</th>
                                <th class="text-left py-2 font-medium text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($user->subscriptions as $sub)
                                <tr>
                                    <td class="py-2 font-medium text-gray-900">{{ $sub->plan?->name ?? '—' }}</td>
                                    <td class="py-2 text-gray-600">{{ ucfirst($sub->billing_cycle ?? '—') }}</td>
                                    <td class="py-2 text-gray-600">{{ $sub->starts_at?->format('M d') }} – {{ $sub->ends_at?->format('M d, Y') }}</td>
                                    <td class="py-2">
                                        <span class="text-xs px-2 py-0.5 rounded-full {{ $sub->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ ucfirst($sub->status ?? 'N/A') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Delete User --}}
            @if($user->role?->name !== 'admin')
                <div class="bg-white rounded-xl border border-red-200 p-6">
                    <h3 class="text-base font-semibold text-red-600 mb-2">Danger Zone</h3>
                    <p class="text-sm text-gray-500 mb-4">Permanently delete this user account. This action cannot be undone.</p>
                    <form method="POST" action="{{ route('admin.users.delete', $user) }}"
                          onsubmit="return confirm('Are you sure you want to delete this user?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-5 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                            Delete User
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
