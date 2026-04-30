@extends('layouts.app')

@section('title', $pageTitle . ' — Jose Consulting Limited')
@section('meta_description', $pageDescription)

@section('content')

{{-- HERO --}}
<section class="relative h-[420px] flex items-center overflow-hidden bg-[#073057]">
    <img src="{{ ($jclImages ?? [])['training_hero'] ?? '' }}"
         alt="Training"
         class="absolute inset-0 w-full h-full object-cover opacity-30" loading="eager" />
    <div class="absolute inset-0 bg-gradient-to-r from-[#073057] via-[#073057]/85 to-transparent"></div>
    <div class="container mx-auto px-6 relative z-10">
        <x-ui.breadcrumbs :items="$breadcrumbs ?? []" class="mb-6 text-[11px] font-bold uppercase tracking-[0.15em] text-[#7DE1D1]" />
        <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-2 text-[11px] font-bold uppercase tracking-[0.18em] text-[#7DE1D1] mb-6 backdrop-blur-sm">
            <iconify-icon icon="lucide:graduation-cap" class="text-sm"></iconify-icon>
            <span>Training</span>
        </div>
        <h1 class="text-[48px] md:text-[60px] font-extrabold text-white leading-none mb-4">Professional Training Programs</h1>
        <p class="mt-2 max-w-xl text-lg text-white/70">Industry-aligned programs for maritime and energy professionals — from STCW to leadership and soft skills.</p>
    </div>
</section>

{{-- TRAINING STREAMS --}}
<section class="bg-white py-20">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <div class="inline-flex rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#1AAD94] mb-4">Training Streams</div>
            <h2 class="text-[38px] font-extrabold text-[#073057] leading-tight">Practical training designed for industry readiness</h2>
        </div>
        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            {{-- Soft Skills --}}
            <div class="group flex flex-col rounded-[28px] border border-[#E5E7EB] bg-white p-8 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#1AAD94]/10 text-[#1AAD94] group-hover:bg-[#1AAD94] group-hover:text-white transition-colors">
                    <iconify-icon icon="lucide:users" class="text-2xl"></iconify-icon>
                </div>
                <h3 class="text-[22px] font-extrabold text-[#073057] mb-3">Soft Skills</h3>
                <p class="text-[#6B7280] text-sm leading-relaxed flex-grow mb-6">Communication, leadership, teamwork, and workplace effectiveness programs for maritime and energy professionals at all levels.</p>
                <a href="{{ route('services.training.soft') }}"
                   class="inline-flex items-center gap-2 text-[13px] font-bold uppercase tracking-[0.08em] text-[#1AAD94] hover:gap-3 transition-all">
                    View Programs
                    <iconify-icon icon="lucide:arrow-right" class="text-sm"></iconify-icon>
                </a>
            </div>
            {{-- Technical --}}
            <div class="group flex flex-col rounded-[28px] border border-[#E5E7EB] bg-white p-8 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#073057]/10 text-[#073057] group-hover:bg-[#073057] group-hover:text-white transition-colors">
                    <iconify-icon icon="lucide:wrench" class="text-2xl"></iconify-icon>
                </div>
                <h3 class="text-[22px] font-extrabold text-[#073057] mb-3">Technical &amp; Non-Technical Skills</h3>
                <p class="text-[#6B7280] text-sm leading-relaxed flex-grow mb-6">Industry-aligned technical training including STCW, NEBOSH, offshore safety, port operations, and energy sector readiness programs.</p>
                <a href="{{ route('services.training.technical') }}"
                   class="inline-flex items-center gap-2 text-[13px] font-bold uppercase tracking-[0.08em] text-[#073057] hover:gap-3 transition-all">
                    View Programs
                    <iconify-icon icon="lucide:arrow-right" class="text-sm"></iconify-icon>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ALL TRAINING PROGRAMS --}}
