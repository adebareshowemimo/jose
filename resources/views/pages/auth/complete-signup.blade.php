@extends('layouts.app')

@section('title', $pageTitle ?? 'Finish setting up your account')

@section('content')
<section class="py-16 bg-[#F9FAFB] min-h-[70vh]">
    <div class="container mx-auto px-6 max-w-2xl">
        <div class="bg-white border border-[#E0E0E0] rounded-[16px] p-8 md:p-10 shadow-sm">
            <h1 class="text-[#073057] text-[28px] md:text-[32px] font-extrabold mb-2">Finish setting up your account</h1>
            <p class="text-[#6B7280] mb-8">Pick the type of account you want and we'll take you to the right dashboard.</p>

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('auth.complete-signup.submit') }}" x-data="{ role: '{{ old('role', 'candidate') }}' }">
                @csrf

                <div class="grid sm:grid-cols-2 gap-4 mb-6">
                    <label :class="role === 'candidate' ? 'border-[#1AAD94] ring-2 ring-[#1AAD94]/30' : 'border-[#E0E0E0]'"
                           class="flex items-start gap-3 p-5 rounded-xl border cursor-pointer hover:border-[#1AAD94] transition">
                        <input type="radio" name="role" value="candidate" x-model="role" class="mt-1 text-[#1AAD94] focus:ring-[#1AAD94]" />
                        <div>
                            <div class="font-bold text-[#073057]">Candidate</div>
                            <div class="text-sm text-[#6B7280]">Find jobs, training, and career opportunities.</div>
                        </div>
                    </label>
                    <label :class="role === 'employer' ? 'border-[#1AAD94] ring-2 ring-[#1AAD94]/30' : 'border-[#E0E0E0]'"
                           class="flex items-start gap-3 p-5 rounded-xl border cursor-pointer hover:border-[#1AAD94] transition">
                        <input type="radio" name="role" value="employer" x-model="role" class="mt-1 text-[#1AAD94] focus:ring-[#1AAD94]" />
                        <div>
                            <div class="font-bold text-[#073057]">Employer</div>
                            <div class="text-sm text-[#6B7280]">Post jobs and manage applications.</div>
                        </div>
                    </label>
                </div>

                <div x-show="role === 'employer'" x-cloak class="mb-6">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-[#073057]/60 mb-2">Company Name</label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}"
                           placeholder="Your company name"
                           class="w-full rounded-xl border border-[#E0E0E0] bg-[#F9FAFB] px-5 py-3 focus:border-[#1AAD94] focus:outline-none focus:ring-1 focus:ring-[#1AAD94]" />
                </div>

                <button type="submit"
                        class="w-full px-6 py-4 bg-[#073057] text-white rounded-xl font-bold uppercase tracking-widest text-sm hover:brightness-110 transition shadow">
                    Continue
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
