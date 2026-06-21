@extends('admin.layouts.app')

@section('title', $event->title)
@section('page-title', 'Event Details')

@section('content')
    @php
        $currentTemplate = $event->reminder_template_key ?: \App\Models\Event::DEFAULT_REMINDER_TEMPLATE;
        $currentFrequency = $event->reminder_repeat_days ? 'repeat' : 'once';
    @endphp

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('admin.events.index') }}" class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-gray-900 hover:bg-gray-50" title="Back to events">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="min-w-0">
                <h1 class="text-2xl font-bold text-[#0A1929] truncate">{{ $event->title }}</h1>
                <p class="text-sm text-gray-500">{{ $event->display_date }} · {{ $event->location }}</p>
            </div>
        </div>
        <a href="{{ route('admin.events.registrations', $event) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#073057] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-lg shadow transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Attendees
        </a>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Event summary --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="h-40 w-full flex items-center justify-center {{ $event->image_url ? 'bg-gray-100' : 'bg-gradient-to-br from-[#073057] via-[#0a4275] to-[#1AAD94]' }}">
                    @if ($event->image_url)
                        <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                    @else
                        <svg class="w-10 h-10 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    @endif
                </div>
                <div class="p-4 space-y-3 text-sm">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $event->category === 'hosted' ? 'bg-[#1AAD94]/10 text-[#158f7a]' : 'bg-blue-100 text-blue-700' }}">{{ $event->category === 'hosted' ? 'JCL hosted' : 'Industry' }}</span>
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold bg-gray-100 text-gray-700">{{ $event->type }}</span>
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ in_array($event->status, ['upcoming', 'active']) ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($event->status) }}</span>
                    </div>
                    <dl class="space-y-2 text-gray-600">
                        <div class="flex justify-between gap-3"><dt class="text-gray-400">Date</dt><dd class="font-medium text-gray-800 text-right">{{ $event->display_date }}</dd></div>
                        @if ($event->starts_at)
                            <div class="flex justify-between gap-3"><dt class="text-gray-400">Starts</dt><dd class="font-medium text-gray-800 text-right">{{ $event->starts_at->format('D, M j, Y') }}</dd></div>
                        @endif
                        <div class="flex justify-between gap-3"><dt class="text-gray-400">Location</dt><dd class="font-medium text-gray-800 text-right">{{ $event->location }}</dd></div>
                        @if ($event->capacity)
                            <div class="flex justify-between gap-3"><dt class="text-gray-400">Capacity</dt><dd class="font-medium text-gray-800 text-right">{{ $event->seats_sold }} / {{ $event->capacity }}</dd></div>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
                    <p class="text-2xl font-bold text-[#0A1929]">{{ $event->registrations_count }}</p>
                    <p class="text-[11px] uppercase tracking-wider text-gray-400 mt-0.5">Registered</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
                    <p class="text-2xl font-bold text-[#1AAD94]">{{ $event->active_registrations_count }}</p>
                    <p class="text-[11px] uppercase tracking-wider text-gray-400 mt-0.5">Remindable</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
                    <p class="text-2xl font-bold text-[#073057]">{{ $event->reminded_registrations_count }}</p>
                    <p class="text-[11px] uppercase tracking-wider text-gray-400 mt-0.5">Reminded</p>
                </div>
            </div>
        </div>

        {{-- Reminder configuration --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm"
                 x-data="{
                    enabled: {{ $event->reminders_enabled ? 'true' : 'false' }},
                    frequency: '{{ $currentFrequency }}',
                    lead: {{ (int) ($event->reminder_lead_days ?? 7) }},
                    repeat: {{ (int) ($event->reminder_repeat_days ?? 2) }},
                    max: {{ (int) ($event->reminder_max_count ?? 3) }},
                    get schedule() {
                        const lead = Math.max(0, parseInt(this.lead) || 0);
                        const max = Math.max(1, parseInt(this.max) || 1);
                        if (this.frequency === 'once') return [lead];
                        const step = Math.max(1, parseInt(this.repeat) || 1);
                        const days = [];
                        for (let d = lead; d >= 0 && days.length < max; d -= step) days.push(d);
                        return days;
                    },
                    label(d) { return d === 0 ? 'on the day' : d + 'd before'; }
                 }">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-[#0A1929]">Email reminders</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Automatically remind registered attendees before this event.</p>
                    </div>
                    <a href="{{ route('admin.email-templates.index') }}" class="text-xs font-semibold text-[#1AAD94] hover:underline">Edit templates</a>
                </div>

                <form method="POST" action="{{ route('admin.events.reminders.update', $event) }}" class="p-5 space-y-5">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Enable toggle --}}
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="reminders_enabled" value="0">
                        <input type="checkbox" name="reminders_enabled" value="1" x-model="enabled"
                               class="w-5 h-5 rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]">
                        <span class="text-sm font-semibold text-gray-800">Send email reminders for this event</span>
                    </label>

                    <div :class="enabled ? '' : 'opacity-50 pointer-events-none'" class="space-y-5 transition">
                        {{-- Template --}}
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 block">Template</label>
                            <select name="reminder_template_key" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                                @foreach ($reminderTemplates as $tpl)
                                    <option value="{{ $tpl->key }}" @selected($currentTemplate === $tpl->key)>{{ $tpl->name }} ({{ $tpl->category }})</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Defaults to the “Event Reminder” template. Edit content under Email Templates.</p>
                        </div>

                        {{-- Lead time --}}
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 block">Start sending</label>
                            <div class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="number" name="reminder_lead_days" x-model="lead" min="0" max="365"
                                       class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94]">
                                <span>day(s) before the event</span>
                            </div>
                        </div>

                        {{-- Frequency --}}
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 block">Then repeat</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="reminder_frequency" value="once" x-model="frequency" class="text-[#1AAD94] focus:ring-[#1AAD94]">
                                    <span>Once only</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="reminder_frequency" value="repeat" x-model="frequency" class="text-[#1AAD94] focus:ring-[#1AAD94]">
                                    <span>Every</span>
                                    <input type="number" name="reminder_repeat_days" x-model="repeat" min="1" max="90" :disabled="frequency !== 'repeat'"
                                           class="w-20 px-3 py-1.5 border border-gray-300 rounded-lg disabled:bg-gray-100 focus:ring-2 focus:ring-[#1AAD94]">
                                    <span>day(s) until the event</span>
                                </label>
                            </div>
                        </div>

                        {{-- Max count --}}
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 block">Max reminders per attendee</label>
                            <input type="number" name="reminder_max_count" x-model="max" min="1" max="20"
                                   class="w-24 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                        </div>

                        {{-- Live preview --}}
                        <div class="rounded-lg bg-gray-50 border border-gray-200 p-3">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1.5">Schedule preview</p>
                            <p class="text-sm text-gray-700">
                                <span x-text="schedule.length"></span> reminder(s):
                                <span class="font-medium text-[#073057]" x-text="schedule.map(label).join(', ')"></span>
                            </p>
                            <p class="text-[11px] text-gray-400 mt-1">Sent by the daily scheduler to the {{ $event->active_registrations_count }} remindable attendee(s). Reminders are day-granular (events have no set time).</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                        <button type="submit" class="px-5 py-2.5 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-lg shadow transition">Save reminders</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
