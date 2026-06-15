@extends('admin.layouts.app')

@section('title', 'New Email Template')

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
<div class="max-w-6xl mx-auto" x-data="templateCreator()">
    <div class="mb-6">
        <a href="{{ route('admin.email-templates.index') }}" class="text-xs font-semibold text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-2">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to templates
        </a>
        <h1 class="text-2xl font-bold text-[#0A1929]">New Email Template</h1>
        <p class="text-sm text-gray-500 mt-1">Create a custom template. It becomes selectable in the recruitment-request notification modal once you save it as Active.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <div class="grid lg:grid-cols-4 gap-6">
        <form method="POST" action="{{ route('admin.email-templates.store') }}" class="lg:col-span-3 bg-white rounded-xl border border-gray-200 p-6" @submit="syncEditor">
            @csrf

            <div class="grid sm:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Template Name (admin only)</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Interview shortlist update"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Category</label>
                    <input type="text" name="category" value="{{ old('category', 'Recruitment') }}" required list="template-categories"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                    <datalist id="template-categories">
                        @foreach ($categories as $category)
                            <option value="{{ $category }}"></option>
                        @endforeach
                    </datalist>
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Subject Line</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1AAD94] focus:border-[#1AAD94] outline-none" />
                <p class="mt-1 text-xs text-gray-400">You can use variables here too — e.g. <span class="font-mono">Update on @{{job_title}}</span></p>
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
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                           class="mt-1 w-4 h-4 rounded border-gray-300 text-[#1AAD94] focus:ring-[#1AAD94]" />
                    <div>
                        <div class="font-semibold text-[#0A1929]">Active</div>
                        <div class="text-sm text-gray-500">Only active templates can be sent and appear in the recruitment-request notification modal.</div>
                    </div>
                </label>
            </div>

            <div class="flex justify-end pt-5 border-t border-gray-100">
                <button type="submit" class="px-6 py-3 bg-[#073057] text-white rounded-lg font-semibold hover:brightness-110 transition shadow">
                    Create Template
                </button>
            </div>
        </form>

        {{-- Sidebar: variables --}}
        <aside class="bg-white rounded-xl border border-gray-200 p-5 h-fit">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Common Variables</h3>
            <p class="text-xs text-gray-500 mb-4">Click to copy. They're replaced with real values when the email is sent. Recruitment notifications also provide <span class="font-mono">job_title</span>, <span class="font-mono">company_name</span>, <span class="font-mono">message</span>, <span class="font-mono">request_url</span> and <span class="font-mono">candidate_count</span>.</p>
            <div class="space-y-1">
                @php
                    $vars = ['name', 'email', 'app_name', 'app_url', 'support_email', 'year', 'job_title', 'company_name', 'message', 'request_url', 'candidate_count'];
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
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
function templateCreator() {
    return {
        mode: 'rich',
        quill: null,
        htmlContent: @json(old('body_html', "<p>Hello {{ name }},</p>\n<p></p>\n<p>Kind regards,<br>{{ app_name }}</p>")),
        copiedVar: '',

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
            if (this.mode === 'rich' && m === 'html') {
                this.htmlContent = this.quill.root.innerHTML;
            } else if (this.mode === 'html' && m === 'rich') {
                this.quill.root.innerHTML = this.htmlContent;
            }
            this.mode = m;
        },

        syncEditor() {
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
