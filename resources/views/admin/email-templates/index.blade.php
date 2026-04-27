@extends('admin.layouts.app')

@section('title', 'Email Templates')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#0A1929]">Email Templates</h1>
        <p class="text-sm text-gray-500 mt-1">Edit the subject line and HTML body for every transactional and notification email the system sends.</p>
    </div>

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
                                    <div class="font-semibold text-[#0A1929]">{{ $template->name }}</div>
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
                                    <a href="{{ route('admin.email-templates.edit', $template) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-[#1AAD94] hover:text-[#0F8B75]">
                                        Edit &rarr;
                                    </a>
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
