@php
    $statusOptions = ['published', 'draft', 'archived'];
    $contentValue = old('content');

    if ($contentValue === null && $article) {
        $contentValue = implode("\n\n", $article->content ?? []);
    }
@endphp

<div>
    <label class="text-xs font-medium text-gray-500 mb-1 block">Title</label>
    <input type="text" name="title" value="{{ old('title', $article?->title) }}" required
        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
</div>

<div>
    <label class="text-xs font-medium text-gray-500 mb-1 block">Slug</label>
    <input type="text" name="slug" value="{{ old('slug', $article?->slug) }}" placeholder="Auto-generated from title"
        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
</div>

<div>
    <label class="text-xs font-medium text-gray-500 mb-1 block">Excerpt</label>
    <textarea name="excerpt" rows="3" required
        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">{{ old('excerpt', $article?->excerpt) }}</textarea>
</div>

<div>
    <label class="text-xs font-medium text-gray-500 mb-1 block">Content</label>
    <textarea name="content" rows="7" required placeholder="Separate paragraphs with a blank line."
        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">{{ $contentValue }}</textarea>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
    <div>
        <label class="text-xs font-medium text-gray-500 mb-1 block">Author</label>
        <input type="text" name="author" value="{{ old('author', $article?->author ?? 'JCL Editorial') }}" required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-medium text-gray-500 mb-1 block">Category</label>
        <input type="text" name="category" value="{{ old('category', $article?->category ?? 'News') }}" required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
    <div>
        <label class="text-xs font-medium text-gray-500 mb-1 block">Published Date</label>
        <input type="date" name="published_at" value="{{ old('published_at', $article?->published_at?->format('Y-m-d')) }}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
        <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
            @foreach($statusOptions as $status)
                <option value="{{ $status }}" {{ old('status', $article?->status ?? 'published') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-xs font-medium text-gray-500 mb-1 block">Sort Order</label>
        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $article?->sort_order ?? 0) }}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
</div>

<label class="inline-flex items-center gap-2 text-sm text-gray-600">
    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $article?->is_featured) ? 'checked' : '' }}
        class="rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
    Featured
</label>
