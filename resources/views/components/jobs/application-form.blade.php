@php
    /** @var \App\Models\JobListing $job */
    $job = $job ?? null;
    $user = auth()->user();
    $candidate = $user?->candidate;
    $resumes = $candidate?->resumes()->orderByDesc('is_default')->orderByDesc('id')->get() ?? collect();
    $applyReady = $candidate ? $candidate->hasApplyReadyProfile() : false;
    $alreadyApplied = $candidate
        ? $candidate->applications()->where('job_listing_id', $job->id)->exists()
        : false;
@endphp

<div id="apply" class="scroll-mt-24 rounded-2xl border border-[#E5E7EB] bg-white p-6 shadow-sm">
    <h3 class="text-[20px] font-extrabold text-[#073057] mb-4">Apply for this role</h3>

    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    @guest
        <p class="text-sm text-[#6B7280] mb-4">Sign in to your candidate account to apply.</p>
        <a href="{{ route('auth.login') }}?intended={{ urlencode(url()->current()) }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1AAD94] rounded-lg text-white text-sm font-bold uppercase tracking-widest hover:brightness-110 shadow transition">
            Sign in to apply
            <iconify-icon icon="lucide:arrow-right"></iconify-icon>
        </a>
    @endguest

    @auth
        @if (! $candidate)
            <p class="text-sm text-[#6B7280] mb-4">You need a candidate profile before applying. Switch your account to a candidate role to continue.</p>
            <a href="{{ route('auth.complete-signup') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#073057] rounded-lg text-white text-sm font-bold uppercase tracking-widest hover:brightness-110 shadow transition">
                Complete candidate signup
            </a>
        @elseif ($alreadyApplied)
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                You have already applied to this role. We will contact you about next steps.
            </div>
        @else
            <form method="POST" action="{{ route('job.apply', $job) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                @if ($applyReady)
                    <div class="rounded-lg border border-[#1AAD94]/30 bg-[#1AAD94]/5 p-4 flex items-start gap-3">
                        <iconify-icon icon="lucide:badge-check" class="text-[#1AAD94] text-xl mt-0.5"></iconify-icon>
                        <div>
                            <p class="text-sm font-bold text-[#073057]">Apply with your profile</p>
                            <p class="text-xs text-[#6B7280] mt-0.5">
                                Your profile{{ $candidate->title ? ' — '.$candidate->title : '' }} (experience, education, skills@if (! empty($candidate->awards)), certifications@endif) will be sent with this application. A CV upload is optional.
                            </p>
                        </div>
                    </div>
                @endif

                @if ($resumes->isNotEmpty())
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-[#6B7280] mb-2 block">{{ $applyReady ? 'Attach a CV (optional)' : 'Select an existing CV' }}</label>
                        <div class="space-y-2">
                            @if ($applyReady)
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-[#1AAD94] cursor-pointer">
                                    <input type="radio" name="resume_id" value="" checked class="text-[#1AAD94] focus:ring-[#1AAD94]">
                                    <span class="text-sm text-[#073057] font-medium">Apply with my profile only</span>
                                    <span class="text-[10px] uppercase tracking-wider bg-[#1AAD94]/10 text-[#1AAD94] font-bold px-2 py-0.5 rounded-full">No CV</span>
                                </label>
                            @endif
                            @foreach ($resumes as $resume)
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-[#1AAD94] cursor-pointer">
                                    <input type="radio" name="resume_id" value="{{ $resume->id }}" {{ (! $applyReady && $resume->is_default) ? 'checked' : '' }} class="text-[#1AAD94] focus:ring-[#1AAD94]">
                                    <span class="text-sm text-[#073057] font-medium">{{ $resume->title }}</span>
                                    @if ($resume->is_default)
                                        <span class="text-[10px] uppercase tracking-wider bg-[#1AAD94]/10 text-[#1AAD94] font-bold px-2 py-0.5 rounded-full">Default</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-[#6B7280] mb-2 block">
                        @if ($resumes->isNotEmpty())
                            Or upload a new CV
                        @else
                            {{ $applyReady ? 'Upload a CV (optional)' : 'Upload your CV' }}
                        @endif
                        @if ($resumes->isEmpty() && ! $applyReady)<span class="text-red-500">*</span>@endif
                    </label>
                    <input type="file" name="resume" accept=".pdf,.doc,.docx" @required($resumes->isEmpty() && ! $applyReady) class="w-full text-sm text-[#073057] file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#1AAD94]/10 file:text-[#1AAD94] hover:file:bg-[#1AAD94]/20">
                    <p class="mt-1 text-xs text-gray-400">PDF, DOC, or DOCX · max 4 MB{{ $applyReady ? '' : ' · required to apply' }}</p>
                </div>

                @unless ($applyReady)
                    <p class="text-xs text-[#6B7280]">
                        <a href="{{ route('user.resume-builder') }}" class="text-[#1AAD94] font-semibold hover:underline">Complete your profile</a>
                        to apply without uploading a CV.
                    </p>
                @endunless

                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-[#6B7280] mb-2 block">Cover letter <span class="text-gray-400 normal-case font-normal">(optional)</span></label>
                    <textarea name="cover_letter" rows="5" placeholder="Tell us briefly why you're a fit..." class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">{{ old('cover_letter') }}</textarea>
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#1AAD94] rounded-lg text-white text-sm font-bold uppercase tracking-widest hover:brightness-110 shadow transition">
                    {{ $applyReady ? 'Apply with profile' : 'Submit application' }}
                    <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                </button>
            </form>
        @endif
    @endauth
</div>
