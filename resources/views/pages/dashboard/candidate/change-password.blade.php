@extends('layouts.dashboard')

@section('title', 'Change Password')
@section('page-title', 'Change Password')

@section('sidebar-nav')
    @include('pages.dashboard.candidate.partials.sidebar')
@endsection

@section('content')
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-[#073057]">Change Password</h2>
        <p class="text-[#6B7280]">Update your password to keep your account secure</p>
    </div>

    <div class="max-w-xl">
        {{-- Password Form --}}
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-6">
            <form action="{{ route('user.password.update') }}" method="POST" class="space-y-6">
                @csrf
                {{-- Current Password --}}
                <div x-data="{ show: false }">
                    <label for="current_password" class="block text-sm font-medium text-[#073057] mb-2">Current Password *</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" name="current_password" id="current_password" placeholder="Enter current password" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none pr-12 @error('current_password') border-red-500 @enderror" required />
                        <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-[#9CA3AF] hover:text-[#073057] cursor-pointer">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- New Password --}}
                <div x-data="{ show: false, password: '', strength: 0 }" x-init="$watch('password', value => {
                    let s = 0;
                    if(value.length >= 8) s++;
                    if(/[A-Z]/.test(value)) s++;
                    if(/[0-9]/.test(value)) s++;
                    if(/[^A-Za-z0-9]/.test(value)) s++;
                    strength = s;
                })">
                    <label for="password" class="block text-sm font-medium text-[#073057] mb-2">New Password *</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" name="password" id="password" x-model="password" placeholder="Enter new password" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none pr-12 @error('password') border-red-500 @enderror" required minlength="8" />
                        <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-[#9CA3AF] hover:text-[#073057] cursor-pointer">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    <p class="text-xs text-[#6B7280] mt-2">Password must be at least 8 characters</p>
                    @error('password')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror

                    {{-- Password Strength Indicator --}}
                    <div x-show="password.length > 0" class="space-y-2 mt-3">
                        <div class="flex gap-1">
                            <div class="h-1 flex-1 rounded" :class="strength >= 1 ? 'bg-red-500' : 'bg-[#E5E7EB]'"></div>
                            <div class="h-1 flex-1 rounded" :class="strength >= 2 ? 'bg-yellow-500' : 'bg-[#E5E7EB]'"></div>
                            <div class="h-1 flex-1 rounded" :class="strength >= 3 ? 'bg-emerald-500' : 'bg-[#E5E7EB]'"></div>
                            <div class="h-1 flex-1 rounded" :class="strength >= 4 ? 'bg-emerald-500' : 'bg-[#E5E7EB]'"></div>
                        </div>
                        <p class="text-xs" :class="{
                            'text-red-600': strength === 1,
                            'text-yellow-600': strength === 2,
                            'text-emerald-600': strength >= 3
                        }">
                            Password strength: <span x-text="strength === 1 ? 'Weak' : strength === 2 ? 'Fair' : strength === 3 ? 'Good' : strength === 4 ? 'Strong' : 'Too short'"></span>
                        </p>
                    </div>
                </div>

                {{-- Confirm New Password --}}
                <div x-data="{ show: false }">
                    <label for="password_confirmation" class="block text-sm font-medium text-[#073057] mb-2">Confirm New Password *</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" placeholder="Confirm new password" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none pr-12" required />
                        <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-[#9CA3AF] hover:text-[#073057] cursor-pointer">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex gap-4 pt-4">
                    <button type="submit" class="px-6 py-3 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition cursor-pointer">
                        Update Password
                    </button>
                    <a href="{{ route('user.dashboard') }}" class="px-6 py-3 border border-[#E5E7EB] text-[#4B5563] font-semibold rounded-xl hover:bg-[#F9FAFB] transition cursor-pointer">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        {{-- Security Tips --}}
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 mt-6">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <h4 class="font-semibold text-amber-800 mb-1">Password Tips</h4>
                    <ul class="text-sm text-amber-700 space-y-1">
                        <li>• Use at least 8 characters with a mix of letters, numbers, and symbols</li>
                        <li>• Avoid using personal information like your name or birthdate</li>
                        <li>• Don't reuse passwords from other accounts</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
