@extends('layouts.dashboard')

@section('title', 'CV Manager')
@section('page-title', 'CV Manager')

@section('sidebar-nav')
    @include('pages.dashboard.candidate.partials.sidebar')
@endsection

@section('content')
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#073057]">CV Manager</h2>
            <p class="text-[#6B7280]">Upload and manage your CV/Resume files</p>
        </div>
    </div>

    {{-- Upload Area --}}
    <form action="{{ route('user.cv.upload') }}" method="POST" enctype="multipart/form-data" id="cv-upload-form">
        @csrf
        <div class="bg-white rounded-xl border-2 border-dashed border-[#E5E7EB] p-8 text-center mb-6 hover:border-[#1AAD94] transition cursor-pointer" onclick="document.getElementById('cv-file-input').click()">
            <div class="w-16 h-16 bg-[#1AAD94]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-[#1AAD94]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            </div>
            <h3 class="font-semibold text-[#073057] mb-2">Drag and drop your CV here</h3>
            <p class="text-sm text-[#6B7280] mb-4">or click to browse files</p>
            <p class="text-xs text-[#9CA3AF]">Supported formats: PDF, DOC, DOCX · Max size: 5MB</p>
            <input type="file" name="cv_file" id="cv-file-input" accept=".pdf,.doc,.docx" class="hidden" onchange="document.getElementById('cv-upload-form').submit()" />
        </div>
        @error('cv_file')
            <p class="text-sm text-red-600 -mt-4 mb-4">{{ $message }}</p>
        @enderror
    </form>

    {{-- Uploaded CVs --}}
    <div class="space-y-4">
        <h3 class="font-semibold text-[#073057]">Your CVs ({{ $resumes->count() }})</h3>
        
        @forelse($resumes as $resume)
        @php
            $extension = pathinfo($resume->file_path, PATHINFO_EXTENSION);
            $isPdf = strtolower($extension) === 'pdf';
            $fileSize = file_exists(public_path($resume->file_path)) ? filesize(public_path($resume->file_path)) : 0;
            $fileSizeFormatted = $fileSize > 0 ? ($fileSize > 1048576 ? round($fileSize / 1048576, 1) . ' MB' : round($fileSize / 1024) . ' KB') : 'N/A';
        @endphp
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-5 flex items-center gap-4 hover:shadow-md transition">
            {{-- File Icon --}}
            <div class="w-14 h-14 rounded-xl flex items-center justify-center {{ $isPdf ? 'bg-red-100' : 'bg-blue-100' }}">
                @if($isPdf)
                <svg class="w-7 h-7 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM8.5 13H10v4.5H8.5V13zm4.5 0h1.5v3H16v1.5h-3V13zm-6 0h1.5v1.5H5.5V16H7v1.5H5.5V13H7v.5z"/></svg>
                @else
                <svg class="w-7 h-7 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM8 17H6v-2h2v2zm0-4H6v-2h2v2zm3 4H9v-2h2v2zm0-4H9v-2h2v2zm3 4h-2v-2h2v2zm0-4h-2v-2h2v2z"/></svg>
                @endif
            </div>
            
            {{-- File Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <h4 class="font-semibold text-[#073057] truncate">{{ $resume->title }}</h4>
                    @if($resume->is_default)
                    <span class="px-2 py-0.5 bg-[#1AAD94] text-white text-xs font-medium rounded">Default</span>
                    @endif
                </div>
                <div class="flex items-center gap-4 text-sm text-[#6B7280]">
                    <span>{{ $fileSizeFormatted }}</span>
                    <span>Uploaded: {{ $resume->created_at->format('M d, Y') }}</span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                @if(!$resume->is_default)
                <form action="{{ route('user.cv.default', $resume) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm text-[#1AAD94] hover:bg-[#1AAD94]/10 rounded-lg transition cursor-pointer">Set as Default</button>
                </form>
                @endif
                <a href="{{ route('user.cv.download', $resume) }}" class="p-2 text-[#6B7280] hover:text-[#073057] hover:bg-[#F3F4F6] rounded-lg transition cursor-pointer" title="Download">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </a>
                <form action="{{ route('user.cv.delete', $resume) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this CV?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 text-[#6B7280] hover:text-red-600 hover:bg-red-50 rounded-lg transition cursor-pointer" title="Delete">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-12 text-center">
            <svg class="w-16 h-16 text-[#E5E7EB] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <h4 class="font-semibold text-[#073057] mb-2">No CVs Uploaded</h4>
            <p class="text-[#6B7280] mb-4">Upload your first CV to start applying for jobs.</p>
        </div>
        @endforelse
    </div>

    {{-- Tips --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mt-6">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <h4 class="font-semibold text-blue-800 mb-1">CV Tips for Maritime Jobs</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Include all your maritime certifications and their expiry dates</li>
                    <li>• List your sea service record with vessel types and gross tonnage</li>
                    <li>• Highlight any specialized training (STCW, GMDSS, etc.)</li>
                    <li>• Keep your CV updated with the latest certificates</li>
                </ul>
            </div>
        </div>
    </div>

    <style>
        button, [type="button"], [type="submit"], a.cursor-pointer { cursor: pointer; }
    </style>
@endsection
