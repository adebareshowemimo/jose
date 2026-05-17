@extends('admin.layouts.app')

@section('title', 'Training Categories')
@section('page-title', 'Training Categories')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <a href="{{ route('admin.training.index') }}" class="text-xs font-semibold text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-2">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to programs
            </a>
            <h1 class="text-2xl font-bold text-[#0A1929]">Training Categories</h1>
            <p class="text-sm text-gray-500 mt-1">Manage the categories admins assign to training programs. Each category gets its own public page at <code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded">/services/training/{slug}</code>.</p>
        </div>
        <a href="{{ route('admin.training-categories.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-lg shadow">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            New Category
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="space-y-3">
        @forelse($categories as $category)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition overflow-hidden" x-data="{ editing: false }">
                <div class="flex flex-col sm:flex-row gap-4 p-4">
                    <div class="shrink-0 w-full sm:w-44 h-32 rounded-lg overflow-hidden flex items-center justify-center relative {{ $category->hero_image_url ? 'bg-gray-100' : 'bg-gradient-to-br from-[#073057] via-[#0a4275] to-[#1AAD94]' }}">
                        @if ($category->hero_image_url)
                            <img src="{{ $category->hero_image_url }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-white px-2 text-center">
                                <iconify-icon icon="{{ $category->icon ?: 'lucide:layers' }}" class="text-3xl opacity-80"></iconify-icon>
                                <span class="text-[10px] font-bold uppercase tracking-wider opacity-80 mt-1">Category</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold bg-gray-100 text-gray-700">{{ $category->programs_count }} {{ Str::plural('program', $category->programs_count) }}</span>
                        </div>
                        <h3 class="font-bold text-[#0A1929] text-base leading-snug">{{ $category->name }}</h3>
                        <p class="text-xs text-gray-500 font-mono mt-0.5">/services/training/{{ $category->slug }}</p>
                        @if ($category->short_description)
                            <p class="text-sm text-gray-600 leading-relaxed mt-2 line-clamp-2">{{ $category->short_description }}</p>
                        @endif

                        <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-end gap-1">
                            <button type="button" @click="editing = !editing" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[#1AAD94] hover:bg-[#1AAD94]/10 font-semibold text-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span x-text="editing ? 'Close' : 'Edit'"></span>
                            </button>
                            <a href="{{ url('/services/training/'.$category->slug) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-gray-600 hover:bg-gray-100 font-semibold text-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14"/></svg>
                                View
                            </a>
                            <form method="POST" action="{{ route('admin.training-categories.destroy', $category) }}" onsubmit="return confirm('Delete this category? Programs assigned to it will block deletion.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-red-600 hover:bg-red-50 font-semibold text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M11 3a2 2 0 00-2 2v2h6V5a2 2 0 00-2-2h-2z"/></svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div x-show="editing" x-cloak x-transition class="border-t border-gray-200 bg-gray-50/60 p-5">
                    <form method="POST" action="{{ route('admin.training-categories.update', $category) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf @method('PUT')
                        @include('admin.training-categories.partials.form', ['category' => $category])
                        <div class="flex justify-end gap-2 pt-2 border-t border-gray-200">
                            <button type="button" @click="editing = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Cancel</button>
                            <button type="submit" class="px-5 py-2 bg-[#073057] text-white text-sm font-semibold rounded-lg hover:brightness-110">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-dashed border-gray-300 p-12 text-center">
                <iconify-icon icon="lucide:layers" class="text-4xl text-gray-300 mb-3"></iconify-icon>
                <p class="text-sm text-gray-500 mb-4">No categories yet.</p>
                <a href="{{ route('admin.training-categories.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-lg shadow transition">
                    Create First Category
                </a>
            </div>
        @endforelse
    </div>
@endsection
