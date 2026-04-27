@extends('admin.layouts.app')

@section('title', 'Job Types')
@section('page-title', 'Job Types')

@section('content')
<div class="grid gap-6 xl:grid-cols-[360px_1fr]">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="font-semibold text-gray-900 mb-4">Add Job Type</h2>
        <form method="POST" action="{{ route('admin.job-types.store') }}" class="space-y-4">
            @csrf
            <input type="text" name="name" required placeholder="Rotational" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-[#1AAD94]">
                Active
            </label>
            <button class="w-full px-4 py-2 bg-[#073057] text-white text-sm font-semibold rounded-lg">Create Job Type</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 font-medium text-gray-500">Name</th>
                    <th class="text-left px-5 py-3 font-medium text-gray-500">Status</th>
                    <th class="text-right px-5 py-3 font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($jobTypes as $type)
                    <tr>
                        <td class="px-5 py-3">
                            <form id="job-type-{{ $type->id }}" method="POST" action="{{ route('admin.job-types.update', $type) }}">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $type->name }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </form>
                        </td>
                        <td class="px-5 py-3">
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="is_active" value="1" form="job-type-{{ $type->id }}" @checked($type->is_active) class="rounded border-gray-300 text-[#1AAD94]">
                                {{ $type->is_active ? 'Active' : 'Inactive' }}
                            </label>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex justify-end gap-2">
                                <button form="job-type-{{ $type->id }}" class="text-[#1AAD94] hover:underline">Save</button>
                                <form method="POST" action="{{ route('admin.job-types.destroy', $type) }}" onsubmit="return confirm('Delete this job type?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-500 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-10 text-center text-gray-400">No job types found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
