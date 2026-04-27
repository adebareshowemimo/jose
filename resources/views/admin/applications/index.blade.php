@extends('admin.layouts.app')

@section('title', 'Applications')
@section('page-title', 'Job Applications')

@section('content')
@php
    $jobTemplates = \App\Models\EmailTemplate::where('category', 'Job Notification')
        ->where('is_active', true)
        ->orderBy('name')
        ->get(['key', 'name', 'subject']);
@endphp

<div x-data="bulkApplications()">
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                    <option value="">All</option>
                    @foreach(['pending', 'reviewed', 'shortlisted', 'rejected', 'hired'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-[#073057] text-white text-sm font-medium rounded-lg hover:bg-[#073057]/90">Filter</button>
            @if(request('status'))
                <a href="{{ route('admin.applications') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Clear</a>
            @endif
        </form>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('warning'))
        <div class="mb-4 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 text-sm">{{ session('warning') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Bulk action bar --}}
    <div x-show="selected.length > 0" x-cloak
         class="mb-4 flex flex-wrap items-center justify-between gap-3 bg-[#073057] text-white rounded-xl p-3 px-5">
        <div class="text-sm">
            <span class="font-bold" x-text="selected.length"></span> application(s) selected
        </div>
        <div class="flex gap-2">
            <button type="button" @click="modalOpen = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#1AAD94] rounded-lg text-sm font-semibold hover:brightness-110">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Send notification
            </button>
            <button type="button" @click="selected = []" class="px-4 py-2 text-sm text-white/70 hover:text-white">Clear</button>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-4 py-3 w-10">
                            <input type="checkbox" @change="toggleAll($event)"
                                   :checked="selected.length > 0 && selected.length === pageIds.length"
                                   class="rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]" />
                        </th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Candidate</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Job</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Company</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Status</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Applied</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($applications as $app)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <input type="checkbox" value="{{ $app->id }}" x-model="selected"
                                       class="rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]" />
                            </td>
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-900">{{ $app->candidate?->user?->name ?? '—' }}</p>
                                <p class="text-xs text-gray-500">{{ $app->candidate?->user?->email ?? '' }}</p>
                            </td>
                            <td class="px-5 py-3 text-gray-700">{{ $app->jobListing?->title ?? '—' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $app->jobListing?->company?->name ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <span class="text-xs px-2 py-1 rounded-full
                                    {{ $app->status === 'hired' ? 'bg-green-100 text-green-700' : ($app->status === 'shortlisted' ? 'bg-blue-100 text-blue-700' : ($app->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')) }}">
                                    {{ ucfirst($app->status ?? 'pending') }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500">{{ $app->created_at?->format('M d, Y') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No applications found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($applications->hasPages())
            <div class="px-5 py-3 border-t border-gray-200">{{ $applications->links() }}</div>
        @endif
    </div>

    {{-- Send-notification modal --}}
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="modalOpen = false">
        <div class="bg-white rounded-xl max-w-2xl w-full p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold text-[#0A1929] mb-1">Send notification to <span x-text="selected.length"></span> applicant(s)</h3>
            <p class="text-sm text-gray-500 mb-5">Pick a template; the system will substitute job and candidate variables for each recipient.</p>

            <form method="POST" action="{{ route('admin.applications.send-notification') }}" x-data="{ tplKey: '' }">
                @csrf
                <template x-for="id in selected" :key="id">
                    <input type="hidden" name="application_ids[]" :value="id">
                </template>

                <div class="mb-5">
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Template</label>
                    <select name="template_key" required x-model="tplKey"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">
                        <option value="">— Select a template —</option>
                        @foreach ($jobTemplates as $tpl)
                            <option value="{{ $tpl->key }}">{{ $tpl->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Personal message <span class="text-gray-400 font-normal normal-case">(optional, replaces <span class="font-mono">@{{message}}</span>)</span></label>
                    <textarea name="message" rows="4" placeholder="A personal note to include in the email..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none"></textarea>
                </div>

                <div x-show="tplKey === 'application.interview'" x-cloak class="grid md:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Interview date / time</label>
                        <input type="text" name="interview_date" placeholder="Tue 5 May 2026, 14:00 GMT"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Location / link</label>
                        <input type="text" name="interview_location" placeholder="Microsoft Teams (link to follow)"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Also update application status</label>
                    <select name="update_status"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none">
                        <option value="">Don't change status</option>
                        <option value="reviewed">Reviewed</option>
                        <option value="shortlisted">Shortlisted</option>
                        <option value="interviewed">Interviewed</option>
                        <option value="offered">Offered</option>
                        <option value="hired">Hired</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div class="rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 text-xs mb-5">
                    Emails will be sent immediately. Make sure your SMTP settings are correctly configured.
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-[#073057] text-white rounded-lg text-sm font-semibold hover:brightness-110">
                        Send to <span x-text="selected.length"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function bulkApplications() {
    return {
        selected: [],
        modalOpen: false,
        pageIds: @json($applications->pluck('id')),
        toggleAll(e) {
            this.selected = e.target.checked ? [...this.pageIds] : [];
        },
    }
}
</script>
@endsection
