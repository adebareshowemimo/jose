@php
    $statusOptions = ['upcoming', 'active', 'completed', 'draft', 'cancelled'];
@endphp

<div>
    <label class="text-xs font-medium text-gray-500 mb-1 block">Title</label>
    <input type="text" name="title" value="{{ old('title', $event?->title) }}" required
        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
    <div>
        <label class="text-xs font-medium text-gray-500 mb-1 block">Type</label>
        <input type="text" name="type" value="{{ old('type', $event?->type ?? 'Event') }}" required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-medium text-gray-500 mb-1 block">Category</label>
        <select name="category" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
            <option value="hosted" {{ old('category', $event?->category ?? 'hosted') === 'hosted' ? 'selected' : '' }}>JCL hosted</option>
            <option value="industry" {{ old('category', $event?->category) === 'industry' ? 'selected' : '' }}>Industry</option>
        </select>
    </div>
</div>

<div>
    <label class="text-xs font-medium text-gray-500 mb-1 block">Display Date</label>
    <input type="text" name="display_date" value="{{ old('display_date', $event?->display_date) }}" placeholder="June 18 - 20, 2026" required
        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
    <div>
        <label class="text-xs font-medium text-gray-500 mb-1 block">Start Date</label>
        <input type="date" name="starts_at" value="{{ old('starts_at', $event?->starts_at?->format('Y-m-d')) }}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-medium text-gray-500 mb-1 block">End Date</label>
        <input type="date" name="ends_at" value="{{ old('ends_at', $event?->ends_at?->format('Y-m-d')) }}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
</div>

<div>
    <label class="text-xs font-medium text-gray-500 mb-1 block">Location</label>
    <input type="text" name="location" value="{{ old('location', $event?->location) }}" required
        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
</div>

<div>
    <label class="text-xs font-medium text-gray-500 mb-1 block">Description</label>
    <textarea name="description" rows="4" required
        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">{{ old('description', $event?->description) }}</textarea>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
    <div>
        <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
        <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
            @foreach($statusOptions as $status)
                <option value="{{ $status }}" {{ old('status', $event?->status ?? 'upcoming') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-xs font-medium text-gray-500 mb-1 block">Sort Order</label>
        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $event?->sort_order ?? 0) }}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
</div>

<label class="inline-flex items-center gap-2 text-sm text-gray-600">
    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $event?->is_featured) ? 'checked' : '' }}
        class="rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
    Featured
</label>
