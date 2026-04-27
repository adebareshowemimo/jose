@extends('layouts.dashboard')

@section('title', 'Resume Builder')
@section('page-title', 'Resume Builder')

@section('sidebar-nav')
    @include('pages.dashboard.candidate.partials.sidebar')
@endsection

@section('content')
<div x-data="{
    showSummaryModal: false,
    showExpModal: false,
    showEduModal: false,
    showCertModal: false,
    showSkillsModal: false,
    editingExp: null,
    editingEdu: null,
    editingCert: null,
    
    openExpModal(exp = null) {
        this.editingExp = exp;
        this.showExpModal = true;
    },
    openEduModal(edu = null) {
        this.editingEdu = edu;
        this.showEduModal = true;
    },
    openCertModal(cert = null) {
        this.editingCert = cert;
        this.showCertModal = true;
    }
}">
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#073057]">Resume Builder</h2>
            <p class="text-[#6B7280]">Build your professional resume step by step</p>
        </div>
        <a href="{{ route('user.candidate.profile') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#073057] hover:bg-[#0a4275] text-white text-sm font-medium rounded-xl transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit Full Profile
        </a>
    </div>

    {{-- Resume Sections --}}
    <div class="space-y-6">
        {{-- Personal Information --}}
        <div class="bg-white rounded-xl border border-[#E5E7EB] overflow-hidden">
            <div class="p-5 border-b border-[#E5E7EB] flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#1AAD94]/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#1AAD94]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-[#073057]">Personal Information</h3>
                        <p class="text-sm text-[#6B7280]">Your basic contact details</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('user.candidate.profile') }}?tab=basic" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-[#1AAD94] hover:bg-[#1AAD94]/10 rounded-lg transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                    <span class="w-6 h-6 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                </div>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-xs text-[#9CA3AF] uppercase tracking-wider">Full Name</span>
                        <p class="text-[#073057] font-medium">{{ $user->name ?? 'Not Set' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-[#9CA3AF] uppercase tracking-wider">Email</span>
                        <p class="text-[#073057] font-medium">{{ $user->email ?? 'Not Set' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-[#9CA3AF] uppercase tracking-wider">Phone</span>
                        <p class="text-[#073057] font-medium">{{ $candidate->phone ?? 'Not Set' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-[#9CA3AF] uppercase tracking-wider">Location</span>
                        <p class="text-[#073057] font-medium">{{ $candidate->location?->name ?? 'Not Set' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Professional Summary --}}
        <div class="bg-white rounded-xl border border-[#E5E7EB] overflow-hidden">
            <div class="p-5 border-b border-[#E5E7EB] flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#1AAD94]/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#1AAD94]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-[#073057]">Professional Summary</h3>
                        <p class="text-sm text-[#6B7280]">A brief overview of your career</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="showSummaryModal = true" type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-[#1AAD94] hover:bg-[#1AAD94]/10 rounded-lg transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </button>
                    @if($candidate->bio)
                    <span class="w-6 h-6 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    @else
                    <span class="w-6 h-6 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                    @endif
                </div>
            </div>
            <div class="p-5">
                @if($candidate->bio)
                <p class="text-[#4B5563]">{{ $candidate->bio }}</p>
                @else
                <p class="text-[#9CA3AF] italic">No professional summary added yet. Add a brief overview of your experience and career goals.</p>
                @endif
            </div>
        </div>

        {{-- Work Experience --}}
        <div class="bg-white rounded-xl border border-[#E5E7EB] overflow-hidden">
            <div class="p-5 border-b border-[#E5E7EB] flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#1AAD94]/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#1AAD94]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-[#073057]">Work Experience</h3>
                        <p class="text-sm text-[#6B7280]">Your employment history</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="openExpModal()" type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm bg-[#1AAD94] hover:bg-[#158f7a] text-white rounded-lg transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add
                    </button>
                    @php $experiences = collect($candidate->experience ?? []); @endphp
                    @if($experiences->count() > 0)
                    <span class="px-2 py-1 bg-[#1AAD94]/10 text-[#1AAD94] text-xs font-medium rounded">{{ $experiences->count() }} entries</span>
                    @else
                    <span class="w-6 h-6 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                    @endif
                </div>
            </div>
            <div class="p-5">
                @if($experiences->count() > 0)
                <div class="space-y-4">
                    @foreach($experiences as $index => $experience)
                    <div class="flex gap-4 @if(!$loop->last) pb-4 border-b border-[#E5E7EB] @endif">
                        <div class="w-12 h-12 bg-[#073057]/10 rounded-lg flex items-center justify-center text-[#073057] font-semibold shrink-0">
                            {{ substr($experience['company'] ?? 'C', 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-[#073057]">{{ $experience['position'] ?? $experience['title'] ?? 'Position' }}</h4>
                            <p class="text-sm text-[#1AAD94]">{{ $experience['company'] ?? 'Company' }}</p>
                            <p class="text-xs text-[#6B7280] mt-1">{{ $experience['start_date'] ?? '' }} - {{ $experience['end_date'] ?? 'Present' }}</p>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button @click="openExpModal(@js(array_merge($experience, ['index' => $index])))" type="button" class="p-2 text-[#6B7280] hover:text-[#1AAD94] hover:bg-[#1AAD94]/10 rounded-lg transition cursor-pointer" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form action="{{ route('user.profile.experience.delete', $index) }}" method="POST" onsubmit="return confirm('Delete this experience?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-[#6B7280] hover:text-red-500 hover:bg-red-50 rounded-lg transition cursor-pointer" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-[#9CA3AF] italic">No work experience added yet. Add your employment history to strengthen your resume.</p>
                @endif
            </div>
        </div>

        {{-- Education --}}
        <div class="bg-white rounded-xl border border-[#E5E7EB] overflow-hidden">
            <div class="p-5 border-b border-[#E5E7EB] flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#1AAD94]/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#1AAD94]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-[#073057]">Education</h3>
                        <p class="text-sm text-[#6B7280]">Your educational background</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="openEduModal()" type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm bg-[#1AAD94] hover:bg-[#158f7a] text-white rounded-lg transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add
                    </button>
                    @php $educations = collect($candidate->education ?? []); @endphp
                    @if($educations->count() > 0)
                    <span class="px-2 py-1 bg-[#1AAD94]/10 text-[#1AAD94] text-xs font-medium rounded">{{ $educations->count() }} entries</span>
                    @else
                    <span class="w-6 h-6 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                    @endif
                </div>
            </div>
            <div class="p-5">
                @if($educations->count() > 0)
                <div class="space-y-4">
                    @foreach($educations as $index => $education)
                    <div class="flex gap-4 @if(!$loop->last) pb-4 border-b border-[#E5E7EB] @endif">
                        <div class="w-12 h-12 bg-[#073057]/10 rounded-lg flex items-center justify-center text-[#073057] font-semibold shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-[#073057]">{{ $education['degree'] ?? 'Degree' }}</h4>
                            <p class="text-sm text-[#1AAD94]">{{ $education['institution'] ?? $education['school'] ?? 'Institution' }}</p>
                            <p class="text-xs text-[#6B7280] mt-1">{{ $education['year'] ?? '' }}</p>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button @click="openEduModal(@js(array_merge($education, ['index' => $index])))" type="button" class="p-2 text-[#6B7280] hover:text-[#1AAD94] hover:bg-[#1AAD94]/10 rounded-lg transition cursor-pointer" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form action="{{ route('user.profile.education.delete', $index) }}" method="POST" onsubmit="return confirm('Delete this education?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-[#6B7280] hover:text-red-500 hover:bg-red-50 rounded-lg transition cursor-pointer" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-[#9CA3AF] italic">No education added yet. Add your academic qualifications and certifications.</p>
                @endif
            </div>
        </div>

        {{-- Skills --}}
        <div class="bg-white rounded-xl border border-[#E5E7EB] overflow-hidden">
            <div class="p-5 border-b border-[#E5E7EB] flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#1AAD94]/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#1AAD94]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-[#073057]">Skills</h3>
                        <p class="text-sm text-[#6B7280]">Your professional skills</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="showSkillsModal = true" type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-[#1AAD94] hover:bg-[#1AAD94]/10 rounded-lg transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </button>
                    @php $skillsList = $candidate->skills_list ?? []; @endphp
                    @if(count($skillsList) > 0)
                    <span class="px-2 py-1 bg-[#1AAD94]/10 text-[#1AAD94] text-xs font-medium rounded">{{ count($skillsList) }} skills</span>
                    @else
                    <span class="w-6 h-6 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                    @endif
                </div>
            </div>
            <div class="p-5">
                @if(count($skillsList) > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($skillsList as $skill)
                    <span class="px-3 py-1.5 bg-[#073057]/5 text-[#073057] text-sm rounded-lg">{{ $skill }}</span>
                    @endforeach
                </div>
                @else
                <p class="text-[#9CA3AF] italic">No skills added yet. Add your technical and soft skills to show employers what you can do.</p>
                @endif
            </div>
        </div>

        {{-- Certifications --}}
        <div class="bg-white rounded-xl border border-[#E5E7EB] overflow-hidden">
            <div class="p-5 border-b border-[#E5E7EB] flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#1AAD94]/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#1AAD94]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-[#073057]">Certifications</h3>
                        <p class="text-sm text-[#6B7280]">Your professional certifications</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="openCertModal()" type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm bg-[#1AAD94] hover:bg-[#158f7a] text-white rounded-lg transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add
                    </button>
                    @php $certifications = collect($candidate->awards ?? []); @endphp
                    @if($certifications->count() > 0)
                    <span class="px-2 py-1 bg-[#1AAD94]/10 text-[#1AAD94] text-xs font-medium rounded">{{ $certifications->count() }} certs</span>
                    @else
                    <span class="w-6 h-6 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                    @endif
                </div>
            </div>
            <div class="p-5">
                @if($certifications->count() > 0)
                <div class="space-y-4">
                    @foreach($certifications as $index => $cert)
                    <div class="flex gap-4 @if(!$loop->last) pb-4 border-b border-[#E5E7EB] @endif">
                        <div class="w-12 h-12 bg-[#073057]/10 rounded-lg flex items-center justify-center text-[#073057] font-semibold shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-[#073057]">{{ $cert['name'] ?? 'Certification' }}</h4>
                            <p class="text-sm text-[#1AAD94]">{{ $cert['issuer'] ?? 'Issuing Authority' }}</p>
                            <p class="text-xs text-[#6B7280] mt-1">{{ $cert['issue_date'] ?? '' }} @if(!empty($cert['expiry_date'])) - {{ $cert['expiry_date'] }} @endif</p>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button @click="openCertModal(@js(array_merge($cert, ['index' => $index])))" type="button" class="p-2 text-[#6B7280] hover:text-[#1AAD94] hover:bg-[#1AAD94]/10 rounded-lg transition cursor-pointer" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form action="{{ route('user.profile.certification.delete', $index) }}" method="POST" onsubmit="return confirm('Delete this certification?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-[#6B7280] hover:text-red-500 hover:bg-red-50 rounded-lg transition cursor-pointer" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-[#9CA3AF] italic">No certifications added yet. Add your professional licenses and certifications.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mt-8 pt-6 border-t border-[#E5E7EB]">
        <a href="{{ route('user.cv-manager') }}" class="inline-flex items-center gap-2 text-[#6B7280] hover:text-[#073057] transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            Upload CV Instead
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('user.candidate.profile') }}" class="px-5 py-2.5 border border-[#E5E7EB] text-[#4B5563] font-semibold rounded-xl hover:bg-[#F9FAFB] transition cursor-pointer">
                Edit Profile
            </a>
        </div>
    </div>

    {{-- Experience Modal (Add/Edit) --}}
    <div x-show="showExpModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showExpModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showExpModal = false; editingExp = null"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showExpModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form :action="editingExp ? '{{ url('candidate/profile/experience') }}/' + editingExp.index : '{{ route('user.profile.experience.add') }}'" method="POST">
                    @csrf
                    <template x-if="editingExp">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    <div class="bg-white px-6 pt-6 pb-4">
                        <h3 class="text-lg font-semibold text-[#073057] mb-4" x-text="editingExp ? 'Edit Work Experience' : 'Add Work Experience'"></h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-[#073057] mb-1">Position *</label>
                                <input type="text" name="position" required placeholder="e.g. Chief Officer" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" :value="editingExp?.position || ''" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#073057] mb-1">Company *</label>
                                <input type="text" name="company" required placeholder="e.g. Maersk Line" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" :value="editingExp?.company || ''" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-[#073057] mb-1">Vessel Type</label>
                                    <input type="text" name="vessel_type" placeholder="e.g. Container Vessel" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" :value="editingExp?.vessel_type || ''" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#073057] mb-1">Vessel Name</label>
                                    <input type="text" name="vessel_name" placeholder="e.g. MV Copenhagen" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" :value="editingExp?.vessel_name || ''" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-[#073057] mb-1">Start Date *</label>
                                    <input type="date" name="start_date" required class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" :value="editingExp?.start_date || ''" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#073057] mb-1">End Date</label>
                                    <input type="date" name="end_date" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" :value="editingExp?.end_date || ''" />
                                </div>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_current" value="1" id="is_current_exp_rb" class="w-4 h-4 text-[#1AAD94] border-[#E5E7EB] rounded focus:ring-[#1AAD94]" :checked="editingExp?.is_current" />
                                <label for="is_current_exp_rb" class="ml-2 text-sm text-[#073057]">I currently work here</label>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" @click="showExpModal = false; editingExp = null" class="px-4 py-2 border border-[#E5E7EB] text-[#6B7280] rounded-lg hover:bg-gray-100 transition">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-[#1AAD94] hover:bg-[#158f7a] text-white rounded-lg transition" x-text="editingExp ? 'Update Experience' : 'Add Experience'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Education Modal (Add/Edit) --}}
    <div x-show="showEduModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showEduModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEduModal = false; editingEdu = null"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showEduModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form :action="editingEdu ? '{{ url('candidate/profile/education') }}/' + editingEdu.index : '{{ route('user.profile.education.add') }}'" method="POST">
                    @csrf
                    <template x-if="editingEdu">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    <div class="bg-white px-6 pt-6 pb-4">
                        <h3 class="text-lg font-semibold text-[#073057] mb-4" x-text="editingEdu ? 'Edit Education' : 'Add Education'"></h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-[#073057] mb-1">Degree / Qualification *</label>
                                <input type="text" name="degree" required placeholder="e.g. Bachelor of Science in Maritime Studies" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" :value="editingEdu?.degree || ''" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#073057] mb-1">Institution *</label>
                                <input type="text" name="institution" required placeholder="e.g. University of Southampton" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" :value="editingEdu?.institution || editingEdu?.school || ''" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#073057] mb-1">Field of Study</label>
                                <input type="text" name="field_of_study" placeholder="e.g. Navigation & Maritime Operations" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" :value="editingEdu?.field_of_study || ''" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-[#073057] mb-1">Start Year *</label>
                                    <input type="number" name="start_year" required min="1950" max="{{ date('Y') + 5 }}" placeholder="2016" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" :value="editingEdu?.start_year || ''" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#073057] mb-1">End Year</label>
                                    <input type="number" name="end_year" min="1950" max="{{ date('Y') + 10 }}" placeholder="2020" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" :value="editingEdu?.end_year || editingEdu?.year || ''" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" @click="showEduModal = false; editingEdu = null" class="px-4 py-2 border border-[#E5E7EB] text-[#6B7280] rounded-lg hover:bg-gray-100 transition">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-[#1AAD94] hover:bg-[#158f7a] text-white rounded-lg transition" x-text="editingEdu ? 'Update Education' : 'Add Education'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Certification Modal (Add/Edit) --}}
    <div x-show="showCertModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showCertModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showCertModal = false; editingCert = null"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showCertModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form :action="editingCert ? '{{ url('candidate/profile/certification') }}/' + editingCert.index : '{{ route('user.profile.certification.add') }}'" method="POST">
                    @csrf
                    <template x-if="editingCert">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    <div class="bg-white px-6 pt-6 pb-4">
                        <h3 class="text-lg font-semibold text-[#073057] mb-4" x-text="editingCert ? 'Edit Certification' : 'Add Certification'"></h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-[#073057] mb-1">Certification Name *</label>
                                <input type="text" name="name" required placeholder="e.g. STCW Basic Safety" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" :value="editingCert?.name || ''" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#073057] mb-1">Issuing Authority *</label>
                                <input type="text" name="issuer" required placeholder="e.g. Maritime & Coastguard Agency" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" :value="editingCert?.issuer || ''" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-[#073057] mb-1">Issue Date</label>
                                    <input type="date" name="issue_date" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" :value="editingCert?.issue_date || ''" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#073057] mb-1">Expiry Date</label>
                                    <input type="date" name="expiry_date" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" :value="editingCert?.expiry_date || ''" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#073057] mb-1">Credential ID</label>
                                <input type="text" name="credential_id" placeholder="Certificate number (optional)" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" :value="editingCert?.credential_id || ''" />
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" @click="showCertModal = false; editingCert = null" class="px-4 py-2 border border-[#E5E7EB] text-[#6B7280] rounded-lg hover:bg-gray-100 transition">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-[#1AAD94] hover:bg-[#158f7a] text-white rounded-lg transition" x-text="editingCert ? 'Update Certification' : 'Add Certification'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Summary Modal --}}
    <div x-show="showSummaryModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showSummaryModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showSummaryModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showSummaryModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('user.profile.summary.update') }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4">
                        <h3 class="text-lg font-semibold text-[#073057] mb-4">Edit Professional Summary</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-[#073057] mb-1">About You</label>
                                <textarea name="bio" rows="6" placeholder="Describe your professional background, expertise, and what makes you stand out..." class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none resize-none">{{ $candidate->bio }}</textarea>
                                <p class="text-xs text-[#6B7280] mt-1">Recommended: 3-5 sentences highlighting your key qualifications and career goals.</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" @click="showSummaryModal = false" class="px-4 py-2 border border-[#E5E7EB] text-[#6B7280] rounded-lg hover:bg-gray-100 transition">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-[#1AAD94] hover:bg-[#158f7a] text-white rounded-lg transition">Save Summary</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Skills Modal --}}
    <div x-show="showSkillsModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showSkillsModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showSkillsModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showSkillsModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('user.profile.skills.update') }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4">
                        <h3 class="text-lg font-semibold text-[#073057] mb-4">Edit Skills</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-[#073057] mb-1">Your Skills</label>
                                <textarea name="skills" rows="4" placeholder="Navigation, Ship Handling, Cargo Operations, ECDIS, GMDSS..." class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none resize-none">{{ implode(', ', $candidate->skills_list ?? []) }}</textarea>
                                <p class="text-xs text-[#6B7280] mt-1">Enter skills separated by commas. Include both technical and soft skills.</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" @click="showSkillsModal = false" class="px-4 py-2 border border-[#E5E7EB] text-[#6B7280] rounded-lg hover:bg-gray-100 transition">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-[#1AAD94] hover:bg-[#158f7a] text-white rounded-lg transition">Save Skills</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
