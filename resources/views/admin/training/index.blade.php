@extends('admin.layouts.app')

@section('title', 'Training & Apprenticeships')
@section('page-title', 'Training')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#0A1929]">Training & Apprenticeships</h1>
            <p class="text-sm text-gray-500 mt-1">Sell training programs and apprenticeship enrolments. Each program is its own product line.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white border border-gray-200 text-xs text-gray-500">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                <span class="font-semibold text-gray-700">{{ $programs->total() }}</span> programs
            </span>
            <a href="{{ route('admin.training.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-lg shadow transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Add Program
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
        <div class="space-y-5">
            @if($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-semibold mb-1">Please fix the highlighted fields.</p>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <form method="GET" class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 block">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Title, category..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94] focus:border-transparent">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 block">Type</label>
                        <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                            <option value="">All</option>
                            <option value="training" {{ request('type') === 'training' ? 'selected' : '' }}>Training</option>
                            <option value="apprenticeship" {{ request('type') === 'apprenticeship' ? 'selected' : '' }}>Apprenticeship</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 block">Status</label>
                        <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1AAD94]">
                            <option value="">All</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-[#073057] text-white text-sm font-semibold rounded-lg hover:brightness-110">Filter</button>
                    @if(request()->hasAny(['search', 'type', 'status']))
                        <a href="{{ route('admin.training.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Clear</a>
                    @endif
                </form>
            </div>

            <div class="space-y-3">
                @forelse($programs as $program)
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition overflow-hidden" x-data="{ editing: false }">
                        <div class="flex flex-col sm:flex-row gap-4 p-4">
                            <div class="shrink-0 w-full sm:w-44 h-32 rounded-lg overflow-hidden flex items-center justify-center relative {{ $program->image_url ? 'bg-gray-100' : 'bg-gradient-to-br from-[#073057] via-[#0a4275] to-[#1AAD94]' }}">
                                @if ($program->image_url)
                                    <img src="{{ $program->image_url }}" alt="{{ $program->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-white px-2 text-center">
                                        <svg class="w-8 h-8 mb-1 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                                        <span class="text-[10px] font-bold uppercase tracking-wider opacity-80">{{ $program->type === 'apprenticeship' ? 'Apprenticeship' : 'Training' }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $program->type === 'apprenticeship' ? 'bg-purple-100 text-purple-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ ucfirst($program->type) }}
                                    </span>
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $program->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $program->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if ($program->category)
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold bg-gray-100 text-gray-700">{{ $program->category }}</span>
                                    @endif
                                </div>
                                <h3 class="font-bold text-[#0A1929] text-base leading-snug">{{ $program->title }}</h3>
                                <p class="text-xs text-gray-500 font-mono mt-0.5">/{{ $program->slug }}</p>
                                @if ($program->short_description)
                                    <p class="text-sm text-gray-600 leading-relaxed mt-2 line-clamp-2">{{ $program->short_description }}</p>
                                @endif

                                <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 text-xs text-gray-500">
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <span class="font-bold text-[#073057] text-sm">{{ $program->currency }} {{ number_format((float) $program->price, 2) }}</span>
                                        @if ($program->duration)
                                            <span class="inline-flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> {{ $program->duration }}</span>
                                        @endif
                                        <span class="inline-flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> {{ $program->enrolments_count }} enrolled</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="editing = !editing" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[#1AAD94] hover:bg-[#1AAD94]/10 font-semibold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span x-text="editing ? 'Close' : 'Edit'"></span>
                                        </button>
                                        <a href="{{ route('training.show', $program->slug) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-gray-600 hover:bg-gray-100 font-semibold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14"/></svg>
                                            View
                                        </a>
                                        <form method="POST" action="{{ route('admin.training.destroy', $program) }}" onsubmit="return confirm('Delete this program? This cannot be undone.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-red-600 hover:bg-red-50 font-semibold">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M11 3a2 2 0 00-2 2v2h6V5a2 2 0 00-2-2h-2z"/></svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div x-show="editing" x-cloak x-transition class="border-t border-gray-200 bg-gray-50/60 p-5">
                            <form method="POST" action="{{ route('admin.training.update', $program) }}" enctype="multipart/form-data" class="space-y-4">
                                @csrf @method('PUT')
                                @include('admin.training.partials.form', ['program' => $program])
                                <div class="flex justify-end gap-2 pt-2 border-t border-gray-200">
                                    <button type="button" @click="editing = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Cancel</button>
                                    <button type="submit" class="px-5 py-2 bg-[#073057] text-white text-sm font-semibold rounded-lg hover:brightness-110">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl border border-dashed border-gray-300 p-12 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                        <p class="text-sm text-gray-500 mb-4">No programs yet.</p>
                        <a href="{{ route('admin.training.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-lg shadow transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Create your first program
                        </a>
                    </div>
                @endforelse
            </div>

            @if($programs->hasPages())
                <div class="bg-white rounded-xl border border-gray-200 px-5 py-3">{{ $programs->links() }}</div>
            @endif
        </div>
    </div>
@endsection
