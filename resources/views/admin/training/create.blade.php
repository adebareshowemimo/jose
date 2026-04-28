@extends('admin.layouts.app')

@section('title', 'New Training Program')
@section('page-title', 'New Program')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <a href="{{ route('admin.training.index') }}" class="text-xs font-semibold text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-2">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to programs
            </a>
            <h1 class="text-2xl font-bold text-[#0A1929]">Create a new program</h1>
            <p class="text-sm text-gray-500 mt-1">Define a training course or apprenticeship — pricing, schedule, curriculum.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold mb-1">Please fix the highlighted fields.</p>
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.training.store') }}" enctype="multipart/form-data" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 md:p-8 space-y-5 max-w-5xl">
        @csrf
        @include('admin.training.partials.form', ['program' => null])
        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.training.index') }}" class="px-5 py-2.5 text-sm text-gray-600 hover:text-gray-900">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-[#1AAD94] hover:brightness-110 text-white text-sm font-bold uppercase tracking-widest rounded-lg shadow transition">
                Create Program
            </button>
        </div>
    </form>
@endsection
