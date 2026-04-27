@extends('admin.layouts.app')

@section('title', 'Edit Template — ' . $template->name)

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
    .ql-toolbar.ql-snow, .ql-container.ql-snow { border-color: #D1D5DB; }
    .ql-toolbar.ql-snow { border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; background: #F9FAFB; }
    .ql-container.ql-snow { border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem; min-height: 380px; font-size: 14px; font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; }
    .ql-editor { min-height: 380px; }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto" x-data="templateEditor()">
    <div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
        <div>
            <a href="{{ route('admin.email-templates.index') }}" class="text-xs font-semibold text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-2">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to templates
            </a>
            <h1 class="text-2xl font-bold text-[#0A1929]">{{ $template->name }}</h1>
            <p class="text-xs text-gray-400 font-mono mt-1">{{ $template->key }} &middot; {{ $template->category }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.email-templates.preview', $template) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Preview
            </a>
            <button type="button" @click="testOpen = true"
                    class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Send test
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <div class="grid lg:grid-cols-4 gap-6">
        {{-- Main editor --}}
        <form method="POST" action="{{ route('admin.email-templates.update', $template) }}" class="lg:col-span-3 bg-white rounded-xl border border-gray-200 p-6" @submit="syncEditor">
            @csrf
            @method('PUT')

            <div class="mb-5">
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Template Name (admin only)</label>
                <input type="text" name="name" value="{{ old('name', $template->name) }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
            </div>

            <div class="mb-5">
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Subject Line</label>
                <input type="text" name="subject" value="{{ old('subject', $template->subject) }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                <p class="mt-1 text-xs text-gray-400">You can use variables here too — e.g. <span class="font-mono">Welcome @{{name}}</span></p>
            </div>

            <div class="mb-5">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500">Body</label>
                    <div class="flex gap-1 text-xs">
                        <button type="button" @click="setMode('rich')" :class="mode === 'rich' ? 'bg-[#1AAD94] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1 rounded font-semibold transition">Rich text</button>
                        <button type="button" @click="setMode('html')" :class="mode === 'html' ? 'bg-[#1AAD94] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1 rounded font-semibold transition">HTML source</button>
                    </div>
                </div>

                <div x-show="mode === 'rich'" x-cloak>
                    <div id="quill-editor"></div>
                </div>
                <div x-show="mode === 'html'" x-cloak>
                    <textarea x-ref="htmlSource" x-model="htmlContent" rows="18"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg font-mono text-xs focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none"></textarea>
                </div>

                <input type="hidden" name="body_html" :value="htmlContent">
            </div>

            <div class="mb-6">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ $template->is_active ? 'checked' : '' }}
                           class="mt-1 w-4 h-4 rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]" />
                    <div>
                        <div class="font-semibold text-[#0A1929]">Active</div>
                        <div class="text-sm text-gray-500">When unchecked, the system skips sending this email and logs a warning instead.</div>
                    </div>
                </label>
            </div>

            <div class="flex justify-end pt-5 border-t border-gray-100">
                <button type="submit" class="px-6 py-3 bg-[#073057] text-white rounded-lg font-semibold hover:brightness-110 transition shadow">
                    Save Template
                </button>
            </div>
        </form>

        {{-- Sidebar: variables --}}
        <aside class="bg-white rounded-xl border border-gray-200 p-5 h-fit">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Available Variables</h3>
            <p class="text-xs text-gray-500 mb-4">Click to copy. They'll be replaced with real values when the email is sent.</p>
            <div class="space-y-1">
                @php
                    $alwaysAvailable = ['name', 'email', 'app_name', 'app_url', 'support_email', 'year'];
                    $vars = array_unique(array_merge($alwaysAvailable, $template->variables ?? []));
                @endphp
                @foreach ($vars as $v)
                    @php $varTag = '{' . '{' . $v . '}' . '}'; @endphp
                    <button type="button" @click="copyVar('{{ $v }}')"
                            class="w-full text-left px-3 py-2 rounded-md bg-gray-50 hover:bg-[#1AAD94]/10 hover:text-[#0F8B75] text-xs font-mono text-gray-700 transition">
                        {{ $varTag }}
                    </button>
                @endforeach
            </div>
            <div x-show="copiedVar" x-cloak class="mt-3 text-xs text-[#0F8B75] font-semibold">
                Copied <span class="font-mono" x-text="copiedVar"></span> to clipboard.
            </div>
        </aside>
    </div>

    {{-- Send-test modal --}}
    <div x-show="testOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="testOpen = false">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-lg font-bold text-[#0A1929] mb-4">Send a test email</h3>
            <form method="POST" action="{{ route('admin.email-templates.send-test', $template) }}">
                @csrf
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Recipient</label>
                <input type="email" name="to" required value="{{ Auth::user()?->email }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                <p class="mt-2 text-xs text-gray-500">Sample variable values will be substituted in the body.</p>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" @click="testOpen = false" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[#073057] text-white rounded-lg text-sm font-semibold hover:brightness-110">Send Test</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
function templateEditor() {
    return {
        mode: 'rich',
        quill: null,
        htmlContent: @json(old('body_html', $template->body_html)),
        copiedVar: '',
        testOpen: false,

        init() {
            this.quill = new Quill('#quill-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ header: [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ color: [] }, { background: [] }],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        [{ align: [] }],
                        ['link', 'blockquote', 'code-block'],
                        ['clean'],
                    ],
                },
            });
            this.quill.root.innerHTML = this.htmlContent;
            this.quill.on('text-change', () => {
                if (this.mode === 'rich') {
                    this.htmlContent = this.quill.root.innerHTML;
                }
            });
        },

        setMode(m) {
            // Sync content between the two editors before switching.
            if (this.mode === 'rich' && m === 'html') {
                this.htmlContent = this.quill.root.innerHTML;
            } else if (this.mode === 'html' && m === 'rich') {
                this.quill.root.innerHTML = this.htmlContent;
            }
            this.mode = m;
        },

        syncEditor() {
            // Final sync before form POST.
            if (this.mode === 'rich') {
                this.htmlContent = this.quill.root.innerHTML;
            }
        },

        copyVar(v) {
            const text = '{' + '{' + v + '}' + '}';
            navigator.clipboard.writeText(text).then(() => {
                this.copiedVar = text;
                setTimeout(() => this.copiedVar = '', 2200);
            });
        },
    }
}
</script>
@endsection
