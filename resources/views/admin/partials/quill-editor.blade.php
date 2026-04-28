{{--
    Reusable Quill editor includes for admin pages.
    Pushes CSS/JS once and exposes the Alpine `quillEditor()` factory.
    Use in form partials like:

        <div x-data="quillEditor({ id: 'unique-id', initial: $existingHtml })">
            ...rich/html toggle + #id div + textarea + hidden input...
        </div>
--}}
@once
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
    .ql-toolbar.ql-snow { border-color: #D1D5DB; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; background: #F9FAFB; }
    .ql-container.ql-snow { border-color: #D1D5DB; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem; min-height: 280px; font-size: 14px; font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; }
    .ql-editor { min-height: 280px; }
</style>
@endpush
@endonce

@once
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    function quillEditor({ id, initial }) {
        return {
            id,
            mode: 'rich',
            html: initial || '',
            quill: null,

            init() {
                this.$nextTick(() => {
                    if (! this.mountIfReady()) {
                        const el = document.getElementById(this.id);
                        if (el && 'IntersectionObserver' in window) {
                            const obs = new IntersectionObserver((entries) => {
                                entries.forEach((entry) => {
                                    if (entry.isIntersecting && this.mountIfReady()) {
                                        obs.disconnect();
                                    }
                                });
                            });
                            obs.observe(el);
                        }
                    }
                });
                this.$watch('mode', () => this.$nextTick(() => this.mountIfReady()));
            },

            mountIfReady() {
                if (this.quill) return true;
                if (this.mode !== 'rich') return false;
                const el = document.getElementById(this.id);
                if (!el || el.offsetParent === null) return false;
                this.quill = new Quill('#' + CSS.escape(this.id), {
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
                if (this.html) this.quill.root.innerHTML = this.html;
                this.quill.on('text-change', () => {
                    if (this.mode === 'rich') {
                        this.html = this.quill.root.innerHTML;
                    }
                });
                return true;
            },

            setMode(m) {
                if (this.mode === 'rich' && m === 'html' && this.quill) {
                    this.html = this.quill.root.innerHTML;
                } else if (this.mode === 'html' && m === 'rich' && this.quill) {
                    this.quill.root.innerHTML = this.html;
                }
                this.mode = m;
            },
        };
    }
</script>
@endpush
@endonce
