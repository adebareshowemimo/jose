@php
    $statusOptions = ['upcoming', 'active', 'completed', 'draft', 'cancelled'];
    $existingImageUrl = $event?->image_url;
    $eventDescEditorId = 'event-desc-' . ($event?->id ?? 'new');
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

<div x-data="quillEditor({ id: @js($eventDescEditorId), initial: @js(old('description', $event?->description ?? '')) })">
    <div class="flex items-center justify-between mb-2">
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Description</label>
        <div class="inline-flex bg-gray-100 rounded-md p-0.5 text-[11px] font-semibold">
            <button type="button" @click="setMode('rich')" :class="mode === 'rich' ? 'bg-[#1AAD94] text-white' : 'text-gray-600 hover:bg-gray-200'" class="px-2.5 py-1 rounded transition">Rich text</button>
            <button type="button" @click="setMode('html')" :class="mode === 'html' ? 'bg-[#1AAD94] text-white' : 'text-gray-600 hover:bg-gray-200'" class="px-2.5 py-1 rounded transition">HTML</button>
        </div>
    </div>

    <div x-show="mode === 'rich'">
        <div :id="id" class="bg-white"></div>
    </div>
    <div x-show="mode === 'html'" x-cloak>
        <textarea x-model="html" rows="10" class="w-full px-3 py-2.5 border border-gray-300 rounded-b-lg font-mono text-xs focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" placeholder="<p>...</p>"></textarea>
    </div>

    <input type="hidden" name="description" :value="html" required>
    <p class="mt-1.5 text-xs text-gray-400">What attendees will learn, who should attend, agenda highlights — rendered as HTML on the public events page.</p>
</div>

<div>
    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">External Registration URL <span class="text-gray-400 font-normal normal-case">(optional)</span></label>
    <input type="url" name="register_url" value="{{ old('register_url', $event?->register_url) }}" placeholder="https://eventbrite.com/..."
        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    <p class="mt-1 text-xs text-gray-400">If set, attendees go to this external URL. Leave blank to use the internal registration form below.</p>
</div>

{{-- Pricing & capacity (internal ticketing) --}}
<div class="rounded-xl border border-[#073057]/15 bg-[#F9FAFB] p-4">
    <p class="text-xs font-bold uppercase tracking-widest text-[#073057] mb-1">Internal ticketing</p>
    <p class="text-xs text-gray-500 mb-4">Used when no external URL is set. Leave price blank for free RSVPs.</p>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div>
            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Price</label>
            <input type="number" name="price" step="0.01" min="0" value="{{ old('price', $event?->price) }}" placeholder="0 for free"
                class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
        </div>
        <div>
            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Currency</label>
            <select name="currency" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                <option value="">—</option>
                @foreach (['USD', 'EUR', 'GBP', 'NGN', 'AED'] as $cur)
                    <option value="{{ $cur }}" {{ old('currency', $event?->currency) === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Capacity</label>
            <input type="number" name="capacity" min="1" value="{{ old('capacity', $event?->capacity) }}" placeholder="Unlimited if blank"
                class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
        </div>
    </div>
</div>

{{-- Custom registration questions --}}
@php
    $existingQuestions = old('questions', $event?->questions ?? []);
@endphp
<div class="rounded-xl border border-[#073057]/15 bg-[#F9FAFB] p-4"
     x-data="{
         questions: @js($existingQuestions ?: []),
         add() { this.questions.push({label: '', type: 'text', required: false, options: ''}); },
         remove(idx) { this.questions.splice(idx, 1); },
     }">
    <div class="flex items-center justify-between mb-3">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-[#073057]">Registration questions</p>
            <p class="text-xs text-gray-500">Custom fields the registration form will ask each buyer.</p>
        </div>
        <button type="button" @click="add()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#073057] text-white text-xs font-semibold rounded-lg hover:brightness-110">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Add question
        </button>
    </div>

    <template x-if="questions.length === 0">
        <p class="text-xs text-gray-400 italic py-2">No custom questions. Buyer name / email / phone are always collected.</p>
    </template>

    <template x-for="(q, idx) in questions" :key="idx">
        <div class="bg-white border border-gray-200 rounded-lg p-3 mb-2">
            <div class="grid grid-cols-1 sm:grid-cols-[1fr_140px_auto_auto] gap-2 items-start">
                <input type="text" x-model="q.label" :name="'questions['+idx+'][label]'" placeholder="Question label (e.g. Dietary requirements)" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                <select x-model="q.type" :name="'questions['+idx+'][type]'" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                    <option value="text">Text</option>
                    <option value="textarea">Long text</option>
                    <option value="select">Select</option>
                </select>
                <label class="inline-flex items-center gap-1.5 text-xs text-gray-700 self-center">
                    <input type="checkbox" x-model="q.required" :true-value="1" :false-value="0" :name="'questions['+idx+'][required]'" class="rounded border-gray-300 text-[#1AAD94]">
                    Req'd
                </label>
                <button type="button" @click="remove(idx)" class="text-red-500 hover:text-red-700 self-center text-sm" title="Remove">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7"/></svg>
                </button>
            </div>
            <div x-show="q.type === 'select'" x-cloak class="mt-2">
                <textarea x-model="q.options" :name="'questions['+idx+'][options]'" rows="2" placeholder="One option per line (e.g. S, M, L, XL)" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent"></textarea>
            </div>
        </div>
    </template>
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
