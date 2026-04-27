@extends('layouts.dashboard')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('sidebar-nav')
    @include('pages.dashboard.candidate.partials.sidebar')
@endsection

@php
    $user = $user ?? auth()->user();
    $candidate = $candidate ?? $user->candidate;
    $nameParts = explode(' ', $user->name ?? '', 2);
    $firstName = $nameParts[0] ?? '';
    $lastName = $nameParts[1] ?? '';
    
    // Calculate profile completion
    $completionItems = [
        'name' => !empty($user->name),
        'email' => !empty($user->email),
        'phone' => !empty($user->phone),
        'avatar' => !empty($user->avatar),
        'bio' => !empty($candidate?->bio),
        'experience' => !empty($candidate?->experience),
        'education' => !empty($candidate?->education),
        'certifications' => !empty($candidate?->awards),
    ];
    $completionPercent = count(array_filter($completionItems)) / count($completionItems) * 100;
@endphp

@section('content')
    <div x-data="{ 
        activeTab: 'basic',
        showExpModal: false,
        showEduModal: false,
        showCertModal: false,
        editingExp: null,
        editingEdu: null,
        editingCert: null
    }">
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
                <h2 class="text-2xl font-bold text-[#073057]">My Profile</h2>
                <p class="text-[#6B7280]">Update your personal information and preferences</p>
            </div>
        </div>

        {{-- Profile Completion Banner --}}
        <div class="bg-gradient-to-r from-[#073057] to-[#0a4a7c] rounded-xl p-6 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold text-lg">Profile Completion: {{ round($completionPercent) }}%</h3>
                        <p class="text-white/70 text-sm">Complete your profile to increase visibility to employers</p>
                    </div>
                </div>
                <div class="flex-1 max-w-[300px]">
                    <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                        <div class="h-full bg-[#1AAD94] rounded-full" style="width: {{ $completionPercent }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="flex flex-wrap border-b border-[#E5E7EB] mb-6 gap-1">
            <button @click="activeTab = 'basic'" :class="activeTab === 'basic' ? 'border-[#1AAD94] text-[#1AAD94]' : 'border-transparent text-[#6B7280] hover:text-[#073057]'" class="px-6 py-3 text-sm font-medium border-b-2 -mb-px transition">Basic Info</button>
            <button @click="activeTab = 'experience'" :class="activeTab === 'experience' ? 'border-[#1AAD94] text-[#1AAD94]' : 'border-transparent text-[#6B7280] hover:text-[#073057]'" class="px-6 py-3 text-sm font-medium border-b-2 -mb-px transition">Experience</button>
            <button @click="activeTab = 'education'" :class="activeTab === 'education' ? 'border-[#1AAD94] text-[#1AAD94]' : 'border-transparent text-[#6B7280] hover:text-[#073057]'" class="px-6 py-3 text-sm font-medium border-b-2 -mb-px transition">Education</button>
            <button @click="activeTab = 'certifications'" :class="activeTab === 'certifications' ? 'border-[#1AAD94] text-[#1AAD94]' : 'border-transparent text-[#6B7280] hover:text-[#073057]'" class="px-6 py-3 text-sm font-medium border-b-2 -mb-px transition">Certifications</button>
            <button @click="activeTab = 'social'" :class="activeTab === 'social' ? 'border-[#1AAD94] text-[#1AAD94]' : 'border-transparent text-[#6B7280] hover:text-[#073057]'" class="px-6 py-3 text-sm font-medium border-b-2 -mb-px transition">Social Links</button>
        </div>

        {{-- Basic Info Tab --}}
        <div x-show="activeTab === 'basic'" class="space-y-6">
            {{-- Profile Photo --}}
            <div class="bg-white rounded-xl border border-[#E5E7EB] p-6">
                <h3 class="font-semibold text-[#073057] mb-4">Profile Photo</h3>
                <form action="{{ route('user.profile.avatar.update') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-6">
                    @csrf
                    <div class="w-24 h-24 bg-[#E5E7EB] rounded-full flex items-center justify-center overflow-hidden">
                        @if($user->avatar)
                            <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover" />
                        @else
                            <svg class="w-12 h-12 text-[#9CA3AF]" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        @endif
                    </div>
                    <div>
                        <div class="flex gap-3 mb-2">
                            <label class="px-4 py-2 bg-[#1AAD94] hover:bg-[#158f7a] text-white text-sm font-medium rounded-lg transition cursor-pointer">
                                <span>Choose Photo</span>
                                <input type="file" name="avatar" accept="image/*" class="hidden" onchange="this.form.submit()" />
                            </label>
                        </div>
                        <p class="text-sm text-[#6B7280]">Max file size: 2MB. Supported formats: JPG, PNG, GIF</p>
                        @error('avatar')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </form>
            </div>

            {{-- Personal Information Form --}}
            <form action="{{ route('user.profile.basic.update') }}" method="POST">
                @csrf
                <div class="bg-white rounded-xl border border-[#E5E7EB] p-6 mb-6">
                    <h3 class="font-semibold text-[#073057] mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-[#073057] mb-2">First Name *</label>
                            <input type="text" name="first_name" value="{{ old('first_name', $firstName) }}" required class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none @error('first_name') border-red-500 @enderror" />
                            @error('first_name')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#073057] mb-2">Last Name *</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $lastName) }}" required class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none @error('last_name') border-red-500 @enderror" />
                            @error('last_name')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#073057] mb-2">Email Address *</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none @error('email') border-red-500 @enderror" />
                            @error('email')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#073057] mb-2">Phone Number</label>
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none @error('phone') border-red-500 @enderror" />
                            @error('phone')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#073057] mb-2">Date of Birth</label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $candidate?->date_of_birth?->format('Y-m-d')) }}" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#073057] mb-2">Gender</label>
                            <select name="gender" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $candidate?->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $candidate?->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $candidate?->gender) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#073057] mb-2">Nationality</label>
                            <select name="location_id" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                                <option value="">Select Nationality</option>
                                @foreach($locations ?? [] as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id', $candidate?->location_id) == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Professional Details --}}
                <div class="bg-white rounded-xl border border-[#E5E7EB] p-6 mb-6">
                    <h3 class="font-semibold text-[#073057] mb-4">Professional Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-[#073057] mb-2">Current Position</label>
                            <input type="text" name="title" value="{{ old('title', $candidate?->title) }}" placeholder="e.g. Chief Officer" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#073057] mb-2">Years of Experience</label>
                            <select name="experience_years" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                                <option value="">Select Experience</option>
                                <option value="0-2" {{ old('experience_years', $candidate?->experience_years) == '0-2' ? 'selected' : '' }}>0-2 Years</option>
                                <option value="3-5" {{ old('experience_years', $candidate?->experience_years) == '3-5' ? 'selected' : '' }}>3-5 Years</option>
                                <option value="5-10" {{ old('experience_years', $candidate?->experience_years) == '5-10' ? 'selected' : '' }}>5-10 Years</option>
                                <option value="10+" {{ old('experience_years', $candidate?->experience_years) == '10+' ? 'selected' : '' }}>10+ Years</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#073057] mb-2">Expected Salary (Monthly USD)</label>
                            <input type="number" name="expected_salary" value="{{ old('expected_salary', $candidate?->expected_salary) }}" placeholder="e.g. 8000" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                        </div>
                    </div>
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-[#073057] mb-2">About Me</label>
                        <textarea name="bio" rows="4" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none resize-none" placeholder="Write a brief description about yourself...">{{ old('bio', $candidate?->bio) }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition">
                        Save Basic Info
                    </button>
                </div>
            </form>
        </div>

        {{-- Experience Tab --}}
        <div x-show="activeTab === 'experience'" class="space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-[#073057]">Work Experience</h3>
                <button @click="showExpModal = true; editingExp = null" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-[#1AAD94] hover:bg-[#158f7a] text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Add Experience
                </button>
            </div>

            @forelse($candidate?->experience ?? [] as $index => $exp)
                <div class="bg-white rounded-xl border border-[#E5E7EB] p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-[#073057]/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-[#073057]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-[#073057]">{{ $exp['position'] ?? 'N/A' }}</h4>
                                <p class="text-[#1AAD94]">{{ $exp['company'] ?? 'N/A' }}</p>
                                <p class="text-sm text-[#6B7280] mt-1">
                                    {{ $exp['start_date'] ? \Carbon\Carbon::parse($exp['start_date'])->format('M Y') : '' }} 
                                    - {{ $exp['is_current'] ?? false ? 'Present' : ($exp['end_date'] ? \Carbon\Carbon::parse($exp['end_date'])->format('M Y') : '') }}
                                </p>
                                @if(!empty($exp['vessel_type']) || !empty($exp['vessel_name']))
                                    <p class="text-sm text-[#6B7280]">{{ $exp['vessel_type'] ?? '' }} {{ !empty($exp['vessel_name']) ? '· ' . $exp['vessel_name'] : '' }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <form action="{{ route('user.profile.experience.delete', $exp['id'] ?? $index) }}" method="POST" class="inline" onsubmit="return confirm('Delete this experience?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-[#6B7280] hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-[#E5E7EB] p-12 text-center">
                    <svg class="w-16 h-16 text-[#E5E7EB] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <h4 class="font-semibold text-[#073057] mb-2">No Experience Added</h4>
                    <p class="text-[#6B7280] mb-4">Add your work experience to showcase your career history.</p>
                    <button @click="showExpModal = true" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-[#1AAD94] hover:bg-[#158f7a] text-white text-sm font-medium rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Add Your First Experience
                    </button>
                </div>
            @endforelse
        </div>

        {{-- Education Tab --}}
        <div x-show="activeTab === 'education'" class="space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-[#073057]">Education</h3>
                <button @click="showEduModal = true; editingEdu = null" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-[#1AAD94] hover:bg-[#158f7a] text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Add Education
                </button>
            </div>

            @forelse($candidate?->education ?? [] as $index => $edu)
                <div class="bg-white rounded-xl border border-[#E5E7EB] p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-[#1AAD94]/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-[#1AAD94]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-[#073057]">{{ $edu['degree'] ?? 'N/A' }}</h4>
                                <p class="text-[#1AAD94]">{{ $edu['institution'] ?? 'N/A' }}</p>
                                <p class="text-sm text-[#6B7280] mt-1">{{ $edu['start_year'] ?? '' }} - {{ $edu['end_year'] ?? 'Present' }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <form action="{{ route('user.profile.education.delete', $edu['id'] ?? $index) }}" method="POST" class="inline" onsubmit="return confirm('Delete this education?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-[#6B7280] hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-[#E5E7EB] p-12 text-center">
                    <svg class="w-16 h-16 text-[#E5E7EB] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    <h4 class="font-semibold text-[#073057] mb-2">No Education Added</h4>
                    <p class="text-[#6B7280] mb-4">Add your educational background and qualifications.</p>
                    <button @click="showEduModal = true" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-[#1AAD94] hover:bg-[#158f7a] text-white text-sm font-medium rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Add Your First Education
                    </button>
                </div>
            @endforelse
        </div>

        {{-- Certifications Tab --}}
        <div x-show="activeTab === 'certifications'" class="space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-[#073057]">Certifications & Licenses</h3>
                <button @click="showCertModal = true; editingCert = null" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-[#1AAD94] hover:bg-[#158f7a] text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Add Certification
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($candidate?->awards ?? [] as $index => $cert)
                    <div class="bg-white rounded-xl border border-[#E5E7EB] p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-semibold text-[#073057]">{{ $cert['name'] ?? 'N/A' }}</h4>
                                        <p class="text-sm text-[#6B7280]">{{ $cert['issuer'] ?? 'N/A' }}</p>
                                    </div>
                                    <form action="{{ route('user.profile.certification.delete', $cert['id'] ?? $index) }}" method="POST" class="inline" onsubmit="return confirm('Delete this certification?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-[#6B7280] hover:text-red-600 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </form>
                                </div>
                                <div class="flex items-center gap-2 mt-2">
                                    @php
                                        $expiryDate = !empty($cert['expiry_date']) ? \Carbon\Carbon::parse($cert['expiry_date']) : null;
                                        $isValid = !$expiryDate || $expiryDate->isFuture();
                                    @endphp
                                    <span class="px-2 py-1 {{ $isValid ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }} text-xs font-medium rounded">
                                        {{ $isValid ? 'Valid' : 'Expired' }}
                                    </span>
                                    @if($expiryDate)
                                        <span class="text-xs text-[#6B7280]">Expires: {{ $expiryDate->format('M Y') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 bg-white rounded-xl border border-[#E5E7EB] p-12 text-center">
                        <svg class="w-16 h-16 text-[#E5E7EB] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        <h4 class="font-semibold text-[#073057] mb-2">No Certifications Added</h4>
                        <p class="text-[#6B7280] mb-4">Add your maritime certifications and licenses.</p>
                        <button @click="showCertModal = true" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-[#1AAD94] hover:bg-[#158f7a] text-white text-sm font-medium rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Add Your First Certification
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Social Links Tab --}}
        <div x-show="activeTab === 'social'" class="space-y-6">
            <form action="{{ route('user.profile.social.update') }}" method="POST">
                @csrf
                <div class="bg-white rounded-xl border border-[#E5E7EB] p-6">
                    <h3 class="font-semibold text-[#073057] mb-4">Social & Professional Links</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-[#073057] mb-2">LinkedIn Profile</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#6B7280]">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                </span>
                                <input type="url" name="linkedin" value="{{ old('linkedin', $candidate?->social_links['linkedin'] ?? '') }}" placeholder="https://linkedin.com/in/username" class="w-full pl-12 pr-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                            </div>
                            @error('linkedin')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#073057] mb-2">Website / Portfolio</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#6B7280]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                </span>
                                <input type="url" name="website" value="{{ old('website', $candidate?->website) }}" placeholder="https://yourwebsite.com" class="w-full pl-12 pr-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                            </div>
                            @error('website')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#073057] mb-2">Twitter / X</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#6B7280]">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                </span>
                                <input type="url" name="twitter" value="{{ old('twitter', $candidate?->social_links['twitter'] ?? '') }}" placeholder="https://twitter.com/username" class="w-full pl-12 pr-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit" class="px-6 py-3 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition">
                        Save Social Links
                    </button>
                </div>
            </form>
        </div>

        {{-- Experience Modal --}}
        <div x-show="showExpModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showExpModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showExpModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showExpModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('user.profile.experience.add') }}" method="POST">
                        @csrf
                        <div class="bg-white px-6 pt-6 pb-4">
                            <h3 class="text-lg font-semibold text-[#073057] mb-4">Add Work Experience</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-[#073057] mb-1">Position *</label>
                                    <input type="text" name="position" required placeholder="e.g. Chief Officer" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#073057] mb-1">Company *</label>
                                    <input type="text" name="company" required placeholder="e.g. Maersk Line" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-[#073057] mb-1">Vessel Type</label>
                                        <input type="text" name="vessel_type" placeholder="e.g. Container Vessel" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-[#073057] mb-1">Vessel Name</label>
                                        <input type="text" name="vessel_name" placeholder="e.g. MV Copenhagen" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-[#073057] mb-1">Start Date *</label>
                                        <input type="date" name="start_date" required class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-[#073057] mb-1">End Date</label>
                                        <input type="date" name="end_date" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_current" value="1" id="is_current_exp" class="w-4 h-4 text-[#1AAD94] border-[#E5E7EB] rounded focus:ring-[#1AAD94]" />
                                    <label for="is_current_exp" class="ml-2 text-sm text-[#073057]">I currently work here</label>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                            <button type="button" @click="showExpModal = false" class="px-4 py-2 border border-[#E5E7EB] text-[#6B7280] rounded-lg hover:bg-gray-100 transition">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-[#1AAD94] hover:bg-[#158f7a] text-white rounded-lg transition">Add Experience</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Education Modal --}}
        <div x-show="showEduModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showEduModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEduModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showEduModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('user.profile.education.add') }}" method="POST">
                        @csrf
                        <div class="bg-white px-6 pt-6 pb-4">
                            <h3 class="text-lg font-semibold text-[#073057] mb-4">Add Education</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-[#073057] mb-1">Degree / Qualification *</label>
                                    <input type="text" name="degree" required placeholder="e.g. Bachelor of Science in Maritime Studies" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#073057] mb-1">Institution *</label>
                                    <input type="text" name="institution" required placeholder="e.g. University of Southampton" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#073057] mb-1">Field of Study</label>
                                    <input type="text" name="field_of_study" placeholder="e.g. Navigation & Maritime Operations" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-[#073057] mb-1">Start Year *</label>
                                        <input type="number" name="start_year" required min="1950" max="{{ date('Y') + 5 }}" placeholder="2016" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-[#073057] mb-1">End Year</label>
                                        <input type="number" name="end_year" min="1950" max="{{ date('Y') + 10 }}" placeholder="2020" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                            <button type="button" @click="showEduModal = false" class="px-4 py-2 border border-[#E5E7EB] text-[#6B7280] rounded-lg hover:bg-gray-100 transition">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-[#1AAD94] hover:bg-[#158f7a] text-white rounded-lg transition">Add Education</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Certification Modal --}}
        <div x-show="showCertModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showCertModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showCertModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showCertModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('user.profile.certification.add') }}" method="POST">
                        @csrf
                        <div class="bg-white px-6 pt-6 pb-4">
                            <h3 class="text-lg font-semibold text-[#073057] mb-4">Add Certification</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-[#073057] mb-1">Certification Name *</label>
                                    <input type="text" name="name" required placeholder="e.g. STCW Basic Safety" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#073057] mb-1">Issuing Authority *</label>
                                    <input type="text" name="issuer" required placeholder="e.g. Maritime & Coastguard Agency" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-[#073057] mb-1">Issue Date</label>
                                        <input type="date" name="issue_date" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-[#073057] mb-1">Expiry Date</label>
                                        <input type="date" name="expiry_date" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#073057] mb-1">Credential ID</label>
                                    <input type="text" name="credential_id" placeholder="Certificate number (optional)" class="w-full px-4 py-2.5 border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                            <button type="button" @click="showCertModal = false" class="px-4 py-2 border border-[#E5E7EB] text-[#6B7280] rounded-lg hover:bg-gray-100 transition">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-[#1AAD94] hover:bg-[#158f7a] text-white rounded-lg transition">Add Certification</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        button, [type="button"], [type="submit"], label.cursor-pointer { cursor: pointer; }
    </style>
@endsection
