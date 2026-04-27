@extends('layouts.dashboard')

@section('title', 'Change Password')
@section('page-title', 'Change Password')

@section('sidebar-nav')
    @include('pages.dashboard.employer.partials.sidebar')
@endsection

@section('content')
    {{-- Page Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-[#073057]">Change Password</h2>
        <p class="text-[#6B7280]">Update your password to keep your account secure</p>
    </div>

    <div class="max-w-xl">
        {{-- Password Form --}}
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-6">
            <form class="space-y-6" x-data="{ currentShow: false, newShow: false, confirmShow: false }">
                {{-- Current Password --}}
                <div>
                    <label class="block text-sm font-medium text-[#073057] mb-2">Current Password *</label>
                    <div class="relative">
                        <input :type="currentShow ? 'text' : 'password'" placeholder="Enter current password" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none pr-12" />
                        <button type="button" @click="currentShow = !currentShow" class="absolute right-4 top-1/2 -translate-y-1/2 text-[#9CA3AF] hover:text-[#073057]">
                            <svg x-show="!currentShow" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="currentShow" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                {{-- New Password --}}
                <div>
                    <label class="block text-sm font-medium text-[#073057] mb-2">New Password *</label>
                    <div class="relative">
                        <input :type="newShow ? 'text' : 'password'" placeholder="Enter new password" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none pr-12" />
                        <button type="button" @click="newShow = !newShow" class="absolute right-4 top-1/2 -translate-y-1/2 text-[#9CA3AF] hover:text-[#073057]">
                            <svg x-show="!newShow" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="newShow" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    <p class="text-xs text-[#6B7280] mt-2">Password must be at least 8 characters</p>
                </div>

                {{-- Password Strength --}}
                <div class="space-y-2">
                    <div class="flex gap-1">
                        <div class="h-1 flex-1 bg-emerald-500 rounded"></div>
                        <div class="h-1 flex-1 bg-emerald-500 rounded"></div>
                        <div class="h-1 flex-1 bg-emerald-500 rounded"></div>
                        <div class="h-1 flex-1 bg-[#E5E7EB] rounded"></div>
                    </div>
                    <p class="text-xs text-emerald-600">Password strength: Good</p>
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label class="block text-sm font-medium text-[#073057] mb-2">Confirm New Password *</label>
                    <div class="relative">
                        <input :type="confirmShow ? 'text' : 'password'" placeholder="Confirm new password" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none pr-12" />
                        <button type="button" @click="confirmShow = !confirmShow" class="absolute right-4 top-1/2 -translate-y-1/2 text-[#9CA3AF] hover:text-[#073057]">
                            <svg x-show="!confirmShow" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="confirmShow" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex gap-4 pt-4">
                    <button type="submit" class="px-6 py-3 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition">
                        Update Password
                    </button>
                    <button type="button" class="px-6 py-3 border border-[#E5E7EB] text-[#4B5563] font-semibold rounded-xl hover:bg-[#F9FAFB] transition">
                        Cancel
                    </button>
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
