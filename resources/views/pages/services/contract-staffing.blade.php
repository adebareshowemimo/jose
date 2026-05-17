@extends('layouts.app')

@section('title', $pageTitle . ' — Jose Ocean Jobs')
@section('meta_description', $pageDescription)

@section('content')
@php $img = $jclImages ?? []; @endphp

{{-- Hero --}}
<section class="relative h-[400px] flex items-center overflow-hidden bg-[#073057]">
    @if (!empty($img['crew_management']))
        <img src="{{ $img['crew_management'] }}"
             alt="Contract Staffing"
             class="absolute inset-0 w-full h-full object-cover opacity-40" loading="eager" />
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-[#073057] via-[#073057]/85 to-transparent"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <h1 class="text-[48px] md:text-[64px] font-extrabold text-white leading-none">Contract Staffing</h1>
        <p class="mt-4 max-w-2xl text-lg text-white/70">Your Skills, Our Structure — A Career Built the Right Way.</p>
    </div>
</section>

{{-- What Is Contract Staffing? --}}
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-start">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-6">
                    <iconify-icon icon="lucide:briefcase"></iconify-icon>
                    Contract Staffing
                </div>
                <h2 class="text-3xl font-extrabold text-[#073057] leading-tight mb-6">What Is Contract Staffing?</h2>
                <p class="text-[#4B5563] leading-relaxed mb-5">Contract Staffing is our dedicated space for contract-based employment opportunities within the Maritime and Energy sector. These are not your typical job listings — they are structured positions where companies take on professionals for a defined contract period, provide training and development throughout, and retain the best performers at the end.</p>
                <p class="text-[#4B5563] leading-relaxed mb-5"><span class="font-bold text-[#073057]">It is a career pathway, not just a job.</span></p>
                <p class="text-[#4B5563] leading-relaxed mb-8">Whether you are just starting out or looking to grow your expertise within a structured environment, Contract Staffing gives you the opportunity to prove yourself, gain real industry experience, and build a future with a company that has already invested in you.</p>
                <a href="{{ route('services.contract-staffing.jobs') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all">
                    View Open Roles
                    <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                </a>
            </div>
            <div class="rounded-[32px] overflow-hidden shadow-xl bg-gradient-to-br from-[#073057] via-[#0a4275] to-[#1AAD94] min-h-[480px] flex items-center justify-center text-white">
                @if (!empty($img['crew_management']))
                    <img src="{{ $img['crew_management'] }}" alt="Contract Staffing" class="w-full h-[480px] object-cover" />
                @else
                    <div class="text-center p-12">
                        <iconify-icon icon="lucide:briefcase" class="text-7xl mb-4 opacity-80"></iconify-icon>
                        <p class="text-lg font-bold uppercase tracking-[0.15em] opacity-80">A Career Pathway</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- How It Works --}}
<section class="py-20 bg-[#F9FAFB]">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mb-12">
            <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-4">
                <iconify-icon icon="lucide:list-checks"></iconify-icon>
                The Process
            </div>
            <h2 class="text-3xl font-extrabold text-[#073057] leading-tight">How It Works</h2>
        </div>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['01', 'lucide:file-text', 'Companies Post Roles', 'Companies post contract roles with defined durations, training commitments, and retention criteria.'],
                ['02', 'lucide:user-check', 'You Apply', 'You apply for the role that fits your background and career goals.'],
                ['03', 'lucide:graduation-cap', 'Join + Train', 'You join on a contract basis and receive structured training throughout your engagement.'],
                ['04', 'lucide:badge-check', 'Permanent Offer', 'At the end of the contract, strong performers are offered permanent positions within the company.'],
            ] as $step)
                <div class="rounded-[24px] bg-white border border-[#E5E7EB] p-7 shadow-sm hover:shadow-lg transition-all">
                    <div class="text-[#1AAD94] text-[28px] font-extrabold leading-none mb-4">{{ $step[0] }}</div>
                    <iconify-icon icon="{{ $step[1] }}" class="text-3xl text-[#073057] mb-4"></iconify-icon>
                    <h3 class="text-[18px] font-extrabold text-[#073057] mb-2">{{ $step[2] }}</h3>
                    <p class="text-sm text-[#6B7280] leading-relaxed">{{ $step[3] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Why Consider a Contract Role --}}
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mb-12">
            <div class="inline-flex items-center gap-2 rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-4">
                <iconify-icon icon="lucide:sparkles"></iconify-icon>
                Why It Matters
            </div>
            <h2 class="text-3xl font-extrabold text-[#073057] leading-tight">Why Consider a Contract Role?</h2>
        </div>
        <div class="grid gap-6 md:grid-cols-2">
            @foreach ([
                ['lucide:wrench', 'Hands-on Training & Experience', 'Gain hands-on training and experience backed by an established company.'],
                ['lucide:file-badge', 'Build Your CV', 'Build your CV with credible, structured Maritime and Energy sector experience.'],
                ['lucide:milestone', 'A Clear Pathway', 'A clear pathway to permanent employment for those who perform.'],
                ['lucide:trending-up', 'Companies Invested in You', 'Work with companies that are genuinely invested in your growth.'],
            ] as $benefit)
                <div class="flex gap-5 rounded-[24px] border border-[#E5E7EB] p-7 hover:shadow-lg transition-all">
                    <div class="shrink-0 h-14 w-14 rounded-2xl bg-[#1AAD94]/10 text-[#1AAD94] flex items-center justify-center">
                        <iconify-icon icon="{{ $benefit[0] }}" class="text-2xl"></iconify-icon>
                    </div>
                    <div>
                        <h3 class="text-[18px] font-extrabold text-[#073057] mb-2">{{ $benefit[1] }}</h3>
                        <p class="text-sm text-[#6B7280] leading-relaxed">{{ $benefit[2] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Available Contract Positions --}}
