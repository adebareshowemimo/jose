@extends('layouts.app')

@section('title', $pageTitle ?? 'Email Verification')

@section('content')
<section class="py-16 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6 max-w-3xl">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />

        <div class="bg-white border border-[#E0E0E0] rounded-[12px] p-8 text-center">
            <h1 class="text-[#073057] text-[34px] font-extrabold mb-2">{{ $pageTitle ?? 'Verify your email' }}</h1>
            <p class="text-[#6B7280] mb-8">{{ $pageDescription ?? 'We sent a verification link to your inbox. Click the link to activate your account.' }}</p>

            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-[#1AAD94]/10 text-[#1AAD94] mb-6">
                <iconify-icon icon="lucide:mail-check" width="32"></iconify-icon>
            </div>

            @if (session('status') === 'verification-link-sent')
                <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 text-[#0F8B75] text-sm px-4 py-2">
                    <iconify-icon icon="lucide:check-circle" width="16"></iconify-icon>
                    A new verification link has been sent to your email address.
                </div>
            @endif

            <p class="text-[#2C2C2C] mb-6">Didn't receive it? Check your spam folder, or request a new link below.</p>

            <div class="flex flex-wrap justify-center gap-3">
                @auth
                    <form method="POST" action="{{ route('auth.verify-email.resend') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-[#073057] text-white rounded-[8px] font-semibold hover:brightness-110 transition">
                            Resend Verification Email
                        </button>
                    </form>
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center px-6 py-3 border border-[#E0E0E0] text-[#073057] rounded-[8px] font-semibold hover:bg-[#F3F4F6] transition">
                            Log out
                        </button>
                    </form>
                @else
                    <a href="{{ route('auth.login') }}" class="inline-flex items-center justify-center px-6 py-3 border border-[#E0E0E0] text-[#073057] rounded-[8px] font-semibold hover:bg-[#F3F4F6] transition">Back to Login</a>
                @endauth
            </div>
        </div>
    </div>
</section>
@endsection
