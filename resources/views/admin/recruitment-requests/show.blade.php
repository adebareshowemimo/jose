@extends('admin.layouts.app')

@section('title', 'Recruitment Request — ' . $recruitment->job_title)
@section('page-title', 'Recruitment Request')

@section('content')
@php
    $statusBadge = function ($status) {
        return match ($status) {
            'pending' => 'bg-yellow-100 text-yellow-700',
            'quote_sent' => 'bg-blue-100 text-blue-700',
            'paid', 'in_progress' => 'bg-indigo-100 text-indigo-700',
            'candidates_delivered' => 'bg-purple-100 text-purple-700',
            'completed' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-gray-100 text-gray-500',
            default => 'bg-gray-100 text-gray-600',
        };
    };
@endphp

<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <a href="{{ route('admin.recruitment-requests.index') }}" class="text-xs font-semibold text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-2">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>
        <h1 class="text-2xl font-bold text-[#0A1929]">{{ $recruitment->job_title }}</h1>
        <p class="text-sm text-gray-500">{{ $recruitment->company?->name }} &middot; {{ \App\Models\RecruitmentRequest::SERVICE_TYPES[$recruitment->service_type] }} &middot; Submitted {{ $recruitment->created_at->format('M d, Y') }}</p>
    </div>
    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusBadge($recruitment->status) }}">
        {{ \App\Models\RecruitmentRequest::STATUSES[$recruitment->status] }}
    </span>
</div>

@if (session('success'))
    <div class="mb-5 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="mb-5 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
@endif
@if ($errors->any())
    <div class="mb-5 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
        @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
@endif

