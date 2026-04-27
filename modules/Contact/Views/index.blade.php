@extends('layouts.app')

@section('content')
    <x-page-banner
        :title="__('Contact Us')"
        :subtitle="__('We\'d love to hear from you')"
        :breadcrumbs="[['label' => __('Home'), 'url' => url('/')], ['label' => __('Contact')]]"
    />

    <section class="section-spacing">
        <div class="container-site">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Contact Form --}}
                <div class="lg:col-span-2">
                    <div class="card">
                        <h2 class="text-xl font-bold text-color-dark mb-6">{{ __('Send us a Message') }}</h2>

                        @if(session('success'))
                            <div class="bg-success/10 text-success rounded-lg p-4 mb-6 text-sm">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('contact.store') }}" method="POST" class="space-y-5">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="form-label">{{ __('Full Name') }} *</label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-input" required>
                                    @error('name') <span class="text-danger text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="form-label">{{ __('Email Address') }} *</label>
                                    <input type="email" name="email" value="{{ old('email') }}" class="form-input" required>
                                    @error('email') <span class="text-danger text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div>
                                <label class="form-label">{{ __('Subject') }}</label>
                                <input type="text" name="subject" value="{{ old('subject') }}" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">{{ __('Message') }} *</label>
                                <textarea name="message" rows="6" class="form-input" required>{{ old('message') }}</textarea>
                                @error('message') <span class="text-danger text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="btn btn-primary cursor-pointer">
                                <i data-lucide="send" class="w-4 h-4"></i> {{ __('Send Message') }}
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Contact Info --}}
                <div class="space-y-6">
                    <div class="card">
                        <h3 class="text-lg font-bold text-color-dark mb-4">{{ __('Get In Touch') }}</h3>
                        <div class="space-y-5">
                            @if(setting_item('contact_address'))
                                <div class="flex gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center shrink-0">
                                        <i data-lucide="map-pin" class="w-5 h-5 text-accent"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-color-dark text-sm">{{ __('Address') }}</h4>
                                        <p class="text-sm text-color-muted">{{ setting_item('contact_address') }}</p>
                                    </div>
                                </div>
                            @endif
                            @if(setting_item('contact_phone'))
                                <div class="flex gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center shrink-0">
                                        <i data-lucide="phone" class="w-5 h-5 text-accent"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-color-dark text-sm">{{ __('Phone') }}</h4>
                                        <p class="text-sm text-color-muted">{{ setting_item('contact_phone') }}</p>
                                    </div>
                                </div>
                            @endif
                            @if(setting_item('contact_email') || setting_item('admin_email'))
                                <div class="flex gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center shrink-0">
                                        <i data-lucide="mail" class="w-5 h-5 text-accent"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-color-dark text-sm">{{ __('Email') }}</h4>
                                        <p class="text-sm text-color-muted">{{ setting_item('contact_email') ?: setting_item('admin_email') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Office Hours --}}
                    <div class="card">
                        <h3 class="text-lg font-bold text-color-dark mb-4">{{ __('Office Hours') }}</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-color-muted">{{ __('Monday - Friday') }}</span>
                                <span class="font-medium text-color-dark">9:00 AM - 6:00 PM</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-color-muted">{{ __('Saturday') }}</span>
                                <span class="font-medium text-color-dark">10:00 AM - 2:00 PM</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-color-muted">{{ __('Sunday') }}</span>
                                <span class="font-medium text-color-dark">{{ __('Closed') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
