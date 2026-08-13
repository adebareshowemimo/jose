@extends('admin.layouts.app')

@section('title', $package->exists ? 'Edit Boost Package' : 'New Boost Package')
@section('page-title', 'Boost Packages')

@section('content')
    @php
        $action = $package->exists
            ? route('admin.boosts.packages.update', $package)
            : route('admin.boosts.packages.store');
        $selectedPerks = array_keys(array_filter($package->perks ?? []));
    @endphp

    <div class="mb-6">
        <a href="{{ route('admin.boosts.packages.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to packages</a>
        <h1 class="text-2xl font-bold text-[#0A1929] mt-2">{{ $package->exists ? 'Edit package' : 'New package' }}</h1>
        <p class="text-sm text-gray-500 mt-1">These values appear on the candidate boost page immediately after saving.</p>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold mb-1">Please fix the highlighted fields.</p>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $action }}" class="bg-white border border-gray-200 rounded-xl p-6 space-y-5 max-w-3xl">
        @csrf
        @if ($package->exists)
            @method('PUT')
        @endif

        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label for="label" class="block text-sm font-semibold text-gray-700 mb-1.5">Label</label>
                <input id="label" type="text" name="label" value="{{ old('label', $package->label) }}" required
                       class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">
                <p class="text-xs text-gray-500 mt-1">Shown as the tier name, e.g. "Standard".</p>
            </div>

            <div>
                <label for="tagline" class="block text-sm font-semibold text-gray-700 mb-1.5">Tagline</label>
                <input id="tagline" type="text" name="tagline" value="{{ old('tagline', $package->tagline) }}"
                       class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">
                <p class="text-xs text-gray-500 mt-1">Optional one-line pitch under the tier name.</p>
            </div>

            <div>
                <label for="days" class="block text-sm font-semibold text-gray-700 mb-1.5">Duration (days)</label>
                <input id="days" type="number" name="days" min="1" max="3650" value="{{ old('days', $package->days) }}" required
                       class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">
                <p class="text-xs text-gray-500 mt-1">How long the profile stays featured.</p>
            </div>

            <div>
                <label for="price" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Price ({{ \App\Support\Currency::default() }})
                </label>
                <input id="price" type="number" name="price" step="0.01" min="0" value="{{ old('price', $package->price) }}" required
                       class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">
                <p class="text-xs text-gray-500 mt-1">A package priced at 0 cannot be bought.</p>
            </div>

            <div>
                <label for="sort_order" class="block text-sm font-semibold text-gray-700 mb-1.5">Sort order</label>
                <input id="sort_order" type="number" name="sort_order" min="0" max="9999" value="{{ old('sort_order', $package->sort_order) }}"
                       class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">
                <p class="text-xs text-gray-500 mt-1">Lower numbers appear first.</p>
            </div>

            <div class="flex items-start pt-7">
                <label class="inline-flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
                    <span class="text-sm font-semibold text-gray-700">Active</span>
                </label>
            </div>
        </div>

        <div class="pt-2 border-t border-gray-100">
            <p class="block text-sm font-semibold text-gray-700 mb-2 mt-4">Perks</p>
            <div class="space-y-2">
                @foreach ($perkOptions as $key => $label)
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="perks[]" value="{{ $key }}"
                               {{ in_array($key, old('perks', $selectedPerks), true) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
                        <span class="text-sm text-gray-700">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <p class="text-xs text-gray-500 mt-2">Only perks the site actually applies are listed here.</p>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
            <button type="submit" class="px-5 py-2.5 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-lg shadow transition">
                {{ $package->exists ? 'Save changes' : 'Create package' }}
            </button>
            <a href="{{ route('admin.boosts.packages.index') }}" class="px-5 py-2.5 border border-gray-200 text-sm font-semibold text-gray-600 rounded-lg hover:bg-gray-50 transition">Cancel</a>
        </div>
    </form>
@endsection
