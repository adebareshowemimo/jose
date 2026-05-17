@php
    $existingImageUrl = $category?->hero_image_url;
    $bulletsText = old('bullet_points_text', is_array($category?->bullet_points) ? implode("\n", $category->bullet_points) : '');
@endphp

{{-- Hero image --}}
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
            const input = document.getElementById('cat-image-input-{{ $category?->id ?? 'new' }}');
            if (input) input.value = '';
        }
     }">
    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Hero Image</label>
    <div class="relative rounded-xl border-2 border-dashed border-gray-300 hover:border-[#1AAD94] transition group overflow-hidden bg-gray-50">
        <template x-if="previewUrl">
            <div class="relative">
                <img :src="previewUrl" class="w-full h-44 object-cover" alt="Hero preview">
                <button type="button" @click="clear()" class="absolute top-2 right-2 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white/95 backdrop-blur text-xs font-semibold text-red-600 shadow hover:bg-red-50">Remove</button>
            </div>
        </template>
        <template x-if="!previewUrl">
            <label :for="'cat-image-input-{{ $category?->id ?? 'new' }}'" class="block w-full h-44 flex flex-col items-center justify-center text-gray-400 group-hover:text-[#1AAD94]">
                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="text-sm font-medium">Click to upload hero image</span>
                <span class="text-xs mt-1">JPG, PNG, WEBP · max 4 MB</span>
            </label>
        </template>
    </div>
    <input id="cat-image-input-{{ $category?->id ?? 'new' }}" type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" @change="onPick($event)" class="sr-only">
    <input type="hidden" name="remove_image" x-bind:value="markedForRemoval ? '1' : ''">
</div>

<div class="grid grid-cols-1 sm:grid-cols-[1fr_180px] gap-3">
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Name</label>
        <input type="text" name="name" value="{{ old('name', $category?->name) }}" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Sort Order</label>
        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $category?->sort_order ?? 0) }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-[1fr_220px] gap-3">
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $category?->slug) }}" placeholder="Auto-generated from name" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
        <p class="mt-1 text-xs text-gray-400">URL: /services/training/<span class="font-mono">{slug}</span></p>
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Icon</label>
        <input type="text" name="icon" value="{{ old('icon', $category?->icon) }}" placeholder="lucide:users" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
        <p class="mt-1 text-xs text-gray-400">Iconify icon name, optional.</p>
    </div>
</div>

<div>
    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Short Description</label>
    <textarea name="short_description" rows="2" placeholder="Subtitle shown under the hero on the public page." class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">{{ old('short_description', $category?->short_description) }}</textarea>
</div>

<div>
    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Intro Body (HTML allowed)</label>
    <textarea name="intro_html" rows="5" placeholder="<p>Longer introduction paragraph shown above the bullets.</p>" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">{{ old('intro_html', $category?->intro_html) }}</textarea>
</div>

<div>
    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Bullet Points</label>
    <textarea name="bullet_points_text" rows="6" placeholder="One bullet per line — e.g.&#10;Communication & presentation skills&#10;Team leadership & crew resource management" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">{{ $bulletsText }}</textarea>
    <p class="mt-1 text-xs text-gray-400">Each non-empty line becomes one bullet on the "what we cover" list.</p>
</div>

<div class="pt-2">
    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category?->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
        Active (visible on the public site)
    </label>
</div>
