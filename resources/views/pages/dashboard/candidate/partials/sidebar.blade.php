{{-- User Card --}}
<div class="flex items-center gap-3 p-3 mb-4 bg-[#F9FAFB] rounded-xl">
    <div class="w-12 h-12 bg-gradient-to-br from-[#073057] to-[#1AAD94] rounded-full flex items-center justify-center text-white font-bold text-lg">
        {{ substr(auth()->user()->name ?? 'J', 0, 1) }}
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-[#073057] truncate">{{ auth()->user()->name ?? 'John Doe' }}</p>
        <p class="text-xs text-[#6B7280]">Candidate</p>
    </div>
</div>

@php
$currentRoute = request()->route()->getName();
$chatUnreadCount = 0;
$appliedJobsCount = 0;
$jobAlertsCount = 0;
$savedJobsCount = 0;
$cvCount = 0;
$candidateId = auth()->user()->candidate?->id;

if (\Illuminate\Support\Facades\Schema::hasTable('chat_messages') && \Illuminate\Support\Facades\Schema::hasTable('chat_conversations')) {
    if ($candidateId) {
        $chatUnreadCount = \App\Models\ChatMessage::whereNull('read_at')
            ->where('sender_user_id', '!=', auth()->id())
            ->whereHas('conversation', function ($query) use ($candidateId) {
                $query->where('candidate_id', $candidateId);
            })
            ->count();
    }
}

if ($candidateId && \Illuminate\Support\Facades\Schema::hasTable('job_applications')) {
    $appliedJobsCount = \App\Models\JobApplication::where('candidate_id', $candidateId)->count();
}

if (\Illuminate\Support\Facades\Schema::hasTable('job_alerts')) {
    $jobAlertsCount = \App\Models\JobAlert::where('user_id', auth()->id())
        ->where('is_active', true)
        ->count();
}

if (\Illuminate\Support\Facades\Schema::hasTable('wishlists')) {
    $savedJobsCount = \App\Models\Wishlist::where('user_id', auth()->id())
        ->where('wishlistable_type', \App\Models\JobListing::class)
        ->count();
}

if ($candidateId && \Illuminate\Support\Facades\Schema::hasTable('resumes')) {
    $cvCount = \App\Models\Resume::where('candidate_id', $candidateId)->count();
}

$navItems = [
    ['route' => 'user.dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>', 'label' => 'Dashboard'],
    ['route' => 'user.candidate.profile', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>', 'label' => 'My Profile'],
    ['route' => 'user.resume-builder', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>', 'label' => 'My Resume'],
    ['route' => 'user.applied-jobs', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>', 'label' => 'Applied Jobs', 'badge' => $appliedJobsCount],
    ['route' => 'user.job-alerts', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>', 'label' => 'Job Alerts', 'badge' => $jobAlertsCount],
    ['route' => 'user.bookmark', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>', 'label' => 'Saved Jobs', 'badge' => $savedJobsCount],
    ['route' => 'user.cv-manager', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>', 'label' => 'CV Manager', 'badge' => $cvCount],
    ['route' => 'user.chat', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>', 'label' => 'Messages', 'badge' => $chatUnreadCount],
];
@endphp

{{-- Navigation Links --}}
@foreach($navItems as $item)
<a href="{{ route($item['route']) }}" 
   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
          {{ $currentRoute === $item['route'] ? 'bg-[#1AAD94]/10 text-[#1AAD94]' : 'text-[#4B5563] hover:bg-[#F3F4F6]' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
    {{ $item['label'] }}
    @if(!empty($item['badge']))
    <span class="ml-auto bg-[#1AAD94] text-white text-xs font-medium px-2 py-0.5 rounded-full">{{ $item['badge'] }}</span>
    @endif
</a>
@endforeach

<div class="pt-4 mt-4 border-t border-[#E5E7EB]">
    <p class="px-3 mb-2 text-xs font-semibold text-[#9CA3AF] uppercase tracking-wider">Account</p>
    <a href="{{ route('user.change-password') }}" 
       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition
              {{ $currentRoute === 'user.change-password' ? 'bg-[#1AAD94]/10 text-[#1AAD94]' : 'text-[#4B5563] hover:bg-[#F3F4F6]' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        Change Password
    </a>
    <form action="{{ route('auth.logout') }}" method="POST" class="w-full">
        @csrf
        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg text-red-600 hover:bg-red-50 transition cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Logout
        </button>
    </form>
</div>

{{-- Profile Completion --}}
<div class="mt-6 p-4 bg-gradient-to-br from-[#073057] to-[#0a4275] rounded-xl text-white">
    <p class="text-sm font-medium mb-2">Profile Completion</p>
    <div class="flex items-center gap-3 mb-2">
        <div class="flex-1 h-2 bg-white/20 rounded-full overflow-hidden">
            <div class="h-full bg-[#1AAD94] rounded-full transition-all" style="width: 65%"></div>
        </div>
        <span class="text-sm font-bold">65%</span>
    </div>
    <p class="text-xs text-white/70">Complete your profile to get better job matches</p>
</div>
