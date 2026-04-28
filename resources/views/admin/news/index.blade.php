@extends('admin.layouts.app')

@section('title', 'News')
@section('page-title', 'News')

@section('content')
    {{-- Page header --}}
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#0A1929]">News & Articles</h1>
            <p class="text-sm text-gray-500 mt-1">Publish editorial content, industry insights and company announcements.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white border border-gray-200 text-xs text-gray-500">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                <span class="font-semibold text-gray-700">{{ $articles->total() }}</span> total
            </span>
            <a href="{{ route('admin.news.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-lg shadow transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Add Article
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div>
        {{-- List + filters --}}
        <div class="space-y-5">
            @if($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-semibold mb-1">Please fix the highlighted article details.</p>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Filter bar --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <form method="GET" class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 block">Search</label>
                        <div class="relative">
                            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Title, author, category..."
                                class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 block">Category</label>
                        <select name="category" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                            <option value="">All</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 block">Status</label>
                        <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                            <option value="">All</option>
                            @foreach(['published', 'draft', 'archived'] as $status)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-[#073057] text-white text-sm font-semibold rounded-lg hover:brightness-110">Filter</button>
                    @if(request()->hasAny(['search', 'category', 'status']))
                        <a href="{{ route('admin.news.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Clear</a>
                    @endif
                </form>
            </div>

            {{-- Article cards --}}
            <div class="space-y-3">
                @forelse($articles as $article)
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition overflow-hidden" x-data="{ editing: false }">
                        <div class="flex flex-col sm:flex-row gap-4 p-4">
                            {{-- Thumbnail --}}
                            <div class="shrink-0 w-full sm:w-44 h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg overflow-hidden flex items-center justify-center relative">
                                @if ($article->image_url)
                                    <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="text-gray-400 flex flex-col items-center text-xs">
                                        <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        No image
                                    </div>
                                @endif
                                @if ($article->is_featured)
                                    <span class="absolute top-1.5 left-1.5 inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full bg-amber-100 text-amber-700">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.539 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.462a1 1 0 00.95-.69l1.07-3.292z"/></svg>
                                        Featured
                                    </span>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-start justify-between gap-3 mb-2">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                            <span class="inline-flex rounded-full bg-[#1AAD94]/10 px-2.5 py-0.5 text-[11px] font-semibold text-[#158f7a]">
                                                {{ $article->category }}
                                            </span>
                                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold
                                                {{ $article->status === 'published' ? 'bg-green-100 text-green-700' : ($article->status === 'draft' ? 'bg-gray-100 text-gray-600' : 'bg-amber-100 text-amber-700') }}">
                                                {{ ucfirst($article->status) }}
                                            </span>
                                        </div>
                                        <h3 class="font-bold text-[#0A1929] text-base leading-snug">{{ $article->title }}</h3>
                                        <p class="mt-0.5 text-xs text-gray-500 font-mono">/{{ $article->slug }}</p>
                                    </div>
                                </div>

                                <p class="text-sm text-gray-600 leading-relaxed line-clamp-2">{{ $article->excerpt }}</p>

                                <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 text-xs text-gray-500">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            {{ $article->author }}
                                        </span>
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            {{ $article->published_at?->format('M d, Y') ?? 'Not published' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('news.detail', $article->slug) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-gray-600 hover:bg-gray-100 font-semibold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14"/></svg>
                                            View
                                        </a>
                                        <button type="button" @click="editing = !editing" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[#1AAD94] hover:bg-[#1AAD94]/10 font-semibold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span x-text="editing ? 'Close' : 'Edit'"></span>
                                        </button>
                                        <form method="POST" action="{{ route('admin.news.destroy', $article) }}" onsubmit="return confirm('Delete this article? This cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-red-600 hover:bg-red-50 font-semibold">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M11 3a2 2 0 00-2 2v2h6V5a2 2 0 00-2-2h-2z"/></svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Inline edit panel --}}
                        <div x-show="editing" x-cloak x-transition class="border-t border-gray-200 bg-gray-50/60 p-5">
                            <form method="POST" action="{{ route('admin.news.update', $article) }}" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                @method('PUT')
                                @include('admin.news.partials.form', ['article' => $article])
                                <div class="flex justify-end gap-2 pt-2 border-t border-gray-200">
                                    <button type="button" @click="editing = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Cancel</button>
                                    <button type="submit" class="px-5 py-2 bg-[#073057] text-white text-sm font-semibold rounded-lg hover:brightness-110">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl border border-dashed border-gray-300 p-12 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2M5 8h4m-4 4h4"/></svg>
                        <p class="text-sm text-gray-500 mb-4">No articles yet.</p>
                        <a href="{{ route('admin.news.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-lg shadow transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Create your first article
                        </a>
                    </div>
                @endforelse
            </div>

            @if($articles->hasPages())
                <div class="bg-white rounded-xl border border-gray-200 px-5 py-3">{{ $articles->links() }}</div>
            @endif
        </div>
    </div>
@endsection
