@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalUsers) }}</p>
            <p class="text-sm text-gray-500">Total Users</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalCompanies) }}</p>
            <p class="text-sm text-gray-500">Companies</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalJobs) }}</p>
            <p class="text-sm text-gray-500">Job Listings <span class="text-green-600">({{ $activeJobs }} active)</span></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ money($totalRevenue, \App\Support\Currency::default()) }}</p>
            <p class="text-sm text-gray-500">Total Revenue</p>
        </div>
    </div>

    {{-- Secondary Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
            <p class="text-3xl font-bold text-[#073057]">{{ number_format($totalCandidates) }}</p>
            <p class="text-sm text-gray-500 mt-1">Candidates</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
            <p class="text-3xl font-bold text-[#073057]">{{ number_format($pendingJobs) }}</p>
            <p class="text-sm text-gray-500 mt-1">Pending Jobs</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
            <p class="text-3xl font-bold text-[#073057]">{{ number_format($activeSubscriptions) }}</p>
            <p class="text-sm text-gray-500 mt-1">Active Subscriptions</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        {{-- Revenue Chart --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Revenue (Last 6 Months)</h3>
            <div class="space-y-3">
                @php $maxRevenue = max(array_column($revenueChart, 'total')) ?: 1; @endphp
                @foreach($revenueChart as $item)
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-500 w-16 shrink-0">{{ $item['month'] }}</span>
                        <div class="flex-1 bg-gray-100 rounded-full h-6 relative overflow-hidden">
                            <div class="bg-[#1AAD94] h-full rounded-full transition-all" style="width: {{ ($item['total'] / $maxRevenue) * 100 }}%"></div>
                        </div>
                        <span class="text-xs font-medium text-gray-700 w-20 text-right">{{ money($item['total'], \App\Support\Currency::default()) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- User Registrations Chart --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-base font-semibold text-gray-900 mb-4">User Registrations (Last 6 Months)</h3>
            <div class="space-y-3">
                @php $maxUsers = max(array_column($userChart, 'count')) ?: 1; @endphp
                @foreach($userChart as $item)
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-500 w-16 shrink-0">{{ $item['month'] }}</span>
                        <div class="flex-1 bg-gray-100 rounded-full h-6 relative overflow-hidden">
                            <div class="bg-[#073057] h-full rounded-full transition-all" style="width: {{ ($item['count'] / $maxUsers) * 100 }}%"></div>
                        </div>
                        <span class="text-xs font-medium text-gray-700 w-10 text-right">{{ $item['count'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Recent Users --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900">Recent Users</h3>
                <a href="{{ route('admin.users') }}" class="text-sm text-[#1AAD94] hover:underline">View All</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentUsers as $user)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <div class="w-9 h-9 bg-gray-200 rounded-full flex items-center justify-center text-sm font-semibold text-gray-600">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full
                            {{ $user->role?->name === 'admin' ? 'bg-red-100 text-red-700' : ($user->role?->name === 'employer' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                            {{ ucfirst($user->role?->name ?? 'N/A') }}
                        </span>
                    </div>
                @empty
                    <p class="px-5 py-6 text-sm text-gray-400 text-center">No users yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900">Recent Orders</h3>
                <a href="{{ route('admin.orders') }}" class="text-sm text-[#1AAD94] hover:underline">View All</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentOrders as $order)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-500">{{ $order->user?->name ?? 'N/A' }}</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ money($order->total, $order->currency ?? 'USD') }}</span>
                        <span class="text-xs px-2 py-1 rounded-full
                            {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' : ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ ucfirst($order->status ?? 'N/A') }}
                        </span>
                    </div>
                @empty
                    <p class="px-5 py-6 text-sm text-gray-400 text-center">No orders yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
