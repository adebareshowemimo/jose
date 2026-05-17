@php
    $existingImageUrl = ($job?->thumbnail) ? \Illuminate\Support\Facades\Storage::url($job->thumbnail) : null;
    $editorId = 'cs-desc-' . ($job?->id ?? 'new');
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
            const input = document.getElementById('cs-image-input-{{ $job?->id ?? 'new' }}');
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
            <label :for="'cs-image-input-{{ $job?->id ?? 'new' }}'" class="block w-full h-44 flex flex-col items-center justify-center text-gray-400 group-hover:text-[#1AAD94]">
                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="text-sm font-medium">Click to upload a cover image</span>
                <span class="text-xs mt-1">JPG, PNG, WEBP, or GIF · max 4 MB</span>
            </label>
        </template>
    </div>
    <input id="cs-image-input-{{ $job?->id ?? 'new' }}" type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" @change="onPick($event)" class="sr-only">
    <input type="hidden" name="remove_image" x-bind:value="markedForRemoval ? '1' : ''">
    <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-gray-500">
        <label :for="'cs-image-input-{{ $job?->id ?? 'new' }}'" class="inline-flex items-center gap-1 font-semibold text-[#1AAD94] hover:text-[#0F8B75]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            <span x-text="previewUrl ? 'Replace image' : 'Choose image'"></span>
        </label>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-[1fr_220px] gap-3">
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Role Title</label>
        <input type="text" name="title" value="{{ old('title', $job?->title) }}" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $job?->slug) }}" placeholder="Auto" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Category</label>
        <select name="category_id" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
            <option value="">— None —</option>
            @foreach (($categories ?? collect()) as $cat)
                <option value="{{ $cat->id }}" {{ (int) old('category_id', $job?->category_id) === (int) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Job Type</label>
        <select name="job_type_id" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
            <option value="">— None —</option>
            @foreach (($jobTypes ?? collect()) as $jt)
                <option value="{{ $jt->id }}" {{ (int) old('job_type_id', $job?->job_type_id) === (int) $jt->id ? 'selected' : '' }}>{{ $jt->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Location</label>
        <select name="location_id" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
            <option value="">— None —</option>
            @foreach (($locations ?? collect()) as $loc)
                <option value="{{ $loc->id }}" {{ (int) old('location_id', $job?->location_id) === (int) $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div>
    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Address / Site</label>
    <input type="text" name="address" value="{{ old('address', $job?->address) }}" placeholder="On-site Lagos, Remote, etc." class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
</div>

<div x-data="quillEditor({
        id: @js($editorId),
        initial: @js(old('description', $job?->description ?? ''))
     })">
    <div class="flex items-center justify-between mb-2">
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Role Description</label>
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
    <input type="hidden" name="description" :value="html" required>
</div>

<div>
    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Qualification Summary</label>
    <input type="text" name="qualification" value="{{ old('qualification', $job?->qualification) }}" placeholder="e.g. BSc/HND + 3 years offshore" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Salary Min</label>
        <input type="number" step="0.01" min="0" name="salary_min" value="{{ old('salary_min', $job?->salary_min) }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Salary Max</label>
        <input type="number" step="0.01" min="0" name="salary_max" value="{{ old('salary_max', $job?->salary_max) }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Salary Period</label>
        <select name="salary_type" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
            <option value="">—</option>
            @foreach (['hourly', 'monthly', 'yearly'] as $p)
                <option value="{{ $p }}" {{ old('salary_type', $job?->salary_type) === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Experience</label>
        <input type="text" name="experience_required" value="{{ old('experience_required', $job?->experience_required) }}" placeholder="3+ years" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Deadline</label>
        <input type="date" name="deadline" value="{{ old('deadline', $job?->deadline?->format('Y-m-d')) }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Vacancies</label>
        <input type="number" min="1" name="vacancies" value="{{ old('vacancies', $job?->vacancies) }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Hours / Duration</label>
        <input type="text" name="hours" value="{{ old('hours', $job?->hours) }}" placeholder="6-month contract" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
    </div>
</div>

<div>
    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block">Hours Type</label>
    <select name="hours_type" class="w-full sm:w-60 px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
        <option value="">—</option>
        @foreach (['full-time' => 'Full-time', 'part-time' => 'Part-time'] as $val => $label)
            <option value="{{ $val }}" {{ old('hours_type', $job?->hours_type) === $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
</div>

<div class="flex flex-wrap gap-4 pt-2">
    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $job ? ($job->status === 'active') : true) ? 'checked' : '' }} class="rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
        Active (publicly visible)
    </label>
    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $job?->is_featured) ? 'checked' : '' }} class="rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
        Featured
    </label>
    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="is_urgent" value="1" {{ old('is_urgent', $job?->is_urgent) ? 'checked' : '' }} class="rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
        Urgent
    </label>
</div>
