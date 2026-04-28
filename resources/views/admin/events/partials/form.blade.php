@php
    $statusOptions = ['upcoming', 'active', 'completed', 'draft', 'cancelled'];
    $existingImageUrl = $event?->image_url;
@endphp

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
            const input = document.getElementById('event-image-input-{{ $event?->id ?? 'new' }}');
            if (input) input.value = '';
        }
     }">
    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Cover Image</label>

    <div class="relative rounded-xl border-2 border-dashed border-gray-300 hover:border-[#1AAD94] transition group overflow-hidden bg-gray-50">
        <template x-if="previewUrl">
            <div class="relative">
                <img :src="previewUrl" class="w-full h-44 object-cover" alt="Cover preview">
                <button type="button" @click="clear()"
                        class="absolute top-2 right-2 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white/95 backdrop-blur text-xs font-semibold text-red-600 shadow hover:bg-red-50">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Remove
                </button>
            </div>
        </template>
        <template x-if="!previewUrl">
            <label :for="'event-image-input-{{ $event?->id ?? 'new' }}'" class="block w-full h-44 flex flex-col items-center justify-center text-gray-400 group-hover:text-[#1AAD94]">
                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="text-sm font-medium">Click to upload a cover image</span>
                <span class="text-xs mt-1">JPG, PNG, WEBP, or GIF · max 4 MB</span>
            </label>
        </template>
    </div>

    <input id="event-image-input-{{ $event?->id ?? 'new' }}"
           type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif"
           @change="onPick($event)"
           class="sr-only">

    <input type="hidden" name="remove_image" x-bind:value="markedForRemoval ? '1' : ''">

    <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-gray-500">
        <label :for="'event-image-input-{{ $event?->id ?? 'new' }}'"
               class="inline-flex items-center gap-1 font-semibold text-[#1AAD94] hover:text-[#0F8B75]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            <span x-text="previewUrl ? 'Replace image' : 'Choose image'"></span>
        </label>
        <span x-show="hasExisting && !markedForRemoval" class="text-gray-400">Saved image is shown above.</span>
        <span x-show="markedForRemoval" class="text-red-600 font-medium">Will be removed when you save.</span>
    </div>
</div>

<div>
    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Title</label>
    <input type="text" name="title" value="{{ old('title', $event?->title) }}" required
        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Type</label>
        <input type="text" name="type" value="{{ old('type', $event?->type ?? 'Event') }}" placeholder="Conference, Webinar, Summit..." required
            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Category</label>
        <select name="category" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
            <option value="hosted" {{ old('category', $event?->category ?? 'hosted') === 'hosted' ? 'selected' : '' }}>JCL hosted</option>
            <option value="industry" {{ old('category', $event?->category) === 'industry' ? 'selected' : '' }}>Industry</option>
        </select>
    </div>
</div>

<div>
    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Display Date</label>
    <input type="text" name="display_date" value="{{ old('display_date', $event?->display_date) }}" placeholder="June 18 - 20, 2026" required
        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    <p class="mt-1 text-xs text-gray-400">How the date appears on the public page (free text).</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Start Date</label>
        <input type="date" name="starts_at" value="{{ old('starts_at', $event?->starts_at?->format('Y-m-d')) }}"
            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">End Date</label>
        <input type="date" name="ends_at" value="{{ old('ends_at', $event?->ends_at?->format('Y-m-d')) }}"
            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
</div>

<div>
    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Location</label>
    <input type="text" name="location" value="{{ old('location', $event?->location) }}" placeholder="Lagos, Nigeria · Hybrid · Online webinar" required
        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
</div>

<div>
    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Description</label>
    <textarea name="description" rows="4" required placeholder="What attendees will learn, who should attend, agenda highlights..."
        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm leading-relaxed focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">{{ old('description', $event?->description) }}</textarea>
</div>

<div>
    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Registration URL <span class="text-gray-400 font-normal normal-case">(optional)</span></label>
    <input type="url" name="register_url" value="{{ old('register_url', $event?->register_url) }}" placeholder="https://eventbrite.com/..."
        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    <p class="mt-1 text-xs text-gray-400">External registration link. Leave blank to use the default "Register Interest" CTA pointing to the contact form.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Status</label>
        <select name="status" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
            @foreach($statusOptions as $status)
                <option value="{{ $status }}" {{ old('status', $event?->status ?? 'upcoming') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Sort Order</label>
        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $event?->sort_order ?? 0) }}"
            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
</div>

<label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $event?->is_featured) ? 'checked' : '' }}
        class="rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
    <span>Feature this event on the homepage</span>
</label>
