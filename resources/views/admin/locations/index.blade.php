@extends('admin.layouts.app')

@section('title', 'Countries')
@section('page-title', 'Countries')

@section('content')
<div class="grid gap-6 xl:grid-cols-[360px_1fr]">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="font-semibold text-gray-900 mb-4">Add Country</h2>
        <form method="POST" action="{{ route('admin.locations.store') }}" class="space-y-4">
            @csrf
            <input type="text" name="name" required placeholder="Nigeria" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
            <div class="grid grid-cols-2 gap-3">
                <input type="number" step="any" name="latitude" placeholder="Latitude" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <input type="number" step="any" name="longitude" placeholder="Longitude" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <input type="text" name="zipcode" placeholder="ISO/code (optional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-[#1AAD94]">
                Active
            </label>
            <button class="w-full px-4 py-2 bg-[#073057] text-white text-sm font-semibold rounded-lg">Create Country</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 font-medium text-gray-500">Country</th>
                    <th class="text-left px-5 py-3 font-medium text-gray-500">Coordinates</th>
                    <th class="text-left px-5 py-3 font-medium text-gray-500">Status</th>
                    <th class="text-right px-5 py-3 font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($locations as $location)
                    <tr>
                        <td class="px-5 py-3">
                            <form id="location-{{ $location->id }}" method="POST" action="{{ route('admin.locations.update', $location) }}" class="grid gap-2">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $location->name }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <input type="text" name="zipcode" value="{{ $location->zipcode }}" placeholder="ISO/code" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </form>
                        </td>
                        <td class="px-5 py-3">
                            <div class="grid gap-2">
                                <input type="number" step="any" name="latitude" value="{{ $location->latitude }}" form="location-{{ $location->id }}" placeholder="Latitude" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <input type="number" step="any" name="longitude" value="{{ $location->longitude }}" form="location-{{ $location->id }}" placeholder="Longitude" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="is_active" value="1" form="location-{{ $location->id }}" @checked($location->is_active) class="rounded border-gray-300 text-[#1AAD94]">
                                {{ $location->is_active ? 'Active' : 'Inactive' }}
                            </label>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex justify-end gap-2">
                                <button form="location-{{ $location->id }}" class="text-[#1AAD94] hover:underline">Save</button>
                                <form method="POST" action="{{ route('admin.locations.destroy', $location) }}" onsubmit="return confirm('Delete this country?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-500 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-10 text-center text-gray-400">No countries found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
