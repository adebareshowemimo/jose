@extends('layouts.dashboard')

@section('title', 'Job Alerts')
@section('page-title', 'Job Alerts')

@section('sidebar-nav')
    @include('pages.dashboard.candidate.partials.sidebar')
@endsection

@section('content')
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#073057]">Job Alerts</h2>
            <p class="text-[#6B7280]">Get notified when new jobs match your criteria ({{ $activeCount ?? 0 }} active)</p>
        </div>
        <button onclick="document.getElementById('createAlertModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Create Alert
        </button>
    </div>

    {{-- Current Alerts --}}
    @if(isset($alerts) && $alerts->count() > 0)
    <div class="space-y-4">
        @foreach($alerts as $alert)
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-5">
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ $alert->is_active ? 'bg-[#1AAD94]/10' : 'bg-[#F3F4F6]' }}">
                        <svg class="w-6 h-6 {{ $alert->is_active ? 'text-[#1AAD94]' : 'text-[#9CA3AF]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="font-semibold text-[#073057]">{{ $alert->name }}</h4>
                        </div>
                        <div class="flex flex-wrap gap-2 text-sm text-[#6B7280]">
                            @if($alert->keywords)
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                {{ $alert->keywords }}
                            </span>
                            @endif
                            @if($alert->location)
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $alert->location->name }}
                            </span>
                            @endif
                            @if($alert->salary_min)
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                ${{ number_format($alert->salary_min) }}+
                            </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 mt-2 text-sm">
                            <span class="text-[#6B7280]">Frequency: <span class="text-[#073057] font-medium capitalize">{{ $alert->frequency }}</span></span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    {{-- Toggle Switch --}}
                    <form action="{{ route('user.alert.toggle', $alert) }}" method="POST">
                        @csrf
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" class="sr-only peer" {{ $alert->is_active ? 'checked' : '' }} onchange="this.form.submit()">
                            <div class="w-11 h-6 bg-[#E5E7EB] peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#1AAD94]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#1AAD94]"></div>
                        </label>
                    </form>
                    <form action="{{ route('user.alert.delete', $alert) }}" method="POST" onsubmit="return confirm('Delete this job alert?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-[#6B7280] hover:text-red-600 hover:bg-red-50 rounded-lg transition cursor-pointer" title="Delete">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach

        {{-- Pagination --}}
        @if($alerts->hasPages())
        <div class="mt-4">
            {{ $alerts->links() }}
        </div>
        @endif
    </div>
    @else
    {{-- Empty State --}}
    <div class="text-center py-16 bg-white rounded-xl border border-[#E5E7EB]">
        <div class="w-20 h-20 bg-[#F3F4F6] rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-[#9CA3AF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-[#073057] mb-2">No Job Alerts Yet</h3>
        <p class="text-[#6B7280] mb-6">Create alerts to get notified when jobs match your preferences</p>
        <button onclick="document.getElementById('createAlertModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Create Your First Alert
        </button>
    </div>
    @endif

    {{-- Create Alert Modal --}}
    <div id="createAlertModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('createAlertModal').classList.add('hidden')"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-[#073057]">Create Job Alert</h3>
                    <button onclick="document.getElementById('createAlertModal').classList.add('hidden')" class="text-[#6B7280] hover:text-[#073057] cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form action="{{ route('user.alert.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-[#073057] mb-2">Alert Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Chief Officer Jobs" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#073057] mb-2">Keywords</label>
                        <input type="text" name="keywords" placeholder="e.g. Chief Officer, Container, Tanker" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#073057] mb-2">Minimum Salary</label>
                        <select name="salary_min" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                            <option value="">Any</option>
                            <option value="5000">$5,000+</option>
                            <option value="8000">$8,000+</option>
                            <option value="10000">$10,000+</option>
                            <option value="15000">$15,000+</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#073057] mb-2">Email Frequency</label>
                        <div class="flex gap-3">
                            <label class="flex-1 flex items-center justify-center gap-2 p-3 border border-[#E5E7EB] rounded-xl cursor-pointer hover:border-[#1AAD94] has-[:checked]:border-[#1AAD94] has-[:checked]:bg-[#1AAD94]/5">
                                <input type="radio" name="frequency" value="daily" class="sr-only" checked />
                                <span class="text-sm font-medium">Daily</span>
                            </label>
                            <label class="flex-1 flex items-center justify-center gap-2 p-3 border border-[#E5E7EB] rounded-xl cursor-pointer hover:border-[#1AAD94] has-[:checked]:border-[#1AAD94] has-[:checked]:bg-[#1AAD94]/5">
                                <input type="radio" name="frequency" value="weekly" class="sr-only" />
                                <span class="text-sm font-medium">Weekly</span>
                            </label>
                            <label class="flex-1 flex items-center justify-center gap-2 p-3 border border-[#E5E7EB] rounded-xl cursor-pointer hover:border-[#1AAD94] has-[:checked]:border-[#1AAD94] has-[:checked]:bg-[#1AAD94]/5">
                                <input type="radio" name="frequency" value="instant" class="sr-only" />
                                <span class="text-sm font-medium">Instant</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" onclick="document.getElementById('createAlertModal').classList.add('hidden')" class="flex-1 px-4 py-3 border border-[#E5E7EB] text-[#4B5563] font-semibold rounded-xl hover:bg-[#F9FAFB] transition cursor-pointer">Cancel</button>
                        <button type="submit" class="flex-1 px-4 py-3 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition cursor-pointer">Create Alert</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
