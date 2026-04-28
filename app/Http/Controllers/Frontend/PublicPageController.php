<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Event;
use App\Models\JobListing;
use App\Models\NewsArticle;
use App\Models\TrainingProgram;
use App\Support\JclProfileContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PublicPageController extends BasePageController
{
    public function about()
    {
        $profile = JclProfileContent::company();

        return view('pages.about.index', $this->buildJclPageData(
            title: 'About JCL',
            description: 'Discover JCL’s mission, vision, values, and workforce transformation approach.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'About JCL'],
            ],
            extra: [
                'profile' => $profile,
            ],
        ));
    }

    public function leadership()
    {
        $profile = JclProfileContent::company();

        return view('pages.leadership.index', $this->buildJclPageData(
            title: 'Leadership & Experts',
            description: 'Meet the experienced leaders, specialists, and technical partners behind JCL’s delivery model.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Leadership & Experts'],
            ],
            extra: [
                'profile' => $profile,
                'leaders' => $profile['leadership'],
            ],
        ));
    }

    public function partnerships()
    {
        $profile = JclProfileContent::company();

        return view('pages.partnerships.index', $this->buildJclPageData(
            title: 'Partnerships & Expertise',
            description: 'Explore JCL’s specialist partnerships, delivery flexibility, and global-facing capability areas.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Partnerships & Expertise'],
            ],
            extra: [
                'profile' => $profile,
                'partnerships' => $profile['partnerships'],
            ],
        ));
    }
    public function training(Request $request)
    {
        return $this->renderTrainingListing($request, 'training');
    }

    public function trainingShow(string $slug)
    {
        $program = \App\Models\TrainingProgram::where('slug', $slug)->where('is_active', true)->first();
        if (! $program) {
            abort(404);
        }
        $isApprenticeship = $program->type === 'apprenticeship';
        $listingRoute = $isApprenticeship ? 'career.apprenticeship' : 'training.index';
        $listingLabel = $isApprenticeship ? 'Apprenticeships' : 'Training';

        return view('pages.training.show', $this->buildJclPageData(
            title: $program->title,
            description: $program->short_description ?? $program->title,
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => $listingLabel, 'url' => route($listingRoute)],
                ['label' => $program->title],
            ],
            extra: ['program' => $program],
        ));
    }

    private function renderTrainingListing(Request $request, string $type)
    {
        $isApprenticeship = $type === 'apprenticeship';

        $query = \App\Models\TrainingProgram::active()->ofType($type);
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }
        $programs = $query
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $categories = \App\Models\TrainingProgram::active()->ofType($type)
            ->whereNotNull('category')
            ->select('category')->distinct()->orderBy('category')->pluck('category')->all();

        $title = $isApprenticeship ? 'Apprenticeships' : 'Training';
        $crumbs = $isApprenticeship
            ? [['label' => 'Home', 'url' => url('/')], ['label' => 'Career', 'url' => route('career.index')], ['label' => 'Apprenticeship']]
            : [['label' => 'Home', 'url' => url('/')], ['label' => 'Training']];

        return view('pages.training.index', $this->buildJclPageData(
            title: $title,
            description: $isApprenticeship
                ? 'Earn while you learn — paid maritime apprenticeship programmes.'
                : 'Internationally recognised training programmes built around STCW, BOSIET and industry frameworks.',
            breadcrumbs: $crumbs,
            extra: [
                'programs' => $programs,
                'categories' => $categories,
                'filterType' => $type,
            ],
        ));
    }
    public function jobsIndex()
    {
        $jobs = [
            [
                'slug' => 'technical-superintendent',
                'title' => 'Technical Superintendent',
                'company' => 'Maritime Engineering Group Ltd.',
                'location' => 'Oslo, Norway',
                'type' => 'Permanent',
                'salary' => '$14,000 - $18,500',
                'category' => 'Engineering',
            ],
            [
                'slug' => 'master-mariner',
                'title' => 'Master Mariner',
                'company' => 'Blue Star Lines International',
                'location' => 'South China Sea',
                'type' => 'Hot Job',
                'salary' => '$22,000 Tax-Free',
                'category' => 'Deck',
            ],
            [
                'slug' => 'second-engineer',
                'title' => 'Second Engineer',
                'company' => 'Global Fleet Management',
                'location' => 'Rotterdam, Netherlands',
                'type' => 'Contract',
                'salary' => '$9,500 - $11,000',
                'category' => 'Engineering',
            ],
        ];

        return view('pages.jobs.index', [
            'pageTitle' => 'Job Search',
            'pageDescription' => 'Browse maritime and offshore opportunities.',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Jobs'],
            ],
            'jobs' => $jobs,
            'categories' => ['Engineering', 'Deck', 'Offshore', 'Navigation'],
        ]);
    }

    public function jobDetail(string $slug)
    {
        $listing = JobListing::with(['company', 'location', 'jobType'])
            ->where('slug', $slug)
            ->first();

        if ($listing) {
            $isLive = $listing->status === 'active' && $listing->is_approved;
            $user = auth()->user();
            $isOwner = $user && (
                $user->id === $listing->posted_by
                || $user->id === $listing->company?->owner_id
                || ($user->role?->name === 'admin')
            );

            abort_unless($isLive || $isOwner, 404);

            $previewStatus = $isLive ? null : $listing->status;

            $job = [
                'slug' => $listing->slug,
                'title' => $listing->title,
                'company' => $listing->company?->name ?? 'Employer',
                'location' => $listing->location?->name ?? $listing->address ?? 'Worldwide',
                'type' => $listing->jobType?->name ?? ucfirst((string) $listing->hours_type ?: 'Job'),
                'salary' => $listing->salary_min || $listing->salary_max
                    ? trim(($listing->salary_min ? number_format((float) $listing->salary_min) : '').' - '.($listing->salary_max ? number_format((float) $listing->salary_max) : '').' '.($listing->salary_type ?? ''))
                    : 'Not disclosed',
                'description' => $listing->description,
                'requirements' => array_filter(preg_split('/\r\n|\r|\n/', (string) $listing->qualification)),
            ];

            return view('pages.jobs.detail', [
                'pageTitle' => 'Job Detail',
                'pageDescription' => "Viewing role: {$job['title']}",
                'breadcrumbs' => [
                    ['label' => 'Home', 'url' => url('/')],
                    ['label' => 'Jobs', 'url' => route('job.index')],
                    ['label' => $job['title']],
                ],
                'job' => $job,
                'previewStatus' => $previewStatus,
            ]);
        }

        $job = [
            'slug' => $slug,
            'title' => ucwords(str_replace('-', ' ', $slug)),
            'company' => 'Global Maritime Employer',
            'location' => 'Worldwide',
            'type' => 'Full-Time',
            'salary' => '$10,000 - $15,000',
            'description' => 'Lead operational excellence across marine assignments while ensuring compliance with international maritime standards.',
            'requirements' => [
                'Valid STCW certifications and seagoing records',
                'Minimum 3 years relevant maritime experience',
                'Strong communication and safety-first mindset',
            ],
        ];

        return view('pages.jobs.detail', [
            'pageTitle' => 'Job Detail',
            'pageDescription' => "Viewing role: {$job['title']}",
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Jobs', 'url' => route('job.index')],
                ['label' => $job['title']],
            ],
            'job' => $job,
        ]);
    }

    public function jobCategory(string $slug)
    {
        $category = ucwords(str_replace('-', ' ', $slug));

        return view('pages.jobs.category', [
            'pageTitle' => 'Jobs by Category',
            'pageDescription' => "Category: {$category}",
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Jobs', 'url' => route('job.index')],
                ['label' => $category],
            ],
            'category' => $category,
            'jobs' => [
                ['slug' => 'offshore-operations-supervisor', 'title' => 'Offshore Operations Supervisor', 'company' => 'Atlantic Offshore', 'location' => 'North Sea'],
                ['slug' => 'chief-officer-tanker', 'title' => 'Chief Officer (Tanker)', 'company' => 'Marine Crest', 'location' => 'Middle East'],
            ],
        ]);
    }

    public function candidatesIndex()
    {
        $fallback = [
            ['slug' => 'john-anderson', 'name' => 'John Anderson', 'role' => 'Chief Engineer', 'location' => 'Oslo, Norway', 'experience' => '11 years', 'availability' => 'Immediate', 'is_featured' => false],
            ['slug' => 'elena-petrova', 'name' => 'Elena Petrova', 'role' => 'Navigation Officer', 'location' => 'Rotterdam, Netherlands', 'experience' => '8 years', 'availability' => '2 weeks', 'is_featured' => false],
            ['slug' => 'marcus-thorne', 'name' => 'Marcus Thorne', 'role' => 'Safety Superintendent', 'location' => 'Aberdeen, UK', 'experience' => '13 years', 'availability' => 'Immediate', 'is_featured' => false],
        ];

        $candidates = $fallback;

        if (Schema::hasTable('candidates')) {
            // Featured (boosted or premium) come first via featured_until DESC NULLS LAST.
            $now = now()->toDateTimeString();
            $today = now()->toDateString();

            // Pull active premium subscriber user ids (always-featured benefit).
            $premiumUserIds = [];
            if (Schema::hasTable('subscriptions') && Schema::hasTable('plans')) {
                $premiumUserIds = \App\Models\Subscription::query()
                    ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
                    ->where('subscriptions.status', 'active')
                    ->where(function ($q) use ($today) {
                        $q->whereNull('subscriptions.ends_at')->orWhere('subscriptions.ends_at', '>=', $today);
                    })
                    ->whereRaw("plans.benefits LIKE '%\"always_featured\":true%'")
                    ->pluck('subscriptions.user_id')
                    ->all();
            }

            $rows = \App\Models\Candidate::with(['user:id,name', 'location:id,name'])
                ->where('allow_search', true)
                ->orderByRaw('CASE WHEN featured_until > ? OR user_id IN (' . (empty($premiumUserIds) ? 'NULL' : implode(',', $premiumUserIds)) . ') THEN 0 ELSE 1 END', [$now])
                ->orderByDesc('featured_until')
                ->orderByDesc('created_at')
                ->take(24)
                ->get();

            // Mark premium-only candidates as featured for view purposes.
            foreach ($rows as $c) {
                if (in_array($c->user_id, $premiumUserIds, true) && ! $c->isFeatured()) {
                    $c->setAttribute('_premium_featured', true);
                }
            }

            if ($rows->isNotEmpty()) {
                $candidates = $rows->map(fn ($c) => [
                    'slug' => $c->slug,
                    'name' => $c->user?->name ?? 'Candidate',
                    'role' => $c->title ?? 'Maritime Professional',
                    'location' => $c->location?->name ?? $c->address ?? '—',
                    'experience' => $c->experience_years ? $c->experience_years . ' years' : 'Experience varies',
                    'availability' => $c->is_available ? 'Available' : '—',
                    'is_featured' => $c->isFeatured() || $c->getAttribute('_premium_featured') === true,
                ])->all();
            }
        }

        return view('pages.candidates.index', [
            'pageTitle' => 'Candidate Directory',
            'pageDescription' => 'Discover verified maritime talent ready for global deployment.',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Candidates'],
            ],
            'candidates' => $candidates,
        ]);
    }

    public function candidateDetail(string $slug)
    {
        $candidate = [
            'slug' => $slug,
            'name' => ucwords(str_replace('-', ' ', $slug)),
            'role' => 'Marine Professional',
            'location' => 'Global Mobility',
            'experience' => '10+ years',
            'availability' => 'Immediate',
            'summary' => 'Experienced maritime professional with proven vessel operations, safety compliance, and cross-cultural crew leadership.',
            'certifications' => ['STCW', 'GMDSS', 'Advanced Fire Fighting'],
        ];

        return view('pages.candidates.detail', [
            'pageTitle' => 'Candidate Profile',
            'pageDescription' => "Viewing profile: {$candidate['name']}",
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Candidates', 'url' => route('candidate.index')],
                ['label' => $candidate['name']],
            ],
            'candidate' => $candidate,
        ]);
    }

    public function companiesIndex()
    {
        $companies = [
            ['slug' => 'blue-star-lines', 'name' => 'Blue Star Lines', 'location' => 'Singapore', 'open_roles' => 14, 'sector' => 'Shipping'],
            ['slug' => 'atlantic-offshore', 'name' => 'Atlantic Offshore', 'location' => 'Aberdeen, UK', 'open_roles' => 9, 'sector' => 'Offshore Energy'],
            ['slug' => 'marinecrest-logistics', 'name' => 'MarineCrest Logistics', 'location' => 'Dubai, UAE', 'open_roles' => 11, 'sector' => 'Marine Logistics'],
        ];

        return view('pages.companies.index', [
            'pageTitle' => 'Company Directory',
            'pageDescription' => 'Explore top maritime and offshore employers hiring globally.',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Companies'],
            ],
            'companies' => $companies,
        ]);
    }

    public function companyDetail(string $slug)
    {
        $company = [
            'slug' => $slug,
            'name' => ucwords(str_replace('-', ' ', $slug)),
            'location' => 'Global Offices',
            'sector' => 'Maritime & Offshore',
            'open_roles' => 12,
            'about' => 'Global maritime employer focused on vessel operations, offshore support, and long-term crew development.',
            'benefits' => ['International deployment', 'Structured training pathway', 'Performance incentives'],
        ];

        return view('pages.companies.detail', [
            'pageTitle' => 'Company Profile',
            'pageDescription' => "Viewing company: {$company['name']}",
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Companies', 'url' => route('companies.index')],
                ['label' => $company['name']],
            ],
            'company' => $company,
        ]);
    }

    public function newsIndex()
    {
        $articles = $this->fallbackNewsArticles();

        if (Schema::hasTable('news_articles')) {
            $storedArticles = NewsArticle::published()
                ->orderBy('sort_order')
                ->orderByDesc('published_at')
                ->get();

            if ($storedArticles->isNotEmpty()) {
                $articles = $storedArticles
                    ->map(fn (NewsArticle $article) => $this->newsArticleForCard($article))
                    ->all();
            }
        }

        return view('pages.news.index', [
            'pageTitle' => 'News & Insights',
            'pageDescription' => 'Latest updates from maritime and offshore industries.',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'News'],
            ],
            'articles' => $articles,
        ]);
    }

    public function newsDetail(string $slug)
    {
        $article = null;
        $related = [];

        if (Schema::hasTable('news_articles')) {
            $storedArticle = NewsArticle::published()->where('slug', $slug)->first();

            if ($storedArticle) {
                $article = $this->newsArticleForDetail($storedArticle);
                $related = $this->relatedNewsArticles($storedArticle);
            }
        }

        $article ??= collect($this->fallbackNewsArticles())
            ->firstWhere('slug', $slug)
            ?? $this->generatedNewsArticle($slug);

        // Fallback related articles when DB lookup yielded nothing
        if (empty($related)) {
            $related = collect($this->fallbackNewsArticles())
                ->where('slug', '!=', $article['slug'] ?? null)
                ->take(3)
                ->values()
                ->all();
        }

        return view('pages.news.detail', [
            'pageTitle' => 'News Detail',
            'pageDescription' => "Article: {$article['title']}",
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'News', 'url' => route('news.index')],
                ['label' => $article['title']],
            ],
            'article' => $article,
            'related' => $related,
            'social' => app(\App\Support\Settings::class)->group('social'),
        ]);
    }

    private function fallbackNewsArticles(): array
    {
        return [
            [
                'slug' => 'offshore-safety-updates-2026',
                'title' => 'Offshore Safety Updates for 2026',
                'excerpt' => 'A practical summary of new compliance expectations impacting offshore crews and operators.',
                'author' => 'JCL Editorial',
                'date' => 'Mar 20, 2026',
                'category' => 'Safety',
                'image_url' => 'https://images.unsplash.com/photo-1605281317010-fe5ffe798166?w=1200&q=80',
                'content' => [
                    'Offshore safety requirements continue to evolve as operators respond to tighter compliance expectations, higher client scrutiny, and more complex deployment environments.',
                    'Crews should expect stronger emphasis on documented risk assessment, incident reporting, permit-to-work discipline, and recurring emergency response drills.',
                    'Employers can reduce delays by confirming that worker certifications, medical records, and safety training evidence are complete before mobilization.',
                ],
            ],
            [
                'slug' => 'global-maritime-hiring-trends',
                'title' => 'Global Maritime Hiring Trends This Quarter',
                'excerpt' => 'Demand is rising for deck officers, engineers, and dynamic positioning specialists.',
                'author' => 'Market Insights Team',
                'date' => 'Mar 12, 2026',
                'category' => 'Hiring',
                'image_url' => 'https://images.unsplash.com/photo-1494412519320-aa613dfb7738?w=1200&q=80',
                'content' => [
                    'Global maritime hiring remains active, with employers prioritizing qualified deck officers, marine engineers, offshore support crews, and dynamic positioning specialists.',
                    'Verified documents and current competency records are increasingly important because employers are shortening recruitment windows for urgent placements.',
                    'Candidates who maintain updated profiles, clear availability dates, and validated certificates are being matched faster across maritime and offshore roles.',
                ],
            ],
            [
                'slug' => 'stcw-certification-pathways',
                'title' => 'STCW Certification Pathways Explained',
                'excerpt' => 'Understanding the route from basic safety to advanced endorsements and deployment readiness.',
                'author' => 'Training Desk',
                'date' => 'Mar 03, 2026',
                'category' => 'Training',
                'image_url' => 'https://images.unsplash.com/photo-1473221326025-9183b464bb7e?w=1200&q=80',
                'content' => [
                    'STCW certification provides the foundation for safe and compliant seafaring work, starting with basic safety training and progressing into role-specific endorsements.',
                    'Professionals should understand renewal timelines, refresher requirements, and the supporting medical and identity documentation needed for deployment.',
                    'A structured certification pathway helps candidates plan training investments and gives employers greater confidence in workforce readiness.',
                ],
            ],
        ];
    }

    private function generatedNewsArticle(string $slug): array
    {
        return [
            'slug' => $slug,
            'title' => ucwords(str_replace('-', ' ', $slug)),
            'author' => 'JCL Editorial',
            'date' => 'Mar 27, 2026',
            'category' => 'Insights',
            'content' => [
                'Maritime hiring and training continue to evolve as operators prioritize safety, compliance, and technical readiness.',
                'Candidates with complete documentation and verified certifications are being matched faster for global placements.',
                'Employers are encouraged to align role requirements with modern competency frameworks to improve retention and deployment success.',
            ],
        ];
    }

    private function newsArticleForCard(NewsArticle $article): array
    {
        return [
            'slug' => $article->slug,
            'title' => $article->title,
            'excerpt' => $article->excerpt,
            'author' => $article->author,
            'date' => $article->published_at?->format('M d, Y') ?? 'Draft',
            'category' => $article->category,
            'image_url' => $article->image_url,
        ];
    }

    private function newsArticleForDetail(NewsArticle $article): array
    {
        $paragraphs = $article->content ?? [];
        $wordCount = str_word_count(strip_tags(implode(' ', $paragraphs)));
        $readMinutes = max(1, (int) ceil($wordCount / 200));

        return array_merge($this->newsArticleForCard($article), [
            'content' => $paragraphs,
            'read_minutes' => $readMinutes,
            'word_count' => $wordCount,
        ]);
    }

    private function relatedNewsArticles(NewsArticle $current, int $limit = 3): array
    {
        // Prefer same-category matches; if there aren't enough, top up with the latest from any category.
        $sameCategory = NewsArticle::published()
            ->where('id', '!=', $current->id)
            ->where('category', $current->category)
            ->orderByDesc('published_at')
            ->take($limit)
            ->get();

        if ($sameCategory->count() < $limit) {
            $excludeIds = $sameCategory->pluck('id')->push($current->id)->all();
            $topUp = NewsArticle::published()
                ->whereNotIn('id', $excludeIds)
                ->orderByDesc('published_at')
                ->take($limit - $sameCategory->count())
                ->get();
            $sameCategory = $sameCategory->concat($topUp);
        }

        return $sameCategory
            ->map(fn (NewsArticle $a) => $this->newsArticleForCard($a))
            ->all();
    }

    public function contact()
    {
        $profile = JclProfileContent::company();

        return view('pages.contact.index', [
            'pageTitle' => 'Contact JCL',
            'pageDescription' => 'Start a conversation about career pathways, consulting, partnerships, or training.',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Contact JCL'],
            ],
            'profile' => $profile,
            'contactPathways' => $profile['contact_pathways'],
            'jclImages' => JclProfileContent::images(),
        ]);
    }

    public function plan()
    {
        return view('pages.plan.index', [
            'pageTitle' => 'Plans & Pricing',
            'pageDescription' => 'Choose a plan that fits your hiring or career goals.',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Plans'],
            ],
            'plans' => [
                ['name' => 'Starter', 'price' => '$49/mo', 'features' => ['1 active posting', 'Basic candidate filters', 'Email support']],
                ['name' => 'Professional', 'price' => '$129/mo', 'features' => ['5 active postings', 'Advanced filters', 'Priority matching']],
                ['name' => 'Enterprise', 'price' => 'Custom', 'features' => ['Unlimited postings', 'Dedicated account manager', 'Custom integrations']],
            ],
        ]);
    }

    public function cms(string $slug)
    {
        $title = ucwords(str_replace('-', ' ', $slug));

        return view('pages.cms.detail', [
            'pageTitle' => $title,
            'pageDescription' => 'Dynamic CMS page.',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => $title],
            ],
            'slug' => $slug,
            'contentBlocks' => [
                'This is a dynamic CMS template ready for per-page content integration.',
                'Use this as the shared skeleton for About, Terms, FAQs, and future static informational pages.',
            ],
        ]);
    }

    public function services()
    {
        $profile = JclProfileContent::company();

        return view('pages.services.index', $this->buildJclPageData(
            title: 'Our Services',
            description: 'Explore JCL\'s full range of maritime and energy workforce services.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services'],
            ],
            extra: ['profile' => $profile],
        ));
    }

    public function servicesTraining()
    {
        $profile = JclProfileContent::company();

        $dbPrograms = collect();
        if (Schema::hasTable('training_programs')) {
            $dbPrograms = TrainingProgram::active()
                ->ofType(TrainingProgram::TYPE_TRAINING)
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->take(12)
                ->get();
        }

        return view('pages.services.training', $this->buildJclPageData(
            title: 'Training',
            description: 'Professional training programs aligned to international maritime and energy standards.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Training'],
            ],
            extra: [
                'profile' => $profile,
                'programs' => $profile['training_programs'],
                'dbPrograms' => $dbPrograms,
            ],
        ));
    }

    public function servicesTrainingSoft()
    {
        return view('pages.services.soft-skills', $this->buildJclPageData(
            title: 'Soft Skills Training',
            description: 'Communication, leadership, and workplace effectiveness programs for maritime and energy professionals.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Training', 'url' => route('services.training')],
                ['label' => 'Soft Skills'],
            ],
        ));
    }

    public function servicesTrainingTechnical()
    {
        return view('pages.services.technical-skills', $this->buildJclPageData(
            title: 'Technical & Non Technical Skills',
            description: 'Industry-aligned technical and operational skills training for maritime and energy sector professionals.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Training', 'url' => route('services.training')],
                ['label' => 'Technical & Non Technical Skills'],
            ],
        ));
    }

    public function servicesCrewManagement()
    {
        return view('pages.services.crew-management', $this->buildJclPageData(
            title: 'Crew Management',
            description: 'End-to-end crew management solutions for vessel operators and offshore employers.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Crew Management'],
            ],
        ));
    }

    public function servicesShipChandelling()
    {
        return view('pages.services.ship-chandelling', $this->buildJclPageData(
            title: 'Ship Chandelling',
            description: 'Comprehensive ship chandelling and vessel supply services for maritime operations.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Ship Chandelling'],
            ],
        ));
    }

    public function servicesCrewAbandonment()
    {
        return view('pages.services.crew-abandonment', $this->buildJclPageData(
            title: 'Solution to Crew Abandonment',
            description: 'Specialist support and resolution services for crew abandonment situations.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Solution to Crew Abandonment'],
            ],
        ));
    }

    public function servicesMarineProcurement()
    {
        return view('pages.services.marine-procurement', $this->buildJclPageData(
            title: 'Marine Procurement',
            description: 'Strategic marine procurement services for vessel operators and offshore projects.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Marine Procurement'],
            ],
        ));
    }

    public function servicesMarineInsurance()
    {
        return view('pages.services.marine-insurance', $this->buildJclPageData(
            title: 'Marine Insurance',
            description: 'Marine insurance advisory and placement services for maritime professionals and operators.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Marine Insurance'],
            ],
        ));
    }

    public function servicesTravelManagement()
    {
        return view('pages.services.travel-management', $this->buildJclPageData(
            title: 'Travel Management Service',
            description: 'End-to-end travel management for crew, maritime professionals, and offshore personnel.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Travel Management Service'],
            ],
        ));
    }

    public function career()
    {
        return view('pages.career.index', $this->buildJclPageData(
            title: 'Career Pathways',
            description: 'Explore apprenticeship and internship opportunities with Jose Consulting Limited.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Career'],
            ],
        ));
    }

    public function careerApprenticeship(Request $request)
    {
        return $this->renderTrainingListing($request, 'apprenticeship');
    }

    public function careerInternship()
    {
        return view('pages.career.internship', $this->buildJclPageData(
            title: 'Internship',
            description: 'Professional internship placements in maritime, logistics, and energy organisations.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Career', 'url' => route('career.index')],
                ['label' => 'Internship'],
            ],
        ));
    }

    public function events()
    {
        $profile = JclProfileContent::company();
        $events = $profile['events'];
        $industryEvents = $profile['industry_events'];

        if (Schema::hasTable('events')) {
            $storedEvents = Event::published()
                ->orderBy('sort_order')
                ->orderByRaw('starts_at is null')
                ->orderBy('starts_at')
                ->get();

            if ($storedEvents->isNotEmpty()) {
                $events = $storedEvents->where('category', 'hosted')
                    ->values()
                    ->map(fn (Event $event) => $this->eventForView($event))
                    ->all();

                $industryEvents = $storedEvents->where('category', 'industry')
                    ->values()
                    ->map(fn (Event $event) => $this->eventForView($event))
                    ->all();
            }
        }

        return view('pages.events.index', $this->buildJclPageData(
            title: 'Events',
            description: 'JCL-hosted events, industry conferences, and maritime sector gatherings.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Events'],
            ],
            extra: [
                'profile' => $profile,
                'events' => $events,
                'industry_events' => $industryEvents,
            ],
        ));
    }

    private function eventForView(Event $event): array
    {
        return [
            'id' => $event->id,
            'title' => $event->title,
            'type' => $event->type,
            'date' => $event->display_date,
            'starts_at' => $event->starts_at,
            'location' => $event->location,
            'description' => $event->description,
            'status' => $event->status,
            'is_featured' => (bool) $event->is_featured,
            'image_url' => $event->image_url,
            'register_url' => $event->register_url,
            // Internal-ticketing fields
            'price' => $event->price,
            'currency' => $event->currency,
            'capacity' => $event->capacity,
            'seats_remaining' => $event->seatsRemaining(),
            'is_paid' => $event->isPaid(),
            'is_free_internal' => $event->isFreeInternal(),
            'is_sold_out' => $event->isSoldOut(),
            'register_show_url' => route('events.register.show', $event),
        ];
    }

    private function buildJclPageData(string $title, string $description, array $breadcrumbs, array $extra = []): array
    {
        return array_merge([
            'pageTitle' => $title,
            'pageDescription' => $description,
            'breadcrumbs' => $breadcrumbs,
            'jclImages' => JclProfileContent::images(),
            'seo_meta' => [
                'title' => $title.' — Jose Consulting Limited (JCL)',
                'description' => $description,
                'full_url' => request()->url(),
            ],
        ], $extra);
    }
}
