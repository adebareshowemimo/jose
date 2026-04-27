@extends('layouts.app')

@section('content')
    <x-page-banner
        :title="setting_item_with_lang('news_page_list_title', __('News & Updates'))"
        :subtitle="setting_item_with_lang('news_page_list_sub_title', __('Industry insights and maritime news'))"
        :breadcrumbs="[['label' => __('Home'), 'url' => url('/')], ['label' => __('News')]]"
    />

    <section class="section-spacing">
        <div class="container-site">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Main Content --}}
                <div class="lg:col-span-2">
                    @if($rows->count())
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            @foreach($rows as $row)
                                @php $newsTranslation = $row->translateOrOrigin(app()->getLocale()); @endphp
                                <article class="card group overflow-hidden">
                                    <a href="{{ $row->getDetailUrl() }}" class="block">
                                        @if($row->image_id)
                                            <div class="relative h-48 -mx-6 -mt-6 mb-4 overflow-hidden">
                                                <img src="{{ get_file_url($row->image_id, 'medium') }}" alt="{{ $newsTranslation->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            </div>
                                        @endif
                                    </a>
                                    <div class="flex items-center gap-3 text-xs text-color-muted mb-2">
                                        @if($row->getCategory)
                                            <span class="badge badge-muted text-xs">{{ $row->getCategory->name }}</span>
                                        @endif
                                        <span class="flex items-center gap-1"><i data-lucide="calendar" class="w-3 h-3"></i> {{ $row->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <a href="{{ $row->getDetailUrl() }}" class="block">
                                        <h3 class="font-bold text-color-dark group-hover:text-accent transition-colors mb-2 line-clamp-2">{{ $newsTranslation->title }}</h3>
                                    </a>
                                    <p class="text-sm text-color-muted line-clamp-3">{{ Str::limit(strip_tags($newsTranslation->content), 120) }}</p>
                                    <div class="mt-4 flex items-center gap-2 text-sm">
                                        @if($row->getAuthor)
                                            <span class="text-color-muted">{{ __('By') }} <strong class="text-color-dark">{{ $row->getAuthor->name }}</strong></span>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                        <div class="mt-8">
                            {{ $rows->appends(request()->query())->links('components.pagination') }}
                        </div>
                    @else
                        <x-empty-state icon="newspaper" :title="__('No articles found')" :message="__('Check back soon for the latest news.')" />
                    @endif
                </div>

                {{-- Sidebar --}}
                <aside class="space-y-6">
                    {{-- Search --}}
                    <div class="card">
                        <h3 class="text-lg font-bold text-color-dark mb-3">{{ __('Search') }}</h3>
                        <form action="{{ route('news.index') }}" method="GET">
                            <div class="relative">
                                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-color-muted"></i>
                                <input type="text" name="s" value="{{ request('s') }}" class="form-input pl-10" placeholder="{{ __('Search articles...') }}">
                            </div>
                        </form>
                    </div>

                    {{-- Categories --}}
                    @if(isset($model_category))
                        @php $newsCategories = $model_category->withCount('news')->limit(10)->get(); @endphp
                        @if($newsCategories->count())
                            <div class="card">
                                <h3 class="text-lg font-bold text-color-dark mb-3">{{ __('Categories') }}</h3>
                                <ul class="space-y-2">
                                    @foreach($newsCategories as $cat)
                                        <li>
                                            <a href="{{ route('news.index', ['cat_id' => $cat->id]) }}" class="flex items-center justify-between text-sm text-color-muted hover:text-accent transition-colors">
                                                <span>{{ $cat->name }}</span>
                                                <span class="text-xs bg-color-light rounded-full px-2 py-0.5">{{ $cat->news_count }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endif

                    {{-- Recent Posts --}}
                    @if(isset($model_news))
                        @php $recentPosts = $model_news->orderBy('id','desc')->limit(5)->get(); @endphp
                        @if($recentPosts->count())
                            <div class="card">
                                <h3 class="text-lg font-bold text-color-dark mb-3">{{ __('Recent Posts') }}</h3>
                                <div class="space-y-4">
                                    @foreach($recentPosts as $recent)
                                        <a href="{{ $recent->getDetailUrl() }}" class="flex gap-3 group">
                                            @if($recent->image_id)
                                                <img src="{{ get_file_url($recent->image_id, 'thumb') }}" alt="" class="w-16 h-16 rounded-lg object-cover shrink-0">
                                            @endif
                                            <div>
                                                <h4 class="text-sm font-semibold text-color-dark group-hover:text-accent transition-colors line-clamp-2">{{ $recent->title }}</h4>
                                                <span class="text-xs text-color-muted">{{ $recent->created_at->format('M d, Y') }}</span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Tags --}}
                    @if(isset($model_tag))
                        @php $newsTags = $model_tag->limit(20)->get(); @endphp
                        @if($newsTags->count())
                            <div class="card">
                                <h3 class="text-lg font-bold text-color-dark mb-3">{{ __('Tags') }}</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($newsTags as $tag)
                                        <a href="{{ route('news.index', ['tag' => $tag->slug]) }}" class="badge badge-muted hover:bg-accent hover:text-white transition-colors">{{ $tag->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </aside>
            </div>
        </div>
    </section>
@endsection


