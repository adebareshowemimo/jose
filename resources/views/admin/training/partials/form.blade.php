@php
    $existingImageUrl = $program?->image_url;
    $editorId = 'trn-desc-' . ($program?->id ?? 'new');
@endphp

@include('admin.partials.quill-editor')

{{-- Cover image --}}
<div x-data="{
        previewUrl: @js($existingImageUrl),
        hasExisting: @js((bool) $existingImageUrl),
        markedForRemoval: false,
        onPick(event) {
            const file = event.target.files?.[0];
            if (!file) return;
            this.markedForRemoval = false;
            const reader = new FileReader();
            reader.onload = (e) => { this.previewUrl = e.target.result; };
            reader.readAsDataURL(file);
        },
        clear() {
            this.previewUrl = null;
            this.markedForRemoval = this.hasExisting;
            const input = document.getElementById('training-image-input-{{ $program?->id ?? 'new' }}');
            if (input) input.value = '';
        }
     }">
    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Cover Image</label>
    <div class="relative rounded-xl border-2 border-dashed border-gray-300 hover:border-[#1AAD94] transition group overflow-hidden bg-gray-50">
        <template x-if="previewUrl">
            <div class="relative">
                <img :src="previewUrl" class="w-full h-44 object-cover" alt="Cover preview">
                <button type="button" @click="clear()" class="absolute top-2 right-2 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white/95 backdrop-blur text-xs font-semibold text-red-600 shadow hover:bg-red-50">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Remove
                </button>
            </div>
        </template>
        <template x-if="!previewUrl">
            <label :for="'training-image-input-{{ $program?->id ?? 'new' }}'" class="block w-full h-44 flex flex-col items-center justify-center text-gray-400 group-hover:text-[#1AAD94]">
                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="text-sm font-medium">Click to upload a cover image</span>
                <span class="text-xs mt-1">JPG, PNG, WEBP, or GIF · max 4 MB</span>
            </label>
        </template>
    </div>
    <input id="training-image-input-{{ $program?->id ?? 'new' }}" type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" @change="onPick($event)" class="sr-only">
    <input type="hidden" name="remove_image" x-bind:value="markedForRemoval ? '1' : ''">
    <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-gray-500">
        <label :for="'training-image-input-{{ $program?->id ?? 'new' }}'" class="inline-flex items-center gap-1 font-semibold text-[#1AAD94] hover:text-[#0F8B75]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            <span x-text="previewUrl ? 'Replace image' : 'Choose image'"></span>
        </label>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-[1fr_180px] gap-3">
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Title</label>
        <input type="text" name="title" value="{{ old('title', $program?->title) }}" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Type</label>
        <select name="type" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
            <option value="training" {{ old('type', $program?->type ?? 'training') === 'training' ? 'selected' : '' }}>Training</option>
            <option value="apprenticeship" {{ old('type', $program?->type) === 'apprenticeship' ? 'selected' : '' }}>Apprenticeship</option>
        </select>
    </div>
</div>

<div>
    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Slug</label>
    <input type="text" name="slug" value="{{ old('slug', $program?->slug) }}" placeholder="Auto-generated from title" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
</div>

<div>
    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Short Description</label>
    <textarea name="short_description" rows="2" placeholder="One-line summary shown on listing cards." class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">{{ old('short_description', $program?->short_description) }}</textarea>
</div>

<div x-data="quillEditor({
        id: @js($editorId),
        initial: @js(old('long_description', $program?->long_description ?? ''))
     })">
    <div class="flex items-center justify-between mb-2">
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Full Description</label>
        <div class="inline-flex bg-gray-100 rounded-md p-0.5 text-[11px] font-semibold">
            <button type="button" @click="setMode('rich')" :class="mode === 'rich' ? 'bg-[#1AAD94] text-white' : 'text-gray-600 hover:bg-gray-200'" class="px-2.5 py-1 rounded transition">Rich text</button>
            <button type="button" @click="setMode('html')" :class="mode === 'html' ? 'bg-[#1AAD94] text-white' : 'text-gray-600 hover:bg-gray-200'" class="px-2.5 py-1 rounded transition">HTML</button>
        </div>
    </div>

    <div x-show="mode === 'rich'">
        <div :id="id" class="bg-white"></div>
    </div>
    <div x-show="mode === 'html'" x-cloak>
        <textarea x-model="html" rows="14" class="w-full px-3 py-2.5 border border-gray-300 rounded-b-lg font-mono text-xs focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" placeholder="<p>...</p>"></textarea>
    </div>

    <input type="hidden" name="long_description" :value="html" required>
    <p class="mt-1.5 text-xs text-gray-400">Use the toolbar to format. Rendered as HTML on the program detail page.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-[1fr_120px] gap-3">
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Price</label>
        <input type="number" name="price" step="0.01" min="0" value="{{ old('price', $program?->price ?? 0) }}" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Currency</label>
        <select name="currency" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
            @foreach (['USD', 'EUR', 'GBP', 'NGN', 'AED'] as $cur)
                <option value="{{ $cur }}" {{ old('currency', $program?->currency ?? 'USD') === $cur ? 'selected' : '' }}>{{ $cur }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Duration</label>
        <input type="text" name="duration" value="{{ old('duration', $program?->duration) }}" placeholder="2 weeks, 3 months..." class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Level</label>
        <input type="text" name="level" value="{{ old('level', $program?->level) }}" placeholder="Foundation, Advanced..." class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Capacity</label>
        <input type="number" name="capacity" min="1" value="{{ old('capacity', $program?->capacity) }}" placeholder="Unlimited if blank" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Starts</label>
        <input type="date" name="starts_at" value="{{ old('starts_at', $program?->starts_at?->format('Y-m-d')) }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Enrol Deadline</label>
        <input type="date" name="enrol_deadline" value="{{ old('enrol_deadline', $program?->enrol_deadline?->format('Y-m-d')) }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Sort Order</label>
        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $program?->sort_order ?? 0) }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
</div>

<div>
    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Category</label>
    <input type="text" name="category" value="{{ old('category', $program?->category) }}" placeholder="STCW, Soft Skills, Technical..." class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
</div>

<div class="flex flex-wrap gap-4 pt-2">
    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $program?->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
        Active (publicly visible)
    </label>
    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $program?->is_featured) ? 'checked' : '' }} class="rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
        Featured
    </label>
</div>
