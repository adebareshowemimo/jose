@extends('layouts.dashboard')

@section('title', 'Company Profile')
@section('page-title', 'Company Profile')

@section('sidebar-nav')
    @include('pages.dashboard.employer.partials.sidebar')
@endsection

@section('content')
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#073057]">Company Profile</h2>
            <p class="text-[#6B7280]">Manage your company information and branding</p>
        </div>
    </div>

    @php $company = $company ?? null; @endphp

    <div class="space-y-6">
        {{-- Company Logo & Banner --}}
        <div class="bg-white rounded-xl border border-[#E5E7EB] p-6">
            <h3 class="font-semibold text-[#073057] text-lg mb-4">Company Branding</h3>
            
            {{-- Banner Upload --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-[#073057] mb-2">Cover Banner</label>
                <form action="{{ route('employer.company.cover.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="h-40 rounded-xl relative overflow-hidden {{ $company?->cover_image ? '' : 'bg-gradient-to-r from-[#073057] to-[#1AAD94]' }}">
                        @if($company?->cover_image)
                            <img src="{{ asset('storage/' . $company->cover_image) }}" alt="Cover" class="w-full h-full object-cover" />
                        @endif
                        <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                            <label class="px-4 py-2 bg-white/90 hover:bg-white text-[#073057] text-sm font-medium rounded-lg transition flex items-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Change Banner
                                <input type="file" name="cover_image" accept="image/*" class="hidden" onchange="this.form.submit()" />
                            </label>
                        </div>
                    </div>
                </form>
                @error('cover_image') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-[#6B7280] mt-2">Recommended size: 1200x300 pixels. Max file size: 5MB</p>
            </div>

            {{-- Logo Upload --}}
            <div>
                <label class="block text-sm font-medium text-[#073057] mb-2">Company Logo</label>
                <div class="flex items-center gap-6">
                    <div class="w-24 h-24 bg-[#073057]/10 rounded-xl flex items-center justify-center overflow-hidden">
                        @if($company?->logo)
                            <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="w-full h-full object-cover" />
                        @else
                            <span class="text-[#073057] font-bold text-2xl">{{ substr($company?->name ?? 'C', 0, 2) }}</span>
                        @endif
                    </div>
                    <div>
                        <div class="flex gap-3 mb-2">
                            <form action="{{ route('employer.company.logo.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <label class="px-4 py-2 bg-[#1AAD94] hover:bg-[#158f7a] text-white text-sm font-medium rounded-lg transition cursor-pointer inline-block">
                                    Upload Logo
                                    <input type="file" name="logo" accept="image/*" class="hidden" onchange="this.form.submit()" />
                                </label>
                            </form>
                            @if($company?->logo)
                                <form action="{{ route('employer.company.logo.delete') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 border border-[#E5E7EB] text-[#6B7280] text-sm font-medium rounded-lg hover:bg-[#F9FAFB] transition">Remove</button>
                                </form>
                            @endif
                        </div>
                        @error('logo') <p class="text-xs text-red-500 mb-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-[#6B7280]">Square image recommended. Min 200x200 pixels. Max 2MB</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Profile Form --}}
        <form id="company-profile-form" action="{{ route('employer.company.profile.update') }}" method="POST">
            @csrf

            {{-- Basic Information --}}
            <div class="bg-white rounded-xl border border-[#E5E7EB] p-6 mb-6">
                <h3 class="font-semibold text-[#073057] text-lg mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-[#073057] mb-2">Company Name *</label>
                        <input type="text" name="name" value="{{ old('name', $company?->name) }}" required class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none @error('name') border-red-400 @enderror" />
                        @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#073057] mb-2">Industry</label>
                        <select name="industry_ids[]" multiple class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                            @foreach($industries as $industry)
                                <option value="{{ $industry->id }}" {{ in_array($industry->id, old('industry_ids', $company?->industries?->pluck('id')?->toArray() ?? [])) ? 'selected' : '' }}>
                                    {{ $industry->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#073057] mb-2">Company Size</label>
                        <select name="company_size" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none">
                            <option value="">Select Size</option>
                            @foreach(['1-10', '11-50', '51-200', '201-500', '501-1000', '1000+'] as $size)
                                <option value="{{ $size }}" {{ old('company_size', $company?->company_size) === $size ? 'selected' : '' }}>{{ $size }} employees</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#073057] mb-2">Founded Year</label>
                        <input type="number" name="founded_in" value="{{ old('founded_in', $company?->founded_in) }}" min="1800" max="{{ now()->year }}" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none @error('founded_in') border-red-400 @enderror" />
                        @error('founded_in') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#073057] mb-2">Website</label>
                        <input type="url" name="website" value="{{ old('website', $company?->website) }}" placeholder="https://example.com" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-[#073057] mb-2">Company Description</label>
                        <textarea rows="4" name="about" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none resize-none" placeholder="Describe your company, mission, and what makes it a great place to work...">{{ old('about', $company?->about) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Contact & Countries --}}
            <div class="bg-white rounded-xl border border-[#E5E7EB] p-6 mb-6">
                <h3 class="font-semibold text-[#073057] text-lg mb-4">Contact & Countries</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-[#073057] mb-2">Primary Email</label>
                        <input type="email" name="email" value="{{ old('email', $company?->email) }}" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#073057] mb-2">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone', $company?->phone) }}" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-[#073057] mb-2">Address</label>
                        <input type="text" name="address" value="{{ old('address', $company?->address) }}" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#073057] mb-2">Country</label>
                        <select name="location_id" class="w-full px-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none @error('location_id') border-red-400 @enderror">
                            <option value="">Select Country</option>
                            @foreach($locations as $country)
                                <option value="{{ $country->id }}" {{ old('location_id', $company?->location_id) == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('location_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Social Media --}}
            <div class="bg-white rounded-xl border border-[#E5E7EB] p-6">
                <h3 class="font-semibold text-[#073057] text-lg mb-4">Social Media Links</h3>
                @php $social = old('social_links', $company?->social_links ?? []); @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-[#073057] mb-2">LinkedIn</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#6B7280]">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                            </span>
                            <input type="url" name="social_links[linkedin]" value="{{ $social['linkedin'] ?? '' }}" placeholder="https://linkedin.com/company/..." class="w-full pl-12 pr-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#073057] mb-2">Twitter</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#6B7280]">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </span>
                            <input type="url" name="social_links[twitter]" value="{{ $social['twitter'] ?? '' }}" placeholder="https://twitter.com/..." class="w-full pl-12 pr-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#073057] mb-2">Facebook</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#6B7280]">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </span>
                            <input type="url" name="social_links[facebook]" value="{{ $social['facebook'] ?? '' }}" placeholder="https://facebook.com/..." class="w-full pl-12 pr-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#073057] mb-2">Instagram</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#6B7280]">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                            </span>
                            <input type="url" name="social_links[instagram]" value="{{ $social['instagram'] ?? '' }}" placeholder="https://instagram.com/..." class="w-full pl-12 pr-4 py-3 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#1AAD94] outline-none" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-6">
                <button type="submit" class="px-6 py-3 bg-[#1AAD94] hover:bg-[#158f7a] text-white font-semibold rounded-xl transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
@endsection
