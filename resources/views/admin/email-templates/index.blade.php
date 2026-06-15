@extends('admin.layouts.app')

@section('title', 'Email Templates')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-[#0A1929]">Email Templates</h1>
            <p class="text-sm text-gray-500 mt-1">Edit the subject line and HTML body for every transactional and notification email the system sends.</p>
        </div>
        <a href="{{ route('admin.email-templates.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#073057] text-white rounded-lg text-sm font-semibold hover:brightness-110">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Template
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    @foreach ($templates as $category => $rows)
        <div class="mb-8">
            <h2 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">{{ $category }}</h2>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="text-left px-5 py-3">Template</th>
                            <th class="text-left px-5 py-3">Subject</th>
                            <th class="text-left px-5 py-3">Status</th>
                            <th class="text-right px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $template)
                            <tr class="border-t border-gray-100 hover:bg-gray-50">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-[#0A1929]">
                                        {{ $template->name }}
                                        @if ($template->is_custom)
                                            <span class="ml-1 align-middle text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-700">Custom</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400 font-mono">{{ $template->key }}</div>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-600">{{ $template->subject }}</td>
                                <td class="px-5 py-4">
                                    @if ($template->is_active)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-green-100 text-green-700">Active</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-500">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center justify-end gap-4">
                                        <form method="POST" action="{{ route('admin.email-templates.clone', $template) }}">
                                            @csrf
                                            <button type="submit" class="text-sm font-semibold text-gray-500 hover:text-[#073057]">Clone</button>
                                        </form>
                                        <a href="{{ route('admin.email-templates.edit', $template) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-[#1AAD94] hover:text-[#0F8B75]">
                                            Edit &rarr;
                                        </a>
                                        @if ($template->is_custom)
                                            <form method="POST" action="{{ route('admin.email-templates.destroy', $template) }}" onsubmit="return confirm('Delete this custom template? This cannot be undone.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-sm font-semibold text-red-500 hover:text-red-600">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endsection
