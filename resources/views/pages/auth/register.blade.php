@extends('layouts.app')

@section('title', $pageTitle ?? 'Register')

@push('styles')
<style>
    .register-section { min-height: 100vh; display: flex; position: relative; }
    .register-section .image-layer {
        position: absolute; right: 0; top: 0; width: 45%; height: 100%;
        background-size: cover; background-position: center;
    }
    @media (max-width: 1023px) {
        .register-section .image-layer { display: none; }
    }
</style>
@endpush

@section('content')
<section class="register-section">
    {{-- Background Image Side --}}
    <div class="image-layer">
        @include('pages.auth.partials.image-slider')
    </div>
    
    {{-- Form Side --}}
    <div class="w-full lg:w-[55%] flex items-center justify-center py-12 px-6 bg-white min-h-screen">
        <div class="w-full max-w-xl">
            {{-- Register Form --}}
            <div class="register-form">
                <h3 class="text-[32px] font-bold text-[#073057] mb-2">Create Your Account</h3>
                <p class="text-[#6B7280] mb-6">Join thousands of maritime professionals</p>

                <form method="POST" action="{{ route('auth.register.post') }}" class="space-y-4">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Account Type Dropdown --}}
                    <div x-data="{ role: '{{ old('role', '') }}' }">
                        <div>
                            <label class="block text-sm font-medium text-[#374151] mb-2">Account Type</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </span>
                                <select name="role" required x-model="role"
                                    class="w-full pl-12 pr-10 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none transition appearance-none bg-white">
                                    <option value="">Select account type</option>
                                    <option value="candidate">Candidate - Looking for jobs</option>
                                    <option value="employer">Employer - Hiring talent</option>
                                </select>
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[#9CA3AF] pointer-events-none">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </span>
                            </div>
                        </div>

                        {{-- Company Name (shown when employer is selected) --}}
                        <div x-show="role === 'employer'" x-cloak x-transition class="mt-4">
                            <label class="block text-sm font-medium text-[#374151] mb-2">Company Name <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </span>
                                <input type="text" name="company_name" placeholder="Enter your company name" value="{{ old('company_name') }}"
                                    x-bind:required="role === 'employer'"
                                    class="w-full pl-12 pr-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none transition" />
                            </div>
                            @error('company_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-[#374151] mb-2">First Name</label>
                            <input type="text" name="first_name" required placeholder="John" value="{{ old('first_name') }}"
                                class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none transition" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#374151] mb-2">Last Name</label>
                            <input type="text" name="last_name" required placeholder="Doe" value="{{ old('last_name') }}"
                                class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none transition" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#374151] mb-2">Email Address</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                            </span>
                            <input type="email" name="email" required placeholder="you@example.com" value="{{ old('email') }}"
                                class="w-full pl-12 pr-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none transition" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#374151] mb-2">Phone Number</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </span>
                            <input type="tel" name="phone" placeholder="+44 7700 900000" value="{{ old('phone') }}"
                                class="w-full pl-12 pr-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none transition" />
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-[#374151] mb-2">Password</label>
                            <div class="relative" x-data="{ show: false }">
                                <input :type="show ? 'text' : 'password'" name="password" required placeholder="••••••••"
                                    class="w-full px-4 py-3 pr-12 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none transition" />
                                <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-[#9CA3AF] hover:text-[#6B7280]">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#374151] mb-2">Confirm Password</label>
                            <div class="relative" x-data="{ show: false }">
                                <input :type="show ? 'text' : 'password'" name="password_confirmation" required placeholder="••••••••"
                                    class="w-full px-4 py-3 pr-12 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none transition" />
                                <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-[#9CA3AF] hover:text-[#6B7280]">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="inline-flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="terms" required class="w-5 h-5 mt-0.5 rounded border-[#D1D5DB] text-[#1AAD94] focus:ring-[#1AAD94]" />
                            <span class="text-sm text-[#4B5563]">I agree to the <a href="#" class="text-[#1AAD94] hover:underline">Terms of Service</a> and <a href="#" class="text-[#1AAD94] hover:underline">Privacy Policy</a></span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-3.5 px-6 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition duration-200 shadow-lg shadow-[#1AAD94]/20">
                        Create Account
                    </button>
                </form>

                {{-- Divider --}}
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-[#E5E7EB]"></div></div>
                    <div class="relative flex justify-center"><span class="px-4 bg-white text-sm text-[#9CA3AF]">or</span></div>
                </div>

                {{-- Social Login --}}
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('auth.social.redirect', 'google') }}" class="flex items-center justify-center gap-2 py-3 px-4 border border-[#E5E7EB] rounded-xl hover:bg-[#F9FAFB] transition">
                        <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        <span class="text-sm font-medium text-[#374151]">Google</span>
                    </a>
                    <a href="{{ route('auth.social.redirect', 'microsoft') }}" class="flex items-center justify-center gap-2 py-3 px-4 border border-[#E5E7EB] rounded-xl hover:bg-[#F9FAFB] transition">
                        <svg class="w-5 h-5" viewBox="0 0 23 23"><path fill="#F25022" d="M1 1h10v10H1z"/><path fill="#7FBA00" d="M12 1h10v10H12z"/><path fill="#00A4EF" d="M1 12h10v10H1z"/><path fill="#FFB900" d="M12 12h10v10H12z"/></svg>
                        <span class="text-sm font-medium text-[#374151]">Microsoft</span>
                    </a>
                </div>

                {{-- Login Link --}}
                <p class="mt-6 text-center text-[#6B7280]">
                    Already have an account? 
                    <a href="{{ route('auth.login') }}" class="font-semibold text-[#1AAD94] hover:text-[#158f7a] transition">Sign In</a>
                </p>
            </div>
        </div>
    </div>
</section>
@endsection