<div class="grid lg:grid-cols-3 gap-6" x-data="{ tab: 'details', quoteOpen: false, attachOpen: false, uploadOpen: false, notifyOpen: false }">
    {{-- Main panel --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="flex flex-wrap gap-1 p-2 border-b border-gray-200">
                <button @click="tab = 'details'" :class="tab === 'details' ? 'bg-[#1AAD94] text-white' : 'text-gray-600 hover:bg-gray-100'" class="px-4 py-2 rounded-lg text-sm font-semibold transition">Details</button>
                <button @click="tab = 'candidates'" :class="tab === 'candidates' ? 'bg-[#1AAD94] text-white' : 'text-gray-600 hover:bg-gray-100'" class="px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Candidates <span class="text-xs">({{ $recruitment->candidates->count() }})</span>
                </button>
                <button @click="tab = 'admin'" :class="tab === 'admin' ? 'bg-[#1AAD94] text-white' : 'text-gray-600 hover:bg-gray-100'" class="px-4 py-2 rounded-lg text-sm font-semibold transition">Admin Notes</button>
            </div>

            {{-- Details tab --}}
            <div x-show="tab === 'details'" class="p-6">
                <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">CVs requested</dt><dd class="font-semibold text-[#0A1929]">{{ $recruitment->cv_count }}</dd></div>
                    <div><dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">Category</dt><dd>{{ $recruitment->category?->name ?? '—' }}</dd></div>
                    <div><dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">Job type</dt><dd>{{ $recruitment->jobType?->name ?? '—' }}</dd></div>
                    <div><dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">Location</dt><dd>{{ $recruitment->location?->name ?? 'Any' }}</dd></div>
                    <div><dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">Experience</dt><dd>{{ $recruitment->experience_level ?? 'Any' }}</dd></div>
                    <div><dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">Salary</dt>
                        <dd>
                            @if ($recruitment->salary_min || $recruitment->salary_max)
                                {{ $recruitment->salary_currency }} {{ number_format((float) $recruitment->salary_min, 0) }}–{{ number_format((float) $recruitment->salary_max, 0) }}
                            @else — @endif
                        </dd>
                    </div>
                    <div><dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">Needed by</dt><dd>{{ $recruitment->needed_by?->format('M d, Y') ?? 'Flexible' }}</dd></div>
                    <div><dt class="text-xs uppercase tracking-widest text-gray-400 mb-1">JD attachment</dt>
                        <dd>
                            @if ($recruitment->jd_file_path)
                                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($recruitment->jd_file_path) }}" target="_blank" class="text-[#1AAD94] font-semibold">Download</a>
                            @else <span class="text-gray-400">None</span> @endif
                        </dd>
                    </div>
                </dl>
                @if (! empty($recruitment->skills_list))
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <p class="text-xs uppercase tracking-widest text-gray-400 mb-2">Skills</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($recruitment->skills_list as $skill)
                                <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-[#4B5563] text-xs font-medium rounded-full">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="mt-5 pt-5 border-t border-gray-100">
                    <p class="text-xs uppercase tracking-widest text-gray-400 mb-2">Description</p>
                    <p class="text-sm text-[#4B5563] whitespace-pre-line">{{ $recruitment->description }}</p>
                </div>
            </div>

            {{-- Candidates tab --}}
            <div x-show="tab === 'candidates'" x-cloak class="p-6">
                <div class="flex flex-wrap gap-2 mb-5">
                    <button type="button" @click="attachOpen = true" class="inline-flex items-center gap-2 px-4 py-2 bg-[#073057] text-white rounded-lg text-sm font-semibold hover:brightness-110">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Attach platform candidate
                    </button>
                    <button type="button" @click="uploadOpen = true" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        Upload external CV
                    </button>
                </div>

                @if ($recruitment->candidates->isEmpty())
                    <div class="text-sm text-gray-500 text-center py-8">No candidates attached yet.</div>
                @else
                    <div class="space-y-3">
                        @foreach ($recruitment->candidates as $cand)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div>
                                        <p class="font-semibold text-[#0A1929]">{{ $cand->displayName() }}</p>
                                        <p class="text-xs text-gray-500">{{ $cand->displayEmail() ?? 'No email' }} @if ($cand->external_phone) &middot; {{ $cand->external_phone }} @endif</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $cand->isPlatformCandidate() ? 'Platform candidate' : 'External CV' }} &middot; Decision: <strong>{{ \App\Models\RecruitmentRequestCandidate::DECISIONS[$cand->employer_decision] }}</strong></p>
                                    </div>
                                    <form method="POST" action="{{ route('admin.recruitment-requests.remove-candidate', [$recruitment, $cand]) }}" onsubmit="return confirm('Remove this candidate from the delivery?');">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-600 hover:text-red-700">Remove</button>
                                    </form>
                                </div>
                                @if ($cand->summary)
                                    <p class="text-sm text-[#4B5563] mt-2">{{ $cand->summary }}</p>
                                @endif
                                @if ($cand->external_cv_path)
                                    <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($cand->external_cv_path) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-semibold text-[#1AAD94] hover:text-[#0F8B75] mt-2">Download CV &rarr;</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Admin notes tab --}}
            <div x-show="tab === 'admin'" x-cloak class="p-6">
                <form method="POST" action="{{ route('admin.recruitment-requests.update', $recruitment) }}" class="space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">
                            @foreach (\App\Models\RecruitmentRequest::STATUSES as $val => $label)
                                <option value="{{ $val }}" {{ $recruitment->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Internal notes (admin-only)</label>
                        <textarea name="admin_notes" rows="6" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">{{ $recruitment->admin_notes }}</textarea>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-[#073057] text-white rounded-lg text-sm font-semibold hover:brightness-110">Save</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <aside class="space-y-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h4 class="text-sm font-bold text-[#0A1929] mb-3">Requester</h4>
            <p class="text-sm font-semibold text-[#0A1929]">{{ $recruitment->requester?->name ?? '—' }}</p>
            <p class="text-xs text-gray-500">{{ $recruitment->requester?->email ?? '' }}</p>
            <p class="text-xs text-gray-500 mt-1">Company: {{ $recruitment->company?->name ?? '—' }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-3">
            <h4 class="text-sm font-bold text-[#0A1929]">Quote &amp; Order</h4>
            @if ($recruitment->order)
                <p class="text-xs text-gray-500">Order #{{ $recruitment->order->order_number }}</p>
                <p class="text-xl font-extrabold text-[#073057]">{{ $recruitment->salary_currency }} {{ number_format($recruitment->quoted_amount, 2) }}</p>
                <a href="{{ route('admin.orders.show', $recruitment->order_id) }}" class="inline-block text-sm font-semibold text-[#1AAD94] hover:text-[#0F8B75]">Open order &rarr;</a>
            @else
                <p class="text-sm text-gray-500">No quote issued yet.</p>
                <button type="button" @click="quoteOpen = true" class="w-full px-4 py-2.5 bg-[#1AAD94] text-white rounded-lg text-sm font-semibold hover:brightness-110">Issue Quote</button>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h4 class="text-sm font-bold text-[#0A1929] mb-3">Send notification</h4>
            <button type="button" @click="notifyOpen = true" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">Pick a template &amp; send</button>
        </div>
    </aside>

    {{-- Issue Quote Modal --}}
    <div x-show="quoteOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="quoteOpen = false">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-lg font-bold text-[#0A1929] mb-4">Issue a quote</h3>
            <form method="POST" action="{{ route('admin.recruitment-requests.quote', $recruitment) }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Amount</label>
                        <input type="number" name="quoted_amount" step="0.01" min="0" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Currency</label>
                        <select name="currency" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none">
                            @foreach (['USD', 'EUR', 'GBP', 'NGN', 'AED'] as $cur)
                                <option value="{{ $cur }}" {{ $recruitment->salary_currency === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Note to employer (optional)</label>
                    <textarea name="quote_note" rows="3" placeholder="Includes 5 candidate CVs delivered within 7 business days..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="quoteOpen = false" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-600">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#073057] text-white rounded-lg text-sm font-semibold hover:brightness-110">Issue &amp; email</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Attach Platform Candidate Modal --}}
    <div x-show="attachOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="attachOpen = false">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-lg font-bold text-[#0A1929] mb-4">Attach a platform candidate</h3>
            <form method="POST" action="{{ route('admin.recruitment-requests.attach-candidate', $recruitment) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Candidate ID</label>
                    <input type="number" name="candidate_id" required min="1" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                    <p class="mt-1 text-xs text-gray-400">Find IDs at <a href="{{ route('admin.users') }}" class="text-[#1AAD94]">admin/users</a> (look up the candidate, then their associated candidate record).</p>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Why this candidate (optional)</label>
                    <textarea name="summary" rows="3" placeholder="STCW certified, 8 years container vessel experience..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="attachOpen = false" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-600">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#073057] text-white rounded-lg text-sm font-semibold hover:brightness-110">Attach</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Upload External CV Modal --}}
    <div x-show="uploadOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="uploadOpen = false">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-lg font-bold text-[#0A1929] mb-4">Upload external CV</h3>
            <form method="POST" action="{{ route('admin.recruitment-requests.upload-cv', $recruitment) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Candidate name</label>
                    <input type="text" name="external_name" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Email</label>
                        <input type="email" name="external_email" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Phone</label>
                        <input type="text" name="external_phone" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">CV file</label>
                    <input type="file" name="cv_file" accept=".pdf,.doc,.docx" required class="block w-full text-sm text-[#4B5563] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-[#073057] file:text-white file:font-semibold" />
                    <p class="mt-1 text-xs text-gray-400">PDF, DOC, or DOCX up to 5 MB.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Notes (optional)</label>
                    <textarea name="summary" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="uploadOpen = false" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-600">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#073057] text-white rounded-lg text-sm font-semibold hover:brightness-110">Upload</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Send Notification Modal --}}
    <div x-show="notifyOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="notifyOpen = false">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-lg font-bold text-[#0A1929] mb-4">Send notification to {{ $recruitment->requester?->email }}</h3>
            <form method="POST" action="{{ route('admin.recruitment-requests.notify', $recruitment) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Template</label>
                    <select name="template_key" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none">
                        <option value="">— Select —</option>
                        @foreach ($jobNotificationTemplates as $tpl)
                            <option value="{{ $tpl->key }}">{{ $tpl->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Personal message (optional)</label>
                    <textarea name="message" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="notifyOpen = false" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-600">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#073057] text-white rounded-lg text-sm font-semibold hover:brightness-110">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
