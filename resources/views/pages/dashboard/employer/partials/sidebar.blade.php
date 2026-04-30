{{-- Company Card --}}
<div class="flex items-center gap-3 p-3 mb-4 bg-[#F9FAFB] rounded-xl">
    <div class="w-12 h-12 bg-gradient-to-br from-[#1AAD94] to-[#073057] rounded-xl flex items-center justify-center text-white font-bold text-lg">
        {{ substr(auth()->user()->company->name ?? 'C', 0, 1) }}
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-[#073057] truncate">{{ auth()->user()->company->name ?? 'Company Name' }}</p>
        <p class="text-xs text-[#6B7280]">Employer</p>
    </div>
</div>

@php
$currentRoute = request()->route()->getName();
$chatUnreadCount = 0;
$activeJobsCount = 0;
$applicantsCount = 0;
$shortlistedResumesCount = 0;
$hiringRequestsCount = 0;
$resumeAlertsCount = 0;
$companyId = auth()->user()->company?->id;

if (\Illuminate\Support\Facades\Schema::hasTable('chat_messages') && \Illuminate\Support\Facades\Schema::hasTable('chat_conversations')) {
    if ($companyId) {
        $chatUnreadCount = \App\Models\ChatMessage::whereNull('read_at')
            ->where('sender_user_id', '!=', auth()->id())
            ->whereHas('conversation', function ($query) use ($companyId) {
                $query->where('type', \App\Models\ChatConversation::TYPE_EMPLOYER_CANDIDATE)
                    ->where('company_id', $companyId);
            })
            ->count();
    }
}

if ($companyId && \Illuminate\Support\Facades\Schema::hasTable('job_listings')) {
    $activeJobsCount = \App\Models\JobListing::where('company_id', $companyId)
        ->where('status', 'active')
        ->count();
}

if ($companyId && \Illuminate\Support\Facades\Schema::hasTable('job_applications') && \Illuminate\Support\Facades\Schema::hasTable('job_listings')) {
    $applicantsCount = \App\Models\JobApplication::whereHas('jobListing', function ($query) use ($companyId) {
        $query->where('company_id', $companyId);
    })->count();
}

if (\Illuminate\Support\Facades\Schema::hasTable('wishlists')) {
    $shortlistedResumesCount = \App\Models\Wishlist::where('user_id', auth()->id())
        ->where('wishlistable_type', \App\Models\Candidate::class)
        ->count();
}

if ($companyId && \Illuminate\Support\Facades\Schema::hasTable('recruitment_requests')) {
    $hiringRequestsCount = \App\Models\RecruitmentRequest::where('company_id', $companyId)
        ->whereNotIn('status', ['completed', 'cancelled'])
        ->count();
}

if ($companyId && \Illuminate\Support\Facades\Schema::hasTable('job_listings')) {
    $resumeAlertsCount = \App\Models\JobListing::where('company_id', $companyId)
        ->where('status', 'active')
        ->count();
}

$navItems = [
    ['route' => 'employer.dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>', 'label' => 'Dashboard'],
    ['route' => 'employer.company.profile', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>', 'label' => 'Company Profile'],
    ['route' => 'employer.new-job', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>', 'label' => 'Post New Job'],
    ['route' => 'employer.manage-jobs', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>', 'label' => 'Manage Jobs', 'badge' => $activeJobsCount],
    ['route' => 'employer.applicants', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>', 'label' => 'All Applicants', 'badge' => $applicantsCount, 'badge_class' => 'bg-red-500'],
    ['route' => 'employer.resumes', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>', 'label' => 'Shortlisted Resumes', 'badge' => $shortlistedResumesCount],
    ['route' => 'employer.recruitment-requests.index', 'active_prefix' => 'employer.recruitment-requests', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 8a2 2 0 100-4 2 2 0 000 4zm0 2c-2 0-4 1-4 3v1h8v-1c0-2-2-3-4-3z"/>', 'label' => 'Hiring Services', 'badge' => $hiringRequestsCount],
    ['route' => 'employer.resume-alerts', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>', 'label' => 'Resume Alerts', 'badge' => $resumeAlertsCount],
    ['route' => 'employer.chat', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>', 'label' => 'Messages', 'badge' => $chatUnreadCount],
    ['route' => 'employer.payments', 'active_prefix' => 'employer.payments', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h2m4 0h4M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"/>', 'label' => 'Payments & Receipts'],
];
@endphp

{{-- Navigation Links --}}
@foreach($navItems as $item)
<a href="{{ route($item['route']) }}" 
   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
          @php $isActive = $currentRoute === $item['route'] || (! empty($item['active_prefix']) && str_starts_with((string) $currentRoute, $item['active_prefix'] . '.')); @endphp
          {{ $isActive ? 'bg-[#1AAD94]/10 text-[#1AAD94]' : 'text-[#4B5563] hover:bg-[#F3F4F6]' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
    {{ $item['label'] }}
    @if(!empty($item['badge']))
    <span class="ml-auto {{ $item['badge_class'] ?? 'bg-[#1AAD94]' }} text-white text-xs font-medium px-2 py-0.5 rounded-full">{{ $item['badge'] }}</span>
    @endif
</a>
@endforeach

<div class="pt-4 mt-4 border-t border-[#E5E7EB]">
    <p class="px-3 mb-2 text-xs font-semibold text-[#9CA3AF] uppercase tracking-wider">Account</p>
    <a href="{{ route('employer.change-password') }}" 
       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
              {{ $currentRoute === 'employer.change-password' ? 'bg-[#1AAD94]/10 text-[#1AAD94]' : 'text-[#4B5563] hover:bg-[#F3F4F6]' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        Change Password
    </a>
    <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg text-red-600 hover:bg-red-50 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        Logout
    </a>
</div>