<section class="py-20 bg-[#F9FAFB]">
    <div class="container mx-auto px-6">
        <div class="flex flex-wrap items-end justify-between gap-4 mb-10">
            <div class="max-w-2xl">
                <div class="inline-flex rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-[#1AAD94] mb-4">Available Roles</div>
                <h2 class="text-3xl font-extrabold text-[#073057] mb-3">Available Contract Positions</h2>
                <p class="text-[#4B5563]">Browse our current contract staffing opportunities below and apply directly.</p>
            </div>
            <a href="{{ route('services.contract-staffing.jobs') }}" class="inline-flex items-center gap-2 text-[13px] font-bold uppercase tracking-[0.08em] text-[#1AAD94] hover:gap-3 transition-all">
                See all open roles
                <iconify-icon icon="lucide:arrow-right"></iconify-icon>
            </a>
        </div>

        @if (($featuredJobs ?? collect())->isNotEmpty())
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($featuredJobs as $featured)
                    <a href="{{ route('services.contract-staffing.detail', $featured->slug) }}" class="group flex flex-col rounded-[24px] border border-[#E5E7EB] bg-white p-7 hover:shadow-xl hover:-translate-y-1 transition-all">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#1AAD94]/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-[#1AAD94]">
                                <iconify-icon icon="lucide:briefcase"></iconify-icon>
                                Contract
                            </span>
                            @if ($featured->is_urgent)
                                <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-red-600">Urgent</span>
                            @endif
                        </div>
                        <h3 class="text-[18px] font-extrabold text-[#073057] mb-2">{{ $featured->title }}</h3>
                        <div class="text-sm text-[#6B7280] mb-4 flex flex-wrap gap-x-3 gap-y-1">
                            @if ($featured->location || $featured->address)
                                <span class="inline-flex items-center gap-1"><iconify-icon icon="lucide:map-pin"></iconify-icon> {{ $featured->location?->name ?? $featured->address }}</span>
                            @endif
                            @if ($featured->hours)
                                <span class="inline-flex items-center gap-1"><iconify-icon icon="lucide:clock"></iconify-icon> {{ $featured->hours }}</span>
                            @endif
                        </div>
                        <p class="text-sm text-[#6B7280] line-clamp-3 mb-5">{{ \Illuminate\Support\Str::limit(strip_tags((string) $featured->description), 140) }}</p>
                        <span class="mt-auto inline-flex items-center gap-2 text-[12px] font-bold uppercase tracking-[0.08em] text-[#1AAD94] group-hover:gap-3 transition-all">
                            View role
                            <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                        </span>
                    </a>
                @endforeach
            </div>
        @else
            <div class="rounded-[24px] border border-dashed border-[#E5E7EB] bg-white p-12 text-center">
                <iconify-icon icon="lucide:briefcase" class="text-5xl text-[#073057]/30 mb-4"></iconify-icon>
                <p class="text-[#6B7280] mb-6">No contract positions are open right now. Check back soon or get in touch to register your interest.</p>
                <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all">
                    Register Your Interest
                    <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                </a>
            </div>
        @endif
    </div>
</section>

{{-- CTA --}}
<section class="py-20 bg-[#073057]">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Looking to hire contract staff?</h2>
        <p class="text-white/70 mb-8 max-w-xl mx-auto">Tell us your project needs — Jose Ocean Jobs will match qualified candidates, manage compliance, and have them on-site quickly.</p>
        <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-bold rounded-xl transition-all shadow-lg">
            Contact Jose Ocean Jobs
            <iconify-icon icon="lucide:arrow-right"></iconify-icon>
        </a>
    </div>
</section>

@endsection