<section class="bg-[#F9FAFB] py-24">
    <div class="container mx-auto px-6">
        <div class="text-center mb-14">
            <div class="inline-flex rounded-full bg-[#1AAD94]/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#1AAD94] mb-4">Programs</div>
            <h2 class="text-[38px] font-extrabold text-[#073057] leading-tight">All Training Programs</h2>
            <p class="mt-3 max-w-2xl mx-auto text-lg text-[#6B7280]">Courses designed to international standards, delivered in-person and hybrid.</p>
        </div>

        @if (! empty($dbPrograms) && $dbPrograms->isNotEmpty())
            {{-- DB-backed programs (real TrainingProgram records — admin-managed) --}}
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($dbPrograms as $program)
                    <article class="flex flex-col rounded-[24px] border border-[#E5E7EB] bg-white shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                        @if ($program->image_url)
                            <a href="{{ route('training.show', $program->slug) }}" class="block aspect-[16/10] bg-gray-100 overflow-hidden">
                                <img src="{{ $program->image_url }}" alt="{{ $program->title }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" loading="lazy">
                            </a>
                        @endif

                        <div class="p-7 flex-1 flex flex-col">
                            @if (! $program->image_url)
                                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#073057]/8 text-[#073057]">
                                    <iconify-icon icon="lucide:graduation-cap" class="text-xl"></iconify-icon>
                                </div>
                            @endif

                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                @if ($program->category)
                                    <span class="inline-block rounded-full bg-[#1AAD94]/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-[#1AAD94]">{{ $program->category }}</span>
                                @endif
                                @if ($program->is_featured)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold uppercase tracking-wider">
                                        <iconify-icon icon="lucide:star" class="text-xs"></iconify-icon>
                                        Featured
                                    </span>
                                @endif
                            </div>

                            <h4 class="text-[17px] font-extrabold text-[#073057] mb-2 leading-snug">
                                <a href="{{ route('training.show', $program->slug) }}" class="hover:text-[#1AAD94] transition-colors">{{ $program->title }}</a>
                            </h4>

                            @if ($program->short_description)
                                <p class="text-[#6B7280] text-sm leading-relaxed flex-grow mb-5 line-clamp-3">{{ $program->short_description }}</p>
                            @endif

                            <div class="flex items-center flex-wrap gap-4 text-[12px] text-[#9CA3AF] font-semibold border-t border-[#F3F4F6] pt-4 mb-5">
                                @if ($program->duration)
                                    <span class="flex items-center gap-1.5"><iconify-icon icon="lucide:clock" class="text-[#1AAD94]"></iconify-icon> {{ $program->duration }}</span>
                                @endif
                                @if ($program->level)
                                    <span class="flex items-center gap-1.5"><iconify-icon icon="lucide:bar-chart-2" class="text-[#1AAD94]"></iconify-icon> {{ $program->level }}</span>
                                @endif
                                @if ($program->starts_at)
                                    <span class="flex items-center gap-1.5"><iconify-icon icon="lucide:calendar" class="text-[#1AAD94]"></iconify-icon> {{ $program->starts_at->format('M d') }}</span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <span class="text-base font-extrabold text-[#073057]">
                                    @if ($program->isFree())
                                        <span class="text-[#1AAD94]">Free</span>
                                    @else
                                        {{ money($program->price, $program->currency ?? 'USD') }}
                                    @endif
                                </span>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('training.show', $program->slug) }}"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-2 border-2 border-[#073057] hover:bg-[#073057] hover:text-white text-[#073057] text-[11px] font-bold uppercase tracking-wider rounded-lg transition">
                                        <iconify-icon icon="lucide:eye" class="text-xs"></iconify-icon>
                                        View
                                    </a>
                                    @auth
                                        <form method="POST" action="{{ route('training.enrol', $program) }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#1AAD94] hover:brightness-110 text-white text-[11px] font-bold uppercase tracking-wider rounded-lg transition shadow">
                                                <iconify-icon icon="lucide:graduation-cap" class="text-xs"></iconify-icon>
                                                {{ $program->isFree() ? 'Join' : 'Apply' }}
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('auth.login', ['redirect' => route('training.show', $program->slug)]) }}"
                                           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#1AAD94] hover:brightness-110 text-white text-[11px] font-bold uppercase tracking-wider rounded-lg transition shadow">
                                            <iconify-icon icon="lucide:graduation-cap" class="text-xs"></iconify-icon>
                                            {{ $program->isFree() ? 'Join' : 'Apply' }}
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            {{-- Static fallback — kept until admin creates real TrainingProgram records via /admin/training. --}}
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($programs as $prog)
                <div class="flex flex-col rounded-[24px] border border-[#E5E7EB] bg-white p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#073057]/8 text-[#073057]">
                        <iconify-icon icon="{{ $prog['icon'] ?? 'lucide:graduation-cap' }}" class="text-xl"></iconify-icon>
                    </div>
                    <span class="inline-block mb-3 rounded-full bg-[#1AAD94]/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-[#1AAD94]">{{ $prog['category'] }}</span>
                    <h4 class="text-[17px] font-extrabold text-[#073057] mb-2 leading-snug">{{ $prog['title'] }}</h4>
                    <p class="text-[#6B7280] text-sm leading-relaxed flex-grow mb-5">{{ $prog['description'] }}</p>
                    <div class="flex items-center gap-4 text-[12px] text-[#9CA3AF] font-semibold border-t border-[#F3F4F6] pt-4 mb-5">
                        <span class="flex items-center gap-1.5"><iconify-icon icon="lucide:clock" class="text-[#1AAD94]"></iconify-icon> {{ $prog['duration'] }}</span>
                        <span class="flex items-center gap-1.5"><iconify-icon icon="lucide:monitor" class="text-[#1AAD94]"></iconify-icon> {{ $prog['mode'] }}</span>
                    </div>
                    <div class="flex items-center justify-end gap-2 mt-auto">
                        <a href="{{ route('training.index') }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 border-2 border-[#073057] hover:bg-[#073057] hover:text-white text-[#073057] text-[11px] font-bold uppercase tracking-wider rounded-lg transition">
                            <iconify-icon icon="lucide:eye" class="text-xs"></iconify-icon>
                            View
                        </a>
                        <a href="{{ route('contact.index') }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#1AAD94] hover:brightness-110 text-white text-[11px] font-bold uppercase tracking-wider rounded-lg transition shadow">
                            <iconify-icon icon="lucide:graduation-cap" class="text-xs"></iconify-icon>
                            Apply
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- CTA --}}
<section class="bg-[#073057] py-24">
    <div class="container mx-auto px-6 text-center">
        <div class="max-w-2xl mx-auto">
            <h2 class="text-[38px] font-extrabold text-white mb-6">Enquire about a training program</h2>
            <p class="text-lg text-white/70 mb-10">Our training team will help you find the right program for your needs and schedule.</p>
            <a href="{{ route('contact.index') }}"
               class="inline-flex items-center gap-2 px-8 py-4 bg-[#1AAD94] rounded-[10px] text-white text-[14px] font-bold uppercase tracking-[0.1em] hover:brightness-110 shadow-lg transition-all">
                Get in Touch
                <iconify-icon icon="lucide:arrow-right"></iconify-icon>
            </a>
        </div>
    </div>
</section>

@endsection
