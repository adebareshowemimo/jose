<footer class="bg-[#073057] text-white pt-24 pb-12">
    <div class="container mx-auto px-6">
        <div class="grid gap-14 lg:grid-cols-12 mb-18 py-12">
            <div class="lg:col-span-4">
                <div class="flex items-center gap-4 mb-8">
                    <img src="{{ asset('images/dark_logo.png') }}" alt="JCL Logo" class="h-12 w-auto" />
                </div>
                <p class="max-w-md text-[15px] leading-relaxed text-white/65 mb-8">
                    <p class="max-w-md text-[15px] leading-relaxed text-white/65 mb-8">
                    Jose Consulting Limited helps individuals and organizations strengthen employability, workforce readiness, and global opportunity pathways across the maritime/Logistics and energy sectors.
                </p>
            </div>

            <div class="lg:col-span-2">
                <h4 class="mb-6 text-[13px] font-bold uppercase tracking-[0.18em] text-[#1AAD94]">Explore</h4>
                <ul class="space-y-3 text-white/65">
                    <li><a href="{{ route('about.index') }}" class="hover:text-white transition-colors">About JCL</a></li>
                    <li><a href="{{ route('leadership.index') }}" class="hover:text-white transition-colors">Leadership & Experts</a></li>
                    <li><a href="{{ route('partnerships.index') }}" class="hover:text-white transition-colors">Partnerships & Expertise</a></li>
                    <li><a href="{{ route('training.index') }}" class="hover:text-white transition-colors">Training</a></li>
                    <li><a href="{{ route('events.index') }}" class="hover:text-white transition-colors">Events</a></li>
                    <li><a href="{{ route('contact.index') }}" class="hover:text-white transition-colors">Contact JCL</a></li>
                </ul>
            </div>

            <div class="lg:col-span-2">
                <h4 class="mb-6 text-[13px] font-bold uppercase tracking-[0.18em] text-[#1AAD94]">Platform</h4>
                <ul class="space-y-3 text-white/65">
                    <li><a href="{{ route('job.index') }}" class="hover:text-white transition-colors">Browse Jobs</a></li>
                    <li><a href="{{ route('career.index') }}" class="hover:text-white transition-colors">Browse Career</a></li>
                    <li><a href="{{ route('services.index') }}" class="hover:text-white transition-colors">Browse Services</a></li>
                    <li><a href="{{ route('news.index') }}" class="hover:text-white transition-colors">News & Insights</a></li>
                    <li><a href="{{ route('companies.index') }}" class="hover:text-white transition-colors">Industry Directory</a></li>
                    <li><a href="{{ route('auth.register') }}" class="hover:text-white transition-colors">Start Your Pathway</a></li>
                </ul>
            </div>

            <div class="lg:col-span-4">
                <h4 class="mb-6 text-[13px] font-bold uppercase tracking-[0.18em] text-[#1AAD94]">How we help</h4>
                <div class="space-y-4">
                    <div class="rounded-[14px] border border-white/10 bg-white/5 p-5">
                        <h5 class="font-bold text-white mb-1">Talent pathway</h5>
                        <p class="text-sm leading-relaxed text-white/60">Candidates can register, browse opportunities, and connect their skills to future-ready roles.</p>
                    </div>
                    <div class="rounded-[14px] border border-white/10 bg-white/5 p-5">
                        <h5 class="font-bold text-white mb-1">Training &amp; consulting enquiries</h5>
                        <p class="text-sm leading-relaxed text-white/60">Use the contact flow to discuss workforce development programs, technical training, or operational consulting support.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-10 border-t border-white/10 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <p class="text-[12px] font-medium tracking-[0.1em] uppercase text-white/25">© {{ date('Y') }} Jose Consulting Limited. All rights reserved.</p>
            <div class="flex flex-wrap items-center gap-6 text-white/30 text-[11px] font-bold uppercase tracking-[0.12em]">
                <div class="flex items-center gap-2"><iconify-icon icon="lucide:globe" class="text-lg"></iconify-icon><span>Global collaboration</span></div>
                <div class="flex items-center gap-2"><iconify-icon icon="lucide:badge-check" class="text-lg"></iconify-icon><span>Industry-ready capability</span></div>
                <div class="flex items-center gap-2"><iconify-icon icon="lucide:briefcase-business" class="text-lg"></iconify-icon><span>Career pathways</span></div>
            </div>
        </div>
    </div>
</footer>
