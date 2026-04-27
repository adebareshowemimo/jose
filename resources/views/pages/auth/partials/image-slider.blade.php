@php
    use App\Support\JclProfileContent;

    $jclImages = JclProfileContent::images();
    $slides = [
        [
            'image' => $jclImages['deck_officer'] ?? '',
            'eyebrow' => 'Deck & Navigation Careers',
            'title' => 'Chart your next role on leading vessels worldwide.',
            'description' => 'From bridge officers to watchkeeping professionals, discover sea-going opportunities that reward certification, leadership, and readiness.',
        ],
        [
            'image' => $jclImages['offshore_vessel'] ?? '',
            'eyebrow' => 'Offshore & Marine Operations',
            'title' => 'Step into high-demand offshore and support vessel jobs.',
            'description' => 'Explore careers across offshore support, marine logistics, and energy operations with employers seeking proven maritime talent.',
        ],
        [
            'image' => $jclImages['container_port'] ?? '',
            'eyebrow' => 'Port, Cargo & Logistics Roles',
            'title' => 'Build a marine career that moves global trade forward.',
            'description' => 'Find roles in port operations, cargo handling, marine logistics, and shore-based maritime management connected to real hiring demand.',
        ],
        [
            'image' => $jclImages['auth_bg'] ?? '',
            'eyebrow' => 'Join the JCL Community',
            'title' => 'Register or sign in to access your career dashboard.',
            'description' => 'Manage your applications, build your maritime profile, and stay connected with opportunities that match your skills and certifications.',
        ],
    ];
@endphp

<div
    x-data="{ active: 0, total: {{ count($slides) }} }"
    x-init="setInterval(() => { active = (active + 1) % total }, 5500)"
    class="relative h-full w-full overflow-hidden"
>
    @foreach ($slides as $index => $slide)
        <div
            x-show="active === {{ $index }}"
            x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 scale-105"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-500"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0"
        >
            <div
                class="absolute inset-0 bg-cover bg-center"
                style="background-image: linear-gradient(145deg, rgba(6, 32, 56, 0.35), rgba(6, 32, 56, 0.72)), url('{{ $slide['image'] }}');"
            ></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#041d36] via-[#073057]/70 to-[#1AAD94]/25"></div>

            <div class="relative z-10 flex h-full items-end p-8 xl:p-14">
                <div class="max-w-xl text-white">
                    <div class="mb-4 inline-flex items-center rounded-full border border-white/20 bg-white/10 px-4 py-2 text-[11px] font-bold uppercase tracking-[0.22em] text-white/85 backdrop-blur-sm">
                        {{ $slide['eyebrow'] }}
                    </div>
                    <h2 class="mb-4 max-w-lg text-3xl font-extrabold leading-tight text-white xl:text-5xl">
                        {{ $slide['title'] }}
                    </h2>
                    <p class="max-w-lg text-sm leading-7 text-white/80 xl:text-base">
                        {{ $slide['description'] }}
                    </p>
                </div>
            </div>
        </div>
    @endforeach

    <div class="absolute bottom-8 left-8 z-20 flex items-center gap-3 xl:left-14">
        @foreach ($slides as $index => $slide)
            <button
                type="button"
                @click="active = {{ $index }}"
                :class="active === {{ $index }} ? 'w-10 bg-white' : 'w-3 bg-white/45 hover:bg-white/70'"
                class="h-3 rounded-full transition-all duration-300"
                aria-label="Show slide {{ $index + 1 }}"
            ></button>
        @endforeach
    </div>
</div>
