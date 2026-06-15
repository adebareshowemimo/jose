@php
    $existingImageUrl = ($job?->thumbnail) ? \Illuminate\Support\Facades\Storage::url($job->thumbnail) : null;
    $existingGallery = $job?->gallery ?? [];
    $editorId = 'cs-desc-' . ($job?->id ?? 'new');
    $imgInputId = 'cs-image-input-' . ($job?->id ?? 'new');
    $submitLabel = $job ? 'Save Changes' : 'Post Role';
    $submitClass = $job ? 'bg-[#073057]' : 'bg-[#1AAD94]';
    $labelClass = 'text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 block';
    $inputClass = 'w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent';
@endphp

@include('admin.partials.quill-editor')

<div x-data="{
        step: 1,
        applyMethod: @js(old('apply_method', $job?->apply_method ?? 'internal')),
        summary: {},
        buildSummary() {
            const form = this.$root.closest('form');
            const get = (name) => {
                const el = form.elements[name];
                if (!el) return '';
                if (el.tagName === 'SELECT') return el.value ? (el.options[el.selectedIndex]?.text || '') : '';
                if (el.type === 'checkbox') return el.checked;
                return el.value;
            };
            this.summary = {
                title: get('title'),
                category: get('category_id'),
                job_type: get('job_type_id'),
                location: get('location_id'),
                address: get('address'),
                gender: get('gender_preference'),
                description: get('description'),
                experience: get('experience_required'),
                vacancies: get('vacancies'),
                qualification: get('qualification'),
                video_url: get('video_url'),
                salary_min: get('salary_min'),
                salary_max: get('salary_max'),
                salary_type: get('salary_type'),
                hours: get('hours'),
                hours_type: get('hours_type'),
                deadline: get('deadline'),
                apply_method: get('apply_method'),
                apply_url: get('apply_url'),
                apply_email: get('apply_email'),
                is_active: get('is_active'),
                is_featured: get('is_featured'),
                is_urgent: get('is_urgent'),
            };
        },
        salaryRange() {
            const min = this.summary.salary_min, max = this.summary.salary_max;
            if (min && max) return min + ' – ' + max;
            if (min) return 'From ' + min;
            if (max) return 'Up to ' + max;
            return '—';
        }
     }"
     x-init="$watch('step', v => { if (v === 4) buildSummary(); })"
     class="space-y-6">

    {{-- Step progress --}}
    <div class="flex items-center justify-between relative px-2">
        <div class="absolute top-5 left-0 right-0 h-0.5 bg-gray-200 -z-10"></div>
        <div class="absolute top-5 left-0 h-0.5 bg-[#1AAD94] -z-10 transition-all" :style="'width: ' + ((step - 1) * 33.33) + '%'"></div>
        @foreach([1 => 'Job Details', 2 => 'Requirements', 3 => 'Compensation', 4 => 'Review'] as $number => $label)
            <button type="button" class="flex flex-col items-center bg-white px-1" @click="step = {{ $number }}">
                <span :class="step >= {{ $number }} ? 'bg-[#1AAD94] text-white' : 'bg-gray-200 text-gray-500'" class="w-10 h-10 rounded-full flex items-center justify-center font-semibold transition">{{ $number }}</span>
                <span class="text-xs mt-2 text-center" :class="step >= {{ $number }} ? 'text-[#0A1929] font-semibold' : 'text-gray-400'">{{ $label }}</span>
            </button>
        @endforeach
    </div>

    {{-- ───────────────────────── STEP 1 — JOB DETAILS ───────────────────────── --}}
    <div x-show="step === 1" class="space-y-5">

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
                    const input = document.getElementById('{{ $imgInputId }}');
                    if (input) input.value = '';
                }
             }">
            <label class="{{ $labelClass }}">Cover Image</label>
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
                    <label :for="'{{ $imgInputId }}'" class="block w-full h-44 flex flex-col items-center justify-center text-gray-400 group-hover:text-[#1AAD94]">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-sm font-medium">Click to upload a cover image</span>
                        <span class="text-xs mt-1">JPG, PNG, WEBP, or GIF · max 4 MB</span>
                    </label>
                </template>
            </div>
            <input id="{{ $imgInputId }}" type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" @change="onPick($event)" class="sr-only">
            <input type="hidden" name="remove_image" x-bind:value="markedForRemoval ? '1' : ''">
            <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-gray-500">
                <label :for="'{{ $imgInputId }}'" class="inline-flex items-center gap-1 font-semibold text-[#1AAD94] hover:text-[#0F8B75]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span x-text="previewUrl ? 'Replace image' : 'Choose image'"></span>
                </label>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-[1fr_220px] gap-3">
            <div>
                <label class="{{ $labelClass }}">Role Title</label>
                <input type="text" name="title" value="{{ old('title', $job?->title) }}" required class="{{ $inputClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $job?->slug) }}" placeholder="Auto" class="{{ $inputClass }}">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="{{ $labelClass }}">Category</label>
                <select name="category_id" class="{{ $inputClass }}">
                    <option value="">— None —</option>
                    @foreach (($categories ?? collect()) as $cat)
                        <option value="{{ $cat->id }}" {{ (int) old('category_id', $job?->category_id) === (int) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">Job Type</label>
                <select name="job_type_id" class="{{ $inputClass }}">
                    <option value="">— None —</option>
                    @foreach (($jobTypes ?? collect()) as $jt)
                        <option value="{{ $jt->id }}" {{ (int) old('job_type_id', $job?->job_type_id) === (int) $jt->id ? 'selected' : '' }}>{{ $jt->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">Location</label>
                <select name="location_id" class="{{ $inputClass }}">
                    <option value="">— None —</option>
                    @foreach (($locations ?? collect()) as $loc)
                        <option value="{{ $loc->id }}" {{ (int) old('location_id', $job?->location_id) === (int) $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-[1fr_220px] gap-3">
            <div>
                <label class="{{ $labelClass }}">Address / Site</label>
                <input type="text" name="address" value="{{ old('address', $job?->address) }}" placeholder="On-site Lagos, Remote, etc." class="{{ $inputClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}">Gender Preference</label>
                <select name="gender_preference" class="{{ $inputClass }}">
                    @foreach (['any' => 'Any', 'male' => 'Male', 'female' => 'Female'] as $val => $label)
                        <option value="{{ $val }}" {{ old('gender_preference', $job?->gender_preference ?? 'any') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
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
    </div>

    {{-- ───────────────────────── STEP 2 — REQUIREMENTS ───────────────────────── --}}
    <div x-show="step === 2" x-cloak class="space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="{{ $labelClass }}">Experience</label>
                <input type="text" name="experience_required" value="{{ old('experience_required', $job?->experience_required) }}" placeholder="3+ years" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}">Vacancies</label>
                <input type="number" min="1" name="vacancies" value="{{ old('vacancies', $job?->vacancies) }}" class="{{ $inputClass }}">
            </div>
        </div>

        <div>
            <label class="{{ $labelClass }}">Qualification Summary</label>
            <input type="text" name="qualification" value="{{ old('qualification', $job?->qualification) }}" placeholder="e.g. BSc/HND + 3 years offshore" class="{{ $inputClass }}">
        </div>

        {{-- Gallery (multiple images) --}}
        <div x-data="{
                removed: [],
                newPreviews: [],
                toggleRemove(p) { this.removed.includes(p) ? (this.removed = this.removed.filter(x => x !== p)) : this.removed.push(p); },
                onPick(e) {
                    this.newPreviews = [];
                    Array.from(e.target.files).forEach(f => {
                        const r = new FileReader();
                        r.onload = ev => this.newPreviews.push(ev.target.result);
                        r.readAsDataURL(f);
                    });
                }
             }">
            <label class="{{ $labelClass }}">Gallery Images</label>
            <div class="flex flex-wrap gap-3">
                @foreach ($existingGallery as $path)
                    <div x-show="!removed.includes(@js($path))" class="relative">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($path) }}" class="h-24 w-24 object-cover rounded-lg border border-gray-200" alt="Gallery image">
                        <button type="button" @click="toggleRemove(@js($path))" title="Remove" class="absolute -top-2 -right-2 w-6 h-6 inline-flex items-center justify-center rounded-full bg-white text-red-600 shadow border border-gray-200 hover:bg-red-50">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endforeach
                <template x-for="(src, i) in newPreviews" :key="i">
                    <img :src="src" class="h-24 w-24 object-cover rounded-lg border border-gray-200" alt="New gallery image">
                </template>
            </div>
            <template x-for="path in removed" :key="path">
                <input type="hidden" name="remove_gallery[]" :value="path">
            </template>
            <label class="mt-3 inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-[#1AAD94] border border-[#1AAD94]/40 rounded-lg cursor-pointer hover:bg-[#1AAD94]/5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add images
                <input type="file" name="gallery[]" multiple accept="image/jpeg,image/png,image/webp,image/gif" @change="onPick($event)" class="sr-only">
            </label>
            <p class="text-xs text-gray-400 mt-1">Up to 6 images · JPG, PNG, WEBP, GIF · max 4 MB each</p>
        </div>

        <div>
            <label class="{{ $labelClass }}">Video URL</label>
            <input type="url" name="video_url" value="{{ old('video_url', $job?->video_url) }}" placeholder="https://youtube.com/watch?v=..." class="{{ $inputClass }}">
        </div>
    </div>

    {{-- ───────────────────────── STEP 3 — COMPENSATION & APPLICATION ───────────────────────── --}}
    <div x-show="step === 3" x-cloak class="space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="{{ $labelClass }}">Salary Min</label>
                <input type="number" step="0.01" min="0" name="salary_min" value="{{ old('salary_min', $job?->salary_min) }}" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}">Salary Max</label>
                <input type="number" step="0.01" min="0" name="salary_max" value="{{ old('salary_max', $job?->salary_max) }}" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}">Salary Period</label>
                <select name="salary_type" class="{{ $inputClass }}">
                    <option value="">—</option>
                    @foreach (['hourly', 'monthly', 'yearly'] as $p)
                        <option value="{{ $p }}" {{ old('salary_type', $job?->salary_type) === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="{{ $labelClass }}">Hours / Duration</label>
                <input type="text" name="hours" value="{{ old('hours', $job?->hours) }}" placeholder="6-month contract" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}">Hours Type</label>
                <select name="hours_type" class="{{ $inputClass }}">
                    <option value="">—</option>
                    @foreach (['full-time' => 'Full-time', 'part-time' => 'Part-time'] as $val => $label)
                        <option value="{{ $val }}" {{ old('hours_type', $job?->hours_type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">Deadline</label>
                <input type="date" name="deadline" value="{{ old('deadline', $job?->deadline?->format('Y-m-d')) }}" class="{{ $inputClass }}">
            </div>
        </div>

        {{-- Application method --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="{{ $labelClass }}">Application Method</label>
                <select name="apply_method" x-model="applyMethod" class="{{ $inputClass }}">
                    <option value="internal" @selected(old('apply_method', $job?->apply_method ?? 'internal') === 'internal')>Apply on platform (internal)</option>
                    <option value="external_link" @selected(old('apply_method', $job?->apply_method) === 'external_link')>External link</option>
                    <option value="email" @selected(old('apply_method', $job?->apply_method) === 'email')>Email</option>
                </select>
            </div>
            <div x-show="applyMethod === 'external_link'" x-cloak>
                <label class="{{ $labelClass }}">Application URL</label>
                <input type="url" name="apply_url" value="{{ old('apply_url', $job?->apply_url) }}" placeholder="https://..." class="{{ $inputClass }}">
            </div>
            <div x-show="applyMethod === 'email'" x-cloak>
                <label class="{{ $labelClass }}">Application Email</label>
                <input type="email" name="apply_email" value="{{ old('apply_email', $job?->apply_email) }}" placeholder="jobs@company.com" class="{{ $inputClass }}">
            </div>
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
    </div>

    {{-- ───────────────────────── STEP 4 — REVIEW ───────────────────────── --}}
    <div x-show="step === 4" x-cloak class="space-y-5">
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Review the role before publishing</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                <div><dt class="text-xs text-gray-400">Title</dt><dd class="text-gray-800" x-text="summary.title || '—'"></dd></div>
                <div><dt class="text-xs text-gray-400">Category</dt><dd class="text-gray-800" x-text="summary.category || '—'"></dd></div>
                <div><dt class="text-xs text-gray-400">Job Type</dt><dd class="text-gray-800" x-text="summary.job_type || '—'"></dd></div>
                <div><dt class="text-xs text-gray-400">Location</dt><dd class="text-gray-800" x-text="summary.location || '—'"></dd></div>
                <div><dt class="text-xs text-gray-400">Address / Site</dt><dd class="text-gray-800" x-text="summary.address || '—'"></dd></div>
                <div><dt class="text-xs text-gray-400">Gender Preference</dt><dd class="text-gray-800" x-text="summary.gender || '—'"></dd></div>
                <div><dt class="text-xs text-gray-400">Experience</dt><dd class="text-gray-800" x-text="summary.experience || '—'"></dd></div>
                <div><dt class="text-xs text-gray-400">Vacancies</dt><dd class="text-gray-800" x-text="summary.vacancies || '—'"></dd></div>
                <div><dt class="text-xs text-gray-400">Salary</dt><dd class="text-gray-800"><span x-text="salaryRange()"></span> <span class="text-gray-400" x-text="summary.salary_type"></span></dd></div>
                <div><dt class="text-xs text-gray-400">Hours</dt><dd class="text-gray-800"><span x-text="summary.hours || '—'"></span> <span class="text-gray-400" x-text="summary.hours_type"></span></dd></div>
                <div><dt class="text-xs text-gray-400">Deadline</dt><dd class="text-gray-800" x-text="summary.deadline || '—'"></dd></div>
                <div><dt class="text-xs text-gray-400">Video URL</dt><dd class="text-gray-800 truncate" x-text="summary.video_url || '—'"></dd></div>
                <div>
                    <dt class="text-xs text-gray-400">Apply Method</dt>
                    <dd class="text-gray-800">
                        <span x-text="summary.apply_method || '—'"></span>
                        <span class="text-gray-500" x-show="summary.apply_url" x-text="'· ' + summary.apply_url"></span>
                        <span class="text-gray-500" x-show="summary.apply_email" x-text="'· ' + summary.apply_email"></span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400">Flags</dt>
                    <dd class="text-gray-800">
                        <span x-text="summary.is_active ? 'Active' : 'Paused'"></span><span x-show="summary.is_featured"> · Featured</span><span x-show="summary.is_urgent"> · Urgent</span>
                    </dd>
                </div>
            </dl>

            <div class="mt-4">
                <dt class="text-xs text-gray-400 mb-1">Description</dt>
                <div class="prose prose-sm max-w-none bg-white border border-gray-200 rounded-lg p-3 max-h-48 overflow-auto" x-html="summary.description || '<span class=&quot;text-gray-400&quot;>—</span>'"></div>
            </div>

            <p class="mt-4 text-xs text-gray-500">An <strong>Active</strong> role goes live on the public Contract Staffing page immediately (admin posts are auto-approved). Uncheck “Active” in the previous step to keep it paused.</p>
        </div>
    </div>

    {{-- Navigation --}}
    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
        <a href="{{ route('admin.contract-staffing.index') }}" class="px-5 py-2.5 text-sm text-gray-600 hover:text-gray-900">Cancel</a>
        <div class="flex items-center gap-2">
            <button type="button" x-show="step > 1" @click="step = step - 1" class="px-5 py-2.5 text-sm font-semibold border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Back</button>
            <button type="button" x-show="step < 4" @click="step = step + 1" class="px-6 py-2.5 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold rounded-lg shadow transition">Next</button>
            <button type="submit" x-show="step === 4" x-cloak class="px-6 py-2.5 {{ $submitClass }} hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-lg shadow transition">{{ $submitLabel }}</button>
        </div>
    </div>
</div>
