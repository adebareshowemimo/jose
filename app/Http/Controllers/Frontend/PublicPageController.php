<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Candidate;
use App\Models\CandidateProfileView;
use App\Models\Event;
use App\Models\JobListing;
use App\Models\NewsArticle;
use App\Models\RecruitmentRequestCandidate;
use App\Models\TrainingCategory;
use App\Models\TrainingProgram;
use App\Models\Wishlist;
use App\Support\JclLegalContent;
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
    public function termsOfService()
    {
        return view('pages.legal.terms-of-service', $this->buildJclPageData(
            title: 'Terms of Service',
            description: 'The terms governing your use of the JoseOceanJobs platform, operated by Jose Consulting Limited.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Terms of Service'],
            ],
            extra: [
                'terms' => JclLegalContent::termsOfService(),
            ],
        ));
    }

    public function definitions()
    {
        return view('pages.legal.definition-of-terms', $this->buildJclPageData(
            title: 'Definition of Terms',
            description: 'Key terms and expressions used throughout the JoseOceanJobs Terms of Service.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Definition of Terms'],
            ],
            extra: [
                'glossary' => JclLegalContent::definitions(),
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
    public function jobsIndex(Request $request)
    {
        // `keyword` is this page's own field; `s` is the global header search field.
        $keyword = $request->input('keyword', $request->input('s'));

        $query = JobListing::regularJobs()
            ->where('status', 'active')
            ->where('is_approved', true)
            ->with(['company', 'location', 'jobType', 'category']);

        if (filled($keyword)) {
            $term = (string) $keyword;
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            });
        }

        if ($request->filled('location')) {
            $loc = (string) $request->input('location');
            $query->where(function ($q) use ($loc) {
                $q->where('address', 'like', "%{$loc}%")
                  ->orWhereHas('location', fn ($l) => $l->where('name', 'like', "%{$loc}%"));
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        if ($request->filled('type')) {
            $query->whereIn('job_type_id', array_filter((array) $request->input('type')));
        }

        if ($request->filled('salary')) {
            $buckets = array_filter((array) $request->input('salary'));
            $query->where(function ($q) use ($buckets) {
                foreach ($buckets as $bucket) {
                    [$min, $max] = $this->salaryBucketBounds((string) $bucket);
                    $q->orWhere(function ($qq) use ($min, $max) {
                        $qq->whereNotNull('salary_min')->where('salary_min', '>=', $min);
                        if ($max !== null) {
                            $qq->where('salary_min', '<=', $max);
                        }
                    });
                }
            });
        }

        switch ($request->input('sort')) {
            case 'newest':
                $query->orderByDesc('id');
                break;
            case 'salary':
                $query->orderByDesc('salary_max')->orderByDesc('salary_min')->orderByDesc('id');
                break;
            default: // most relevant
                $query->orderByDesc('is_featured')->orderByDesc('is_urgent')->orderByDesc('id');
        }

        $jobs = $query->paginate(10)->withQueryString();

        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();
        $jobTypes = \App\Models\JobType::orderBy('name')->get();

        // So the saved/bookmark heart can reflect current state.
        $savedJobIds = [];
        if ($user = $request->user()) {
            $savedJobIds = \App\Models\Wishlist::where('user_id', $user->id)
                ->where('wishlistable_type', JobListing::class)
                ->pluck('wishlistable_id')
                ->all();
        }

        return view('pages.jobs.index', [
            'pageTitle' => 'Find Maritime Jobs',
            'pageDescription' => 'Browse maritime, logistics, and energy sector opportunities.',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Jobs'],
            ],
            'jobs' => $jobs,
            'categories' => $categories,
            'jobTypes' => $jobTypes,
            'savedJobIds' => $savedJobIds,
            'keyword' => $keyword,
        ]);
    }

    /**
     * Lower/upper monthly-salary bounds for a salary-range filter bucket.
     * Upper bound of null means "and above".
     *
     * @return array{0:int,1:?int}
     */
    private function salaryBucketBounds(string $bucket): array
    {
        return match ($bucket) {
            '0-5000' => [0, 5000],
            '5000-10000' => [5000, 10000],
            '10000-15000' => [10000, 15000],
            '15000+' => [15000, null],
            default => [0, null],
        };
    }

    public function jobDetail(string $slug)
    {
        $listing = JobListing::regularJobs()
            ->with(['company', 'location', 'jobType'])
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
                'jobListing' => $listing,
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
        $category = \App\Models\Category::where('slug', $slug)->where('is_active', true)->first();
        abort_unless($category, 404);

        $listings = JobListing::regularJobs()
            ->where('status', 'active')
            ->where('is_approved', true)
            ->where('category_id', $category->id)
            ->with(['company', 'location', 'jobType'])
            ->orderByDesc('is_featured')
            ->orderByDesc('is_urgent')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $jobs = $listings->getCollection()->map(fn (JobListing $l) => [
            'slug' => $l->slug,
            'title' => $l->title,
            'company' => $l->company?->name ?? 'Confidential',
            'location' => $l->location?->name ?? $l->address ?? 'Worldwide',
            'type' => $l->jobType?->name ?? ucfirst((string) $l->hours_type ?: 'Job'),
            'salary' => $this->formatSalary($l),
        ])->all();

        return view('pages.jobs.category', [
            'pageTitle' => "{$category->name} Jobs",
            'pageDescription' => "Open {$category->name} roles in maritime, logistics, and energy.",
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Jobs', 'url' => route('job.index')],
                ['label' => $category->name],
            ],
            'category' => $category->name,
            'jobs' => $jobs,
            'paginator' => $listings,
        ]);
    }

    /**
     * Human-readable salary string for a job listing, or "Negotiable" when undisclosed.
     */
    private function formatSalary(JobListing $job): string
    {
        if (! $job->salary_min && ! $job->salary_max) {
            return 'Negotiable';
        }

        $range = trim(
            ($job->salary_min ? '$'.number_format((float) $job->salary_min) : '')
            .($job->salary_min && $job->salary_max ? ' - ' : '')
            .($job->salary_max ? '$'.number_format((float) $job->salary_max) : '')
        );

        return $job->salary_type ? "{$range} / {$job->salary_type}" : $range;
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
        $candidate = Candidate::with(['user', 'location', 'skills', 'resumes' => fn ($q) => $q->latest()])
            ->where('slug', $slug)
            ->first();

        abort_unless($candidate, 404);

        $user = auth()->user();
        $roleName = $user?->role?->name;

        // Employers don't browse candidate profiles directly. They may only view a profile
        // for a candidate that has actually been delivered to one of their own recruitment
        // requests. Any other candidate is off-limits, so send them back to that list.
        if ($roleName === 'employer') {
            $companyId = $user->company?->id;

            // A candidate is viewable to this employer when they've been delivered to a
            // recruitment request that belongs to the employer — linked either by the user
            // who raised the request or by the employer's company. We check both because a
            // request may have been created/delivered before the company profile was set,
            // leaving company_id null while requested_by_user_id still points at the employer.
            $isDeliveredToEmployer = RecruitmentRequestCandidate::query()
                ->where('candidate_id', $candidate->id)
                ->whereHas('recruitmentRequest', function ($q) use ($user, $companyId) {
                    $q->where(function ($sub) use ($user, $companyId) {
                        $sub->where('requested_by_user_id', $user->id);
                        if ($companyId) {
                            $sub->orWhere('company_id', $companyId);
                        }
                    });
                })
                ->exists();

            if (! $isDeliveredToEmployer) {
                return redirect()
                    ->route('employer.recruitment-requests.index')
                    ->with('error', 'Candidate profiles are available through the candidates delivered to your recruitment requests.');
            }

            CandidateProfileView::record($candidate, $user, 'employer');

            return view('pages.candidates.detail', [
                'pageTitle' => $candidate->user?->name ?? 'Candidate',
                'pageDescription' => 'Candidate profile: ' . ($candidate->user?->name ?? 'Candidate'),
                'breadcrumbs' => [
                    ['label' => 'Home', 'url' => url('/')],
                    ['label' => 'Recruitment Requests', 'url' => route('employer.recruitment-requests.index')],
                    ['label' => $candidate->user?->name ?? 'Candidate'],
                ],
                'candidate' => $candidate,
                'isAdmin' => false,
                'isOwner' => false,
            ]);
        }

        // This is a private profile page: only an admin or the candidate themselves may
        // view it. Guests are sent to sign in; any other authenticated user is turned away.
        if (! $user) {
            return redirect()
                ->route('auth.login')
                ->with('error', 'Please sign in to view candidate profiles.');
        }

        $isAdmin = $roleName === 'admin';
        $isOwner = $candidate->user_id === $user->id;

        abort_unless($isAdmin || $isOwner, 404);

        $name = $candidate->user?->name ?? 'Candidate';

        // record() ignores the candidate viewing their own profile, so this only
        // counts genuine admin views.
        $viewSource = $isAdmin ? 'admin' : 'public';
        CandidateProfileView::record($candidate, $user, $viewSource);

        return view('pages.candidates.detail', [
            'pageTitle' => $name,
            'pageDescription' => "Candidate profile: {$name}",
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Candidates', 'url' => route('candidate.index')],
                ['label' => $name],
            ],
            'candidate' => $candidate,
            'isAdmin' => $isAdmin,
            'isOwner' => $isOwner,
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
                'image_url' => asset('images/premium/safety-officer.jpg'),
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
                'image_url' => asset('images/premium/container-port.jpg'),
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
                'image_url' => asset('images/premium/deck-officer.jpg'),
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
            'contactSubjects' => app(\App\Support\ContactRoutes::class)->subjectLabels(),
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
        $categories = collect();
        if (Schema::hasTable('training_programs')) {
            $dbPrograms = TrainingProgram::active()
                ->ofType(TrainingProgram::TYPE_TRAINING)
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->take(12)
                ->get();
        }
        if (Schema::hasTable('training_categories')) {
            $categories = TrainingCategory::active()->orderBy('sort_order')->orderBy('name')->get();
        }

        return view('pages.services.training', $this->buildJclPageData(
            title: 'Training and Certifications',
            description: 'Professional training and certification programs aligned to international maritime and energy standards.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Training and Certifications'],
            ],
            extra: [
                'profile' => $profile,
                'programs' => $profile['training_programs'],
                'dbPrograms' => $dbPrograms,
                'categories' => $categories,
            ],
        ));
    }

    public function servicesTrainingCategory(string $slug)
    {
        if (! Schema::hasTable('training_categories')) {
            abort(404);
        }

        $category = TrainingCategory::active()->where('slug', $slug)->firstOrFail();

        $dbPrograms = TrainingProgram::active()
            ->ofType(TrainingProgram::TYPE_TRAINING)
            ->where('training_category_id', $category->id)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->take(24)
            ->get();

        return view('pages.services.training-category', $this->buildJclPageData(
            title: $category->name,
            description: $category->short_description ?: ('Training programs in ' . $category->name . '.'),
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Training and Certifications', 'url' => route('services.training')],
                ['label' => $category->name],
            ],
            extra: [
                'category' => $category,
                'dbPrograms' => $dbPrograms,
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
            title: 'Crew Abandonment Support',
            description: 'Specialist support and resolution services for crew abandonment situations.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Crew Abandonment Support'],
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
            title: 'Marine Travel',
            description: 'End-to-end marine travel for crew, maritime professionals, and offshore personnel.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Marine Travel'],
            ],
        ));
    }

    public function servicesSelfEmploymentSetup()
    {
        return view('pages.services.self-employment-setup', $this->buildJclPageData(
            title: 'Self Employment Setup',
            description: 'Launch your own consulting practice, agency, or freelance career — with JCL\'s structure and support.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Self Employment Setup'],
            ],
        ));
    }

    public function servicesGlobalOpportunity()
    {
        return view('pages.services.global-opportunity', $this->buildJclPageData(
            title: 'Global Opportunity',
            description: 'International placements, secondments, and cross-border careers for ambitious professionals.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Global Opportunity'],
            ],
        ));
    }

    public function servicesAcademicPartnerships()
    {
        return view('pages.services.academic-partnerships', $this->buildJclPageData(
            title: 'Academic Partnerships',
            description: 'Bridging maritime academies, technical institutes, and industry to build the next generation of professionals.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Academic Partnerships'],
            ],
        ));
    }

    public function servicesBusinessPartnership()
    {
        return view('pages.services.business-partnership', $this->buildJclPageData(
            title: 'Business Partnership',
            description: 'Operational partnerships — procurement, crewing, insurance, and mobilization under one roof.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Business Partnership'],
            ],
        ));
    }

    public function servicesMobilization()
    {
        return view('pages.services.mobilization-services', $this->buildJclPageData(
            title: 'Mobilization Services',
            description: 'Personnel mobilization — visas, flights, briefings, and on-arrival logistics handled end-to-end.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Business Partnership', 'url' => route('services.business-partnership')],
                ['label' => 'Mobilization Services'],
            ],
        ));
    }

    public function servicesContractStaffing()
    {
        $featuredJobs = JobListing::contractStaffing()
            ->where('status', 'active')
            ->orderByDesc('is_featured')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        return view('pages.services.contract-staffing', $this->buildJclPageData(
            title: 'Contract Staffing',
            description: 'Flexible contract staffing solutions for maritime, energy, and offshore operations — sourced and vetted by JCL.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Contract Staffing'],
            ],
            extra: [
                'featuredJobs' => $featuredJobs,
            ],
        ));
    }

    public function contractStaffingJobs(Request $request)
    {
        $query = JobListing::contractStaffing()
            ->where('status', 'active')
            ->with(['category', 'location', 'jobType']);

        if ($request->filled('q')) {
            $term = (string) $request->input('q');
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            });
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }
        if ($request->filled('location')) {
            $query->where('location_id', $request->input('location'));
        }

        $jobs = $query->orderByDesc('is_featured')->orderByDesc('id')->paginate(12)->withQueryString();

        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();
        $locations = \App\Models\Location::orderBy('name')->get();

        return view('pages.services.contract-staffing-jobs', $this->buildJclPageData(
            title: 'Open Contract Staffing Roles',
            description: 'Active contract staffing opportunities with JCL.',
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Contract Staffing', 'url' => route('services.contract-staffing')],
                ['label' => 'Open Roles'],
            ],
            extra: [
                'jobs' => $jobs,
                'categories' => $categories,
                'locations' => $locations,
            ],
        ));
    }

    public function contractStaffingJobShow(string $slug)
    {
        $job = JobListing::contractStaffing()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->with(['category', 'location', 'jobType', 'company'])
            ->firstOrFail();

        return view('pages.services.contract-staffing-detail', $this->buildJclPageData(
            title: $job->title,
            description: \Illuminate\Support\Str::limit(strip_tags((string) $job->description), 160),
            breadcrumbs: [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Services', 'url' => route('services.index')],
                ['label' => 'Contract Staffing', 'url' => route('services.contract-staffing')],
                ['label' => 'Open Roles', 'url' => route('services.contract-staffing.jobs')],
                ['label' => $job->title],
            ],
            extra: [
                'job' => $job,
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
