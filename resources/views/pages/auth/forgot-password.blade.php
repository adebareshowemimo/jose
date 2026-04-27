@extends('layouts.app')

@section('title', $pageTitle ?? 'Forgot Password')

@section('content')
<section class="py-16 bg-[#F9FAFB] min-h-[65vh]">
    <div class="container mx-auto px-6 max-w-2xl">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />

        <div class="bg-white border border-[#E0E0E0] rounded-[16px] p-8 md:p-10 shadow-sm">
            <h1 class="text-[#073057] text-[32px] font-extrabold mb-2">{{ $pageTitle ?? 'Forgot your password?' }}</h1>
            <p class="text-[#6B7280] mb-6">{{ $pageDescription ?? 'Enter the email tied to your account and we\'ll send you a link to set a new password.' }}</p>

            @if (session('status'))
                <div class="mb-5 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('auth.forgot-password.submit') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-[#073057]/60 mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="you@example.com"
                           class="w-full rounded-xl border border-[#E0E0E0] bg-[#F9FAFB] px-5 py-3.5 focus:border-[#1AAD94] focus:outline-none focus:ring-1 focus:ring-[#1AAD94] transition" />
                </div>
                <div class="flex flex-wrap gap-3 pt-2">
                    <button type="submit"
                            class="px-6 py-3 bg-[#073057] text-white rounded-xl font-bold uppercase tracking-widest text-sm hover:brightness-110 transition shadow">
                        Send Reset Link
                    </button>
                    <a href="{{ route('auth.login') }}"
                       class="px-6 py-3 border-2 border-[#E0E0E0] text-[#073057] rounded-xl font-bold uppercase tracking-widest text-sm hover:border-[#1AAD94] hover:text-[#1AAD94] transition flex items-center">
                        Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
