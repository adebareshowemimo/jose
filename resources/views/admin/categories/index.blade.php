@extends('admin.layouts.app')

@section('title', 'Job Categories')
@section('page-title', 'Job Categories')

@section('content')
<div class="grid gap-6 xl:grid-cols-[380px_1fr]">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="font-semibold text-gray-900 mb-4">Add Category</h2>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Name</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Parent</label>
                <select name="parent_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                    <option value="">Top-level category</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Icon</label>
                    <input type="text" name="icon" placeholder="anchor" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Sort</label>
                    <input type="number" name="sort_order" min="0" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]"></textarea>
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-[#1AAD94]">
                Active
            </label>
            <button type="submit" class="w-full px-4 py-2 bg-[#073057] text-white text-sm font-semibold rounded-lg hover:bg-[#073057]/90">Create Category</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-5 border-b border-gray-200">
            <h2 class="font-semibold text-gray-900">Categories</h2>
            <p class="text-sm text-gray-500">Active categories appear on employer job posting forms.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Name</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Parent</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Status</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Sort</th>
                        <th class="text-right px-5 py-3 font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <form id="category-{{ $category->id }}" method="POST" action="{{ route('admin.categories.update', $category) }}" class="grid gap-2 md:grid-cols-[1fr_120px]">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="name" value="{{ $category->name }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    <input type="text" name="icon" value="{{ $category->icon }}" placeholder="Icon" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    <textarea name="description" rows="2" class="md:col-span-2 px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Description">{{ $category->description }}</textarea>
                                </form>
                            </td>
                            <td class="px-5 py-3">
                                <select name="parent_id" form="category-{{ $category->id }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    <option value="">Top-level</option>
                                    @foreach($parents->where('id', '!=', $category->id) as $parent)
                                        <option value="{{ $parent->id }}" @selected((int) $category->parent_id === (int) $parent->id)>{{ $parent->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-5 py-3">
                                <label class="flex items-center gap-2 text-sm text-gray-700">
                                    <input type="checkbox" name="is_active" value="1" form="category-{{ $category->id }}" @checked($category->is_active) class="rounded border-gray-300 text-[#1AAD94]">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </label>
                            </td>
                            <td class="px-5 py-3">
                                <input type="number" name="sort_order" value="{{ $category->sort_order }}" min="0" form="category-{{ $category->id }}" class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex justify-end gap-2">
                                    <button type="submit" form="category-{{ $category->id }}" class="text-[#1AAD94] hover:underline">Save</button>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:underline">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-gray-400">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
