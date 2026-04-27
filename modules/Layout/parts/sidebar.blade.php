<aside class="w-full lg:w-72 bg-[#073057] text-white rounded-[12px] p-6 lg:min-h-[calc(100vh-8rem)]">
    <h3 class="text-sm font-bold uppercase tracking-wider text-[#1AAD94] mb-5">Dashboard</h3>
    <nav class="space-y-2">
        <a href="{{ route('user.dashboard') }}" class="block px-4 py-3 rounded-[8px] bg-white/5 hover:bg-white/10 transition">Overview</a>
        <a href="{{ route('user.candidate.profile') }}" class="block px-4 py-3 rounded-[8px] hover:bg-white/10 transition">Profile</a>
        <a href="{{ route('user.applied-jobs') }}" class="block px-4 py-3 rounded-[8px] hover:bg-white/10 transition">Applied Jobs</a>
        <a href="{{ route('user.cv-manager') }}" class="block px-4 py-3 rounded-[8px] hover:bg-white/10 transition">CV Manager</a>
        <a href="{{ route('user.chat') }}" class="block px-4 py-3 rounded-[8px] hover:bg-white/10 transition">Messages</a>
    </nav>
</aside>
