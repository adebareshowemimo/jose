@extends('admin.layouts.app')

@section('title', 'Boost Packages')
@section('page-title', 'Boost Packages')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#0A1929]">Boost Packages</h1>
            <p class="text-sm text-gray-500 mt-1">The tiers candidates can buy to feature their profile. Prices and durations here drive the public boost page.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white border border-gray-200 text-xs text-gray-500">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                <span class="font-semibold text-gray-700">{{ $packages->where('is_active', true)->count() }}</span> active
            </span>
            <a href="{{ route('admin.boosts.packages.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-lg shadow transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Add Package
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr class="text-left text-xs uppercase tracking-wider text-gray-500">
                        <th class="px-5 py-3 font-semibold">Package</th>
                        <th class="px-5 py-3 font-semibold">Duration</th>
                        <th class="px-5 py-3 font-semibold">Price</th>
                        <th class="px-5 py-3 font-semibold">Perks</th>
                        <th class="px-5 py-3 font-semibold">Sold</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($packages as $package)
                        <tr class="hover:bg-gray-50/60">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-[#0A1929]">{{ $package->label }}</p>
                                @if ($package->tagline)
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $package->tagline }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-gray-700">{{ $package->days }} days</td>
                            <td class="px-5 py-4 font-semibold text-[#0A1929]">{{ money($package->price) }}</td>
                            <td class="px-5 py-4">
                                @forelse (array_keys(array_filter($package->perks ?? [])) as $perk)
                                    <span class="inline-flex items-center px-2 py-0.5 mr-1 mb-1 rounded-md bg-gray-100 text-gray-600 text-[11px] font-medium">
                                        {{ $perkOptions[$perk] ?? $perk }}
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-400">—</span>
                                @endforelse
                            </td>
                            <td class="px-5 py-4 text-gray-700">{{ $package->boosts_count }}</td>
                            <td class="px-5 py-4">
                                @if ($package->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-700 text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Hidden
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.boosts.packages.edit', $package) }}" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">Edit</a>
                                    <form method="POST" action="{{ route('admin.boosts.packages.destroy', $package) }}"
                                          onsubmit="return confirm('{{ $package->boosts_count > 0 ? 'This package has been sold before, so it will be hidden rather than deleted. Continue?' : 'Delete this package permanently?' }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg border border-red-200 text-xs font-semibold text-red-600 hover:bg-red-50 transition">
                                            {{ $package->boosts_count > 0 ? 'Hide' : 'Delete' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-500">
                                No boost packages yet. Candidates cannot buy a boost until at least one active package exists.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <p class="mt-4 text-xs text-gray-500">
        Prices are shown in the site currency ({{ \App\Support\Currency::default() }}). Changing a price here affects new purchases only — completed orders keep the amount they were sold at.
    </p>
@endsection
