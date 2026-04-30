@extends('layouts.app')

@section('title', $pageTitle ?? 'Contact')

@section('content')
@php
    $contactPathways = $contactPathways ?? [];
    $img = $jclImages ?? [];
@endphp

{{-- Hero --}}
<section class="relative h-[400px] flex items-center overflow-hidden bg-[#073057]">
    <div class="absolute inset-0 z-0">
        <img src="{{ $img['contact_hero'] ?? '' }}"
             alt="Professional reaching out to the JCL team"
             class="h-full w-full object-cover opacity-40" loading="eager" />
        <div class="absolute inset-0 bg-gradient-to-r from-[#073057] via-[#073057]/80 to-transparent"></div>
    </div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-xs font-bold uppercase tracking-widest text-[#1AAD94]" />
        <h1 class="text-[48px] md:text-[64px] font-extrabold leading-none text-white">Get in touch with <br/><span class="text-[#1AAD94]">our Team.</span></h1>
        <p class="mt-6 max-w-xl text-lg text-white/70">{{ $pageDescription ?? 'Have questions about our training programs, job placements, or consulting services? We are here to help you navigate your maritime career.' }}</p>
    </div>
</section>

{{-- Form + Sidebar --}}
<section class="py-24 container mx-auto px-6">
    <div class="grid gap-8 lg:grid-cols-12">
        {{-- Main form column --}}
        <div class="lg:col-span-8">
            <div class="rounded-[32px] bg-white border border-[#E0E0E0] p-8 md:p-12 shadow-sm">
                {{-- Category picker cards --}}
                <div class="grid gap-4 md:grid-cols-3 mb-12">
                    @foreach($contactPathways as $pathway)
                        <div class="rounded-2xl bg-[#F9FAFB] border border-[#E0E0E0] p-5 transition-all hover:border-[#1AAD94]">
                            <div class="flex items-center justify-between mb-3">
                                <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center text-[#1AAD94] shadow-sm">
                                    <iconify-icon icon="{{ $pathway['icon'] ?? 'lucide:help-circle' }}" class="text-xl"></iconify-icon>
                                </div>
                                <iconify-icon icon="lucide:arrow-up-right" class="text-gray-300"></iconify-icon>
                            </div>
                            <h4 class="text-sm font-bold text-[#073057] mb-1">{{ $pathway['title'] }}</h4>
                            <p class="text-xs text-[#6B7280]">{{ $pathway['description'] }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Form --}}
                <div class="mb-10">
                    <h2 class="text-[32px] font-extrabold text-[#073057] mb-4">Send a Message</h2>
                    <p class="text-[#6B7280]">Fill out the form below and a member of our team will reach out within 24 hours.</p>
                </div>
                @if(session('success'))
                    <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                        <p class="font-semibold mb-1">Please fix the form errors.</p>
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('contact.store') }}" class="grid gap-6 md:grid-cols-2">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold uppercase tracking-widest text-[#073057]/60">Full Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="John Doe" required class="w-full rounded-xl border border-[#E0E0E0] bg-[#F9FAFB] px-5 py-4 focus:border-[#1AAD94] focus:outline-none focus:ring-1 focus:ring-[#1AAD94] transition-all" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold uppercase tracking-widest text-[#073057]/60">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="john@company.com" required class="w-full rounded-xl border border-[#E0E0E0] bg-[#F9FAFB] px-5 py-4 focus:border-[#1AAD94] focus:outline-none focus:ring-1 focus:ring-[#1AAD94] transition-all" />
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-[11px] font-bold uppercase tracking-widest text-[#073057]/60">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+234..." class="w-full rounded-xl border border-[#E0E0E0] bg-[#F9FAFB] px-5 py-4 focus:border-[#1AAD94] focus:outline-none focus:ring-1 focus:ring-[#1AAD94] transition-all" />
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-[11px] font-bold uppercase tracking-widest text-[#073057]/60">Subject</label>
                        <select name="subject" class="w-full rounded-xl border border-[#E0E0E0] bg-[#F9FAFB] px-5 py-4 focus:border-[#1AAD94] focus:outline-none focus:ring-1 focus:ring-[#1AAD94] transition-all">
                            @foreach($contactSubjects ?? [] as $subject)
                                <option value="{{ $subject }}" {{ old('subject') === $subject ? 'selected' : '' }}>{{ $subject }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-[11px] font-bold uppercase tracking-widest text-[#073057]/60">Message</label>
                        <textarea name="message" rows="5" placeholder="How can we help you?" required class="w-full rounded-xl border border-[#E0E0E0] bg-[#F9FAFB] px-5 py-4 focus:border-[#1AAD94] focus:outline-none focus:ring-1 focus:ring-[#1AAD94] transition-all">{{ old('message') }}</textarea>
                    </div>
                    <div class="md:col-span-2 pt-4 flex flex-col sm:flex-row gap-4">
                        <button type="submit" class="px-8 py-4 bg-[#073057] text-white rounded-xl font-bold uppercase tracking-widest text-sm hover:brightness-110 transition-all shadow-lg">Submit Inquiry</button>
                        <a href="{{ route('auth.register') }}" class="px-8 py-4 border-2 border-[#E0E0E0] text-[#073057] rounded-xl font-bold uppercase tracking-widest text-sm hover:border-[#1AAD94] hover:text-[#1AAD94] transition-all flex items-center justify-center">Start Your Pathway</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-4 space-y-8">
            {{-- Contact methods --}}
            <div class="rounded-[32px] bg-white border border-[#E0E0E0] p-8 shadow-sm">
                <h3 class="text-xl font-extrabold text-[#073057] mb-4">Best way to reach JCL</h3>
                <p class="text-sm leading-relaxed text-[#6B7280] mb-6">Whether you're an individual looking to level up or an organization looking to build a pipeline, we have specialists dedicated to each sector.</p>
                <p class="text-sm leading-relaxed text-[#6B7280] mb-8">Consulting and training enquiries are handled through the form — our team will connect you with Our Team.</p>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#1AAD94]/10 text-[#1AAD94]">
                            <iconify-icon icon="lucide:mail" class="text-xl"></iconify-icon>
                        </div>
                        <div>
                            <h5 class="text-[11px] font-bold uppercase tracking-widest text-[#073057]/60 mb-1">Email Us</h5>
                            <p class="text-sm font-semibold text-[#073057]">info@joseoceanjobs.com</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#1AAD94]/10 text-[#1AAD94]">
                            <iconify-icon icon="lucide:phone-call" class="text-xl"></iconify-icon>
                        </div>
                        <div>
                            <h5 class="text-[11px] font-bold uppercase tracking-widest text-[#073057]/60 mb-1">Phone Support</h5>
                            <p class="text-sm font-semibold text-[#073057]">9024304210</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#1AAD94]/10 text-[#1AAD94]">
                            <iconify-icon icon="lucide:map-pin" class="text-xl"></iconify-icon>
                        </div>
                        <div>
                            <h5 class="text-[11px] font-bold uppercase tracking-widest text-[#073057]/60 mb-1">Global Office</h5>
                            <p class="text-sm font-semibold text-[#073057]">10 Engineering Close, off Idowu Taylor,<br>Victoria Island, Lagos</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Next Steps dark card --}}
            <div class="rounded-[32px] bg-[#073057] text-white overflow-hidden shadow-xl relative">
                <img src="{{ $img['deck_officer'] ?? '' }}"
                     alt="Maritime professional onboard vessel"
                     class="h-48 w-full object-cover object-center opacity-60" loading="lazy" />
                <div class="p-8">
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-[#7DE1D1] mb-6">
                        <iconify-icon icon="lucide:compass" class="text-sm"></iconify-icon>
                        <span>Next Steps</span>
                    </div>
                    <h3 class="text-2xl font-extrabold mb-4">Prefer a self-service route?</h3>
                    <p class="text-sm text-white/70 leading-relaxed mb-8">Quickly find what you're looking for by browsing our active job board or learning more about our operational model.</p>
                    <div class="space-y-3">
                        <a href="{{ route('job.index') }}" class="flex items-center justify-between group p-4 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 transition-all">
                            <span class="font-bold text-sm tracking-wide">Browse open jobs</span>
                            <iconify-icon icon="lucide:arrow-right" class="group-hover:translate-x-1 transition-transform"></iconify-icon>
                        </a>
                        <a href="{{ route('about.index') }}" class="flex items-center justify-between group p-4 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 transition-all">
                            <span class="font-bold text-sm tracking-wide">Learn about JCL</span>
                            <iconify-icon icon="lucide:arrow-right" class="group-hover:translate-x-1 transition-transform"></iconify-icon>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
