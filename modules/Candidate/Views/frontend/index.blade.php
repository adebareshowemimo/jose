@extends('layouts.app')

@section('content')
    <x-page-banner
        :title="setting_item_with_lang('candidate_page_list_title', __('Candidates'))"
        :subtitle="setting_item_with_lang('candidate_page_list_sub_title', __('Browse talented professionals'))"
        :breadcrumbs="[['label' => __('Home'), 'url' => url('/')], ['label' => __('Candidates')]]"
    />

    <section class="section-spacing">
        <div class="container-site">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                {{-- Sidebar Filters --}}
                <aside class="lg:col-span-1" x-data="{ open: false }">
                    <button @click="open = !open" class="lg:hidden btn btn-ghost w-full mb-4 cursor-pointer">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                        {{ __('Filters') }}
                    </button>
                    <div :class="open ? '' : 'max-lg:hidden'" class="space-y-6">
                        <form action="{{ route('candidate.index') }}" method="GET" class="space-y-6">

                            {{-- Keyword --}}
                            <div>
                                <label class="form-label">{{ __('Keyword') }}</label>
                                <div class="relative">
                                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-color-muted"></i>
                                    <input type="text" name="filter[s]" value="{{ $filter['s'] ?? '' }}" class="form-input pl-10" placeholder="{{ __('Name or keyword...') }}">
                                </div>
                            </div>

                            {{-- Location --}}
                            @if(isset($list_locations) && count($list_locations))
                                <div>
                                    <label class="form-label">{{ __('Location') }}</label>
                                    <select name="filter[location_id]" class="form-input">
                                        <option value="">{{ __('All Locations') }}</option>
                                        @foreach($list_locations as $loc)
                                            <option value="{{ $loc->id }}" @selected(($filter['location_id'] ?? '') == $loc->id)>{{ $loc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Category --}}
                            @if(isset($list_categories) && count($list_categories))
                                <div>
                                    <label class="form-label">{{ __('Category') }}</label>
                                    <select name="filter[cat_id]" class="form-input">
                                        <option value="">{{ __('All Categories') }}</option>
                                        @foreach($list_categories as $cat)
                                            <option value="{{ $cat->id }}" @selected(($filter['cat_id'] ?? '') == $cat->id)>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Skills --}}
                            @if(isset($list_skills) && count($list_skills))
                                <div>
                                    <label class="form-label">{{ __('Skills') }}</label>
                                    <select name="filter[skill_id]" class="form-input">
                                        <option value="">{{ __('All Skills') }}</option>
                                        @foreach($list_skills as $sk)
                                            <option value="{{ $sk->id }}" @selected(($filter['skill_id'] ?? '') == $sk->id)>{{ $sk->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <button type="submit" class="btn btn-primary w-full cursor-pointer">{{ __('Search') }}</button>
                            @if(!empty($filter))
                                <a href="{{ route('candidate.index') }}" class="block text-center text-sm text-accent hover:underline">{{ __('Clear Filters') }}</a>
                            @endif
                        </form>
                    </div>
                </aside>

                {{-- Results --}}
                <div class="lg:col-span-3">
                    {{-- Results Header --}}
                    <div class="flex items-center justify-between mb-6">
                        <p class="text-sm text-color-muted">
                            <strong class="text-color-dark">{{ $rows->total() }}</strong> {{ __('candidates found') }}
                        </p>
                    </div>

                    @if($rows->count())
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                            @foreach($rows as $row)
                                <x-candidate-card :candidate="$row" />
                            @endforeach
                        </div>
                        <div class="mt-8">
                            {{ $rows->appends(request()->query())->links('components.pagination') }}
                        </div>
                    @else
                        <x-empty-state icon="users" :title="__('No candidates found')" :message="__('Try adjusting your search filters.')" />
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
