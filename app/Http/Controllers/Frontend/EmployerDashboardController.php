<?php

namespace App\Http\Controllers\Frontend;

use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\Candidate;
use App\Models\Category;
use App\Models\JobType;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmployerDashboardController extends BasePageController
{
    public function dashboard()
    {
        $user = auth()->user();
        $company = $user->company;

        if (!$company) {
            return view('pages.dashboard.employer.dashboard', [
                'dashboardType' => 'employer',
                'section' => 'Employer Dashboard',
                'pageTitle' => 'Employer Dashboard',
                'pageDescription' => 'Create a company profile to get started.',
                'breadcrumbs' => [
                    ['label' => 'Home', 'url' => url('/')],
                    ['label' => 'Employer Dashboard'],
                ],
                'postedJobs' => 0,
                'applications' => 0,
                'shortlisted' => 0,
                'closingSoon' => 0,
                'recentApplicants' => collect(),
                'recentNotifications' => collect(),
                'chartData' => collect(),
            ]);
        }

        $jobListingIds = $company->jobListings()->pluck('id');

        // Stats
        $postedJobs = $company->jobListings()->where('status', 'active')->count();
        $applications = JobApplication::whereIn('job_listing_id', $jobListingIds)->count();
        $shortlisted = JobApplication::whereIn('job_listing_id', $jobListingIds)
            ->where('status', 'shortlisted')->count();
        $closingSoon = $company->jobListings()
            ->where('status', 'active')
            ->where('deadline', '<=', now()->addDays(7))
            ->where('deadline', '>=', now())
            ->count();

        // Recent applicants (latest 6) with candidate + job info
        $recentApplicants = JobApplication::whereIn('job_listing_id', $jobListingIds)
            ->with(['candidate.user', 'candidate.location', 'candidate.skills', 'jobListing'])
            ->latest()
            ->take(6)
            ->get();

        // Recent applications as notification feed (latest 5)
        $recentNotifications = JobApplication::whereIn('job_listing_id', $jobListingIds)
            ->with(['candidate.user', 'jobListing'])
            ->latest()
            ->take(5)
            ->get();

        // Monthly stats for chart (last 6 months)
        $chartData = collect(range(5, 0))->map(function ($monthsAgo) use ($company) {
            $start = now()->subMonths($monthsAgo)->startOfMonth();
            $end = now()->subMonths($monthsAgo)->endOfMonth();
            $jobIds = $company->jobListings()->pluck('id');

            return [
                'month' => $start->format('M'),
                'applications' => JobApplication::whereIn('job_listing_id', $jobIds)
                    ->whereBetween('created_at', [$start, $end])
                    ->count(),
            ];
        })->values();

        return view('pages.dashboard.employer.dashboard', [
            'dashboardType' => 'employer',
            'section' => 'Employer Dashboard',
            'pageTitle' => 'Employer Dashboard',
            'pageDescription' => 'Monitor hiring analytics and top recruitment actions.',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Employer Dashboard'],
            ],
            'postedJobs' => $postedJobs,
            'applications' => $applications,
            'shortlisted' => $shortlisted,
            'closingSoon' => $closingSoon,
            'recentApplicants' => $recentApplicants,
            'recentNotifications' => $recentNotifications,
            'chartData' => $chartData,
        ]);
    }

    public function companyProfile()
    {
        $user = auth()->user();
        $company = $user->company;
        $industries = \App\Models\Industry::where('is_active', true)->orderBy('name')->get();
        $locations = \App\Models\Location::where('type', 'country')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('pages.dashboard.employer.company-profile', [
            'dashboardType' => 'employer',
            'section' => 'Employer Dashboard',
            'pageTitle' => 'Company Profile',
            'pageDescription' => 'Manage company profile visibility and brand trust signals.',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Employer Dashboard', 'url' => route('employer.dashboard')],
                ['label' => 'Company Profile'],
            ],
            'company' => $company,
            'industries' => $industries,
            'locations' => $locations,
        ]);
    }

    public function updateCompanyProfile()
    {
        $user = auth()->user();
        $company = $user->company;

        $validated = request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'about' => 'nullable|string|max:5000',
            'founded_in' => 'nullable|integer|min:1800|max:' . now()->year,
            'company_size' => 'nullable|in:1-10,11-50,51-200,201-500,501-1000,1000+',
            'address' => 'nullable|string|max:500',
            'location_id' => [
                'nullable',
                \Illuminate\Validation\Rule::exists('locations', 'id')->where('type', 'country'),
            ],
            'industry_ids' => 'nullable|array',
            'industry_ids.*' => 'exists:industries,id',
            'social_links.linkedin' => 'nullable|url|max:255',
            'social_links.twitter' => 'nullable|url|max:255',
            'social_links.facebook' => 'nullable|url|max:255',
            'social_links.instagram' => 'nullable|url|max:255',
        ]);

        if (!$company) {
            $validated['owner_id'] = $user->id;
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
            $company = \App\Models\Company::create($validated);
        } else {
            $company->update(collect($validated)->except('industry_ids')->toArray());
        }

        if (isset($validated['industry_ids'])) {
            $company->industries()->sync($validated['industry_ids']);
        }

        return back()->with('success', 'Company profile updated successfully.');
    }

    public function updateCompanyLogo()
    {
        request()->validate([
            'logo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = auth()->user();
        $company = $user->company;

        if (!$company) {
            return back()->with('error', 'Create a company profile first.');
        }

        if ($company->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($company->logo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($company->logo);
        }

        $path = request()->file('logo')->store('companies/logos', 'public');
        $company->update(['logo' => $path]);

        return back()->with('success', 'Company logo updated successfully.');
    }

    public function deleteCompanyLogo()
    {
        $user = auth()->user();
        $company = $user->company;

        if ($company && $company->logo) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($company->logo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($company->logo);
            }
            $company->update(['logo' => null]);
        }

        return back()->with('success', 'Company logo removed.');
    }

    public function updateCompanyCover()
    {
        request()->validate([
            'cover_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $user = auth()->user();
        $company = $user->company;

        if (!$company) {
            return back()->with('error', 'Create a company profile first.');
        }

        if ($company->cover_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($company->cover_image)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($company->cover_image);
        }

        $path = request()->file('cover_image')->store('companies/covers', 'public');
        $company->update(['cover_image' => $path]);

        return back()->with('success', 'Cover banner updated successfully.');
    }

    public function postJob()
    {
        return view('pages.dashboard.employer.new-job', [
            'dashboardType' => 'employer',
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'jobTypes' => JobType::where('is_active', true)->orderBy('name')->get(),
            'locations' => Location::where('type', 'country')->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function storeJob(Request $request)
    {
        $company = $request->user()->company;

        if (! $company) {
            return back()->with('error', 'Create a company profile before posting a job.')->withInput();
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'job_type_id' => ['nullable', 'exists:job_types,id'],
            'location_id' => ['nullable', \Illuminate\Validation\Rule::exists('locations', 'id')->where('type', 'country')->where('is_active', true)],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'qualification' => ['nullable', 'string', 'max:5000'],
            'experience_required' => ['nullable', 'string', 'max:255'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'salary_type' => ['nullable', 'in:hourly,monthly,yearly'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
            'vacancies' => ['nullable', 'integer', 'min:1'],
            'hours' => ['nullable', 'string', 'max:255'],
            'hours_type' => ['nullable', 'in:full-time,part-time'],
            'is_featured' => ['nullable', 'boolean'],
            'is_urgent' => ['nullable', 'boolean'],
            'submit_action' => ['required', 'in:draft,submit'],
        ]);

        $baseSlug = Str::slug($data['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (JobListing::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter++;
        }

        JobListing::create([
            'company_id' => $company->id,
            'posted_by' => $request->user()->id,
            'title' => $data['title'],
            'slug' => $slug,
            'description' => $data['description'],
            'qualification' => $data['qualification'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'job_type_id' => $data['job_type_id'] ?? null,
            'location_id' => $data['location_id'] ?? null,
            'address' => $data['address'] ?? null,
            'salary_min' => $data['salary_min'] ?? null,
            'salary_max' => $data['salary_max'] ?? null,
            'salary_type' => $data['salary_type'] ?? null,
            'experience_required' => $data['experience_required'] ?? null,
            'deadline' => $data['deadline'] ?? null,
            'apply_method' => 'internal',
            'apply_url' => null,
            'apply_email' => null,
            'vacancies' => $data['vacancies'] ?? null,
            'hours' => $data['hours'] ?? null,
            'hours_type' => $data['hours_type'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'is_urgent' => $request->boolean('is_urgent'),
            'is_approved' => false,
            'status' => $data['submit_action'] === 'draft' ? 'draft' : 'pending',
        ]);

        $message = $data['submit_action'] === 'draft'
            ? 'Job saved as draft.'
            : 'Job submitted for admin review. It will go live after approval.';

        return redirect()->route('employer.manage-jobs')->with('success', $message);
    }

    public function manageJobs(Request $request)
    {
        $company = $request->user()->company;

        if (! $company) {
            return view('pages.dashboard.employer.manage-jobs', [
                'dashboardType' => 'employer',
                'jobs' => JobListing::query()->whereRaw('1 = 0')->paginate(10),
                'stats' => [
                    'total' => 0,
                    'active' => 0,
                    'expired' => 0,
                    'draft' => 0,
                    'closing_soon' => 0,
                ],
            ]);
        }

        $baseQuery = JobListing::where('company_id', $company->id);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('status', 'active')->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'expired' => (clone $baseQuery)->where('status', 'expired')->count(),
            'draft' => (clone $baseQuery)->where('status', 'draft')->count(),
            'closing_soon' => (clone $baseQuery)
                ->where('status', 'active')
                ->whereNotNull('deadline')
                ->whereBetween('deadline', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
                ->count(),
        ];

        $jobsQuery = JobListing::with(['location', 'jobType', 'category'])
            ->withCount('applications')
            ->where('company_id', $company->id);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $jobsQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('qualification', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $jobsQuery->where('status', $request->input('status'));
        }

        match ($request->input('sort')) {
            'oldest' => $jobsQuery->oldest(),
            'deadline' => $jobsQuery->orderByRaw('deadline is null')->orderBy('deadline'),
            'applications' => $jobsQuery->orderByDesc('applications_count')->latest(),
            default => $jobsQuery->latest(),
        };

        $jobs = $jobsQuery->paginate(10)->withQueryString();

        return view('pages.dashboard.employer.manage-jobs', [
            'dashboardType' => 'employer',
            'jobs' => $jobs,
            'stats' => $stats,
        ]);
    }

    public function editJob(Request $request, string $id)
    {
        $company = $request->user()->company;
        abort_unless($company, 403, 'Create a company profile before editing a job.');

        $job = JobListing::where('id', $id)
            ->where('company_id', $company->id)
            ->firstOrFail();

        return view('pages.dashboard.employer.edit-job', [
            'dashboardType' => 'employer',
            'job' => $job,
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'jobTypes' => JobType::where('is_active', true)->orderBy('name')->get(),
            'locations' => Location::where('type', 'country')->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function updateJob(Request $request, string $id)
    {
        $company = $request->user()->company;
        abort_unless($company, 403, 'Create a company profile before editing a job.');

        $job = JobListing::where('id', $id)
            ->where('company_id', $company->id)
            ->firstOrFail();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'job_type_id' => ['nullable', 'exists:job_types,id'],
            'location_id' => ['nullable', \Illuminate\Validation\Rule::exists('locations', 'id')->where('type', 'country')->where('is_active', true)],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'qualification' => ['nullable', 'string', 'max:5000'],
            'experience_required' => ['nullable', 'string', 'max:255'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'salary_type' => ['nullable', 'in:hourly,monthly,yearly'],
            'deadline' => ['nullable', 'date'],
            'vacancies' => ['nullable', 'integer', 'min:1'],
            'hours' => ['nullable', 'string', 'max:255'],
            'hours_type' => ['nullable', 'in:full-time,part-time'],
            'is_featured' => ['nullable', 'boolean'],
            'is_urgent' => ['nullable', 'boolean'],
        ]);

        $job->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'qualification' => $data['qualification'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'job_type_id' => $data['job_type_id'] ?? null,
            'location_id' => $data['location_id'] ?? null,
            'address' => $data['address'] ?? null,
            'salary_min' => $data['salary_min'] ?? null,
            'salary_max' => $data['salary_max'] ?? null,
            'salary_type' => $data['salary_type'] ?? null,
            'experience_required' => $data['experience_required'] ?? null,
            'deadline' => $data['deadline'] ?? null,
            'apply_method' => 'internal',
            'apply_url' => null,
            'apply_email' => null,
            'vacancies' => $data['vacancies'] ?? null,
            'hours' => $data['hours'] ?? null,
            'hours_type' => $data['hours_type'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'is_urgent' => $request->boolean('is_urgent'),
        ]);

        return redirect()->route('employer.manage-jobs')->with('success', 'Job updated successfully.');
    }

    public function applicants(Request $request)
    {
        $company = $request->user()->company;

        if (! $company) {
            return view('pages.dashboard.employer.applicants', [
                'dashboardType' => 'employer',
                'applications' => JobApplication::query()->whereRaw('1 = 0')->paginate(12),
                'jobs' => collect(),
                'stats' => [
                    'total' => 0,
                    'pending' => 0,
                    'shortlisted' => 0,
                    'rejected' => 0,
                ],
            ]);
        }

        $jobIds = $company->jobListings()->pluck('id');
        $jobs = $company->jobListings()->orderBy('title')->get(['id', 'title']);

        $baseQuery = JobApplication::whereIn('job_listing_id', $jobIds);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->whereIn('status', ['pending', 'applied'])->count(),
            'shortlisted' => (clone $baseQuery)->where('status', 'shortlisted')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];

        $applicationsQuery = JobApplication::with([
                'candidate.user',
                'candidate.location',
                'candidate.skills',
                'candidate.resumes',
                'jobListing',
                'resume',
            ])
            ->whereIn('job_listing_id', $jobIds);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $applicationsQuery->where(function ($query) use ($search) {
                $query->whereHas('candidate.user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('candidate', function ($candidateQuery) use ($search) {
                    $candidateQuery->where('title', 'like', "%{$search}%");
                })
                ->orWhereHas('jobListing', function ($jobQuery) use ($search) {
                    $jobQuery->where('title', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('job_id')) {
            $applicationsQuery->where('job_listing_id', $request->integer('job_id'));
        }

        if ($request->filled('status')) {
            $status = (string) $request->input('status');
            if ($status === 'pending') {
                $applicationsQuery->whereIn('status', ['pending', 'applied']);
            } else {
                $applicationsQuery->where('status', $status);
            }
        }

        match ($request->input('sort')) {
            'oldest' => $applicationsQuery->oldest(),
            'name' => $applicationsQuery
                ->join('candidates', 'job_applications.candidate_id', '=', 'candidates.id')
                ->join('users', 'candidates.user_id', '=', 'users.id')
                ->orderBy('users.name')
                ->select('job_applications.*'),
            default => $applicationsQuery->latest(),
        };

        $applications = $applicationsQuery->paginate(12)->withQueryString();

        return view('pages.dashboard.employer.applicants', [
            'dashboardType' => 'employer',
            'applications' => $applications,
            'jobs' => $jobs,
            'stats' => $stats,
        ]);
    }

    public function browseResumes(Request $request)
    {
        $query = Candidate::with(['user', 'location', 'skills', 'categories', 'resumes' => fn ($resumeQuery) => $resumeQuery->latest()])
            ->where('allow_search', true)
            ->whereHas('user')
            ->whereHas('resumes');

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($candidateQuery) use ($search) {
                $candidateQuery->where('title', 'like', "%{$search}%")
                    ->orWhere('bio', 'like', "%{$search}%")
                    ->orWhere('skills_list', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('skills', function ($skillQuery) use ($search) {
                        $skillQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('position')) {
            $position = trim((string) $request->input('position'));
            $query->where('title', 'like', "%{$position}%");
        }

        if ($request->filled('experience')) {
            match ($request->input('experience')) {
                '0-2' => $query->whereBetween('experience_years', [0, 2]),
                '3-5' => $query->whereBetween('experience_years', [3, 5]),
                '5-10' => $query->whereBetween('experience_years', [5, 10]),
                '10+' => $query->where('experience_years', '>=', 10),
                default => null,
            };
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->integer('location_id'));
        }

        if ($request->filled('availability')) {
            $query->where('is_available', $request->input('availability') === 'available');
        }

        match ($request->input('sort')) {
            'experience' => $query->orderByDesc('experience_years')->latest(),
            'name' => $query
                ->join('users', 'candidates.user_id', '=', 'users.id')
                ->orderBy('users.name')
                ->select('candidates.*'),
            default => $query->latest(),
        };

        $candidates = $query->paginate(10)->withQueryString();
        $locations = \App\Models\Location::where('type', 'country')
            ->where('is_active', true)
            ->whereHas('candidates', fn ($candidateQuery) => $candidateQuery->whereHas('resumes'))
            ->orderBy('name')
            ->get(['id', 'name']);

        $stats = [
            'total' => Candidate::where('allow_search', true)->whereHas('user')->whereHas('resumes')->count(),
            'available' => Candidate::where('allow_search', true)->where('is_available', true)->whereHas('user')->whereHas('resumes')->count(),
            'new_this_week' => Candidate::where('allow_search', true)->whereHas('user')->whereHas('resumes', fn ($resumeQuery) => $resumeQuery->where('created_at', '>=', now()->subWeek()))->count(),
        ];

        return view('pages.dashboard.employer.resumes', [
            'dashboardType' => 'employer',
            'candidates' => $candidates,
            'locations' => $locations,
            'stats' => $stats,
        ]);
    }

    public function resumeAlerts()
    {
        $company = auth()->user()->company;

        if (! $company) {
            return view('pages.dashboard.employer.resume-alerts', [
                'dashboardType' => 'employer',
                'activeJobs' => collect(),
                'alertRows' => collect(),
                'recentMatches' => collect(),
                'stats' => [
                    'active_alerts' => 0,
                    'total_matches' => 0,
                    'matches_today' => 0,
                    'high_confidence' => 0,
                ],
            ]);
        }

        $activeJobs = $company->jobListings()
            ->with(['location', 'category', 'jobType', 'applications'])
            ->where('status', 'active')
            ->latest()
            ->get();

        $candidates = Candidate::with(['user', 'location', 'skills', 'categories', 'resumes' => fn ($query) => $query->latest()])
            ->where('allow_search', true)
            ->whereHas('user')
            ->whereHas('resumes')
            ->latest()
            ->get();

        $alertRows = $activeJobs->map(function (JobListing $job) use ($candidates) {
            $appliedCandidateIds = $job->applications->pluck('candidate_id')->all();

            $matches = $candidates
                ->reject(fn (Candidate $candidate) => in_array($candidate->id, $appliedCandidateIds, true))
                ->map(function (Candidate $candidate) use ($job) {
                    $match = $this->resumeAlertMatch($job, $candidate);

                    return [
                        'candidate' => $candidate,
                        'score' => $match['score'],
                        'reasons' => $match['reasons'],
                        'resume' => $candidate->resumes->firstWhere('is_default', true) ?? $candidate->resumes->first(),
                    ];
                })
                ->filter(fn (array $match) => $match['score'] >= 35)
                ->sortByDesc('score')
                ->values();

            return [
                'job' => $job,
                'matches' => $matches,
                'matches_today' => $matches->filter(fn (array $match) => $match['resume']?->created_at?->isToday())->count(),
                'high_confidence' => $matches->where('score', '>=', 70)->count(),
            ];
        });

        $recentMatches = $alertRows
            ->flatMap(fn (array $row) => $row['matches']->take(4)->map(fn (array $match) => $match + ['job' => $row['job']]))
            ->sortByDesc(fn (array $match) => $match['resume']?->created_at)
            ->take(12)
            ->values();

        return view('pages.dashboard.employer.resume-alerts', [
            'dashboardType' => 'employer',
            'activeJobs' => $activeJobs,
            'alertRows' => $alertRows,
            'recentMatches' => $recentMatches,
            'stats' => [
                'active_alerts' => $activeJobs->count(),
                'total_matches' => $alertRows->sum(fn (array $row) => $row['matches']->count()),
                'matches_today' => $alertRows->sum('matches_today'),
                'high_confidence' => $alertRows->sum('high_confidence'),
            ],
        ]);
    }

    private function resumeAlertMatch(JobListing $job, Candidate $candidate): array
    {
        $score = 0;
        $reasons = [];

        if ($job->category_id && $candidate->categories->contains('id', $job->category_id)) {
            $score += 30;
            $reasons[] = 'Category match';
        }

        if ($job->location_id && $candidate->location_id && (int) $job->location_id === (int) $candidate->location_id) {
            $score += 20;
            $reasons[] = 'Same country/location';
        }

        if ($candidate->is_available) {
            $score += 15;
            $reasons[] = 'Available';
        }

        if ($candidate->resumes->isNotEmpty()) {
            $score += 15;
            $reasons[] = 'CV uploaded';
        }

        $jobTerms = $this->resumeAlertTerms($job->title.' '.$job->description.' '.$job->qualification);
        $candidateTerms = $this->resumeAlertTerms(
            $candidate->title.' '.$candidate->bio.' '.
            $candidate->skills->pluck('name')->join(' ').
            ' '.collect($candidate->skills_list ?? [])->join(' ')
        );

        $overlap = $jobTerms->intersect($candidateTerms)->take(6)->values();
        if ($overlap->isNotEmpty()) {
            $score += min(20, $overlap->count() * 5);
            $reasons[] = 'Keywords: '.$overlap->join(', ');
        }

        if ($candidate->experience_years) {
            $score += min(10, (int) floor($candidate->experience_years / 2));
            $reasons[] = $candidate->experience_years.' years experience';
        }

        return [
            'score' => min(100, $score),
            'reasons' => array_slice($reasons, 0, 4),
        ];
    }

    private function resumeAlertTerms(string $text)
    {
        return collect(preg_split('/[^a-z0-9]+/', Str::lower(strip_tags($text))))
            ->filter(fn ($term) => strlen($term) >= 4)
            ->reject(fn ($term) => in_array($term, ['with', 'from', 'this', 'that', 'your', 'will', 'have', 'role', 'candidate'], true))
            ->unique()
            ->values();
    }

    public function messages()
    {
        return $this->renderEmployerPage(
            'Messages',
            'Communicate with candidates and coordinate interviews.',
            [
                ['title' => 'Open Threads', 'value' => '27', 'note' => '8 candidates awaiting next steps.'],
                ['title' => 'Unread Messages', 'value' => '11', 'note' => 'Most in technical roles pipeline.'],
                ['title' => 'Avg Reply Time', 'value' => '3.2h', 'note' => 'Above platform average.'],
            ],
            [
                ['Elena Petrova', 'Confirmed interview slot', 'Unread', '20m ago'],
                ['John Anderson', 'Shared updated certificates', 'Read', '2h ago'],
                ['Marcus Thorne', 'Requested role clarification', 'Unread', 'Today'],
            ],
            ['Candidate', 'Latest Message', 'Status', 'When']
        );
    }

    public function changePassword()
    {
        return $this->renderEmployerPage(
            'Change Password',
            'Update account password and security settings.',
            [
                ['title' => 'Last Changed', 'value' => '75 days', 'note' => 'Rotation recommended every 90 days.'],
                ['title' => '2FA Status', 'value' => 'Enabled', 'note' => 'Security key + authenticator configured.'],
                ['title' => 'Admin Sessions', 'value' => '3', 'note' => 'All sessions are recognized devices.'],
            ],
            [
                ['Primary Credential', 'Compliant', 'Strong', 'Updated Jan 11'],
                ['Recovery Contact', 'Verified', 'Active', 'ops@company.com'],
                ['Security Audit', 'Passed', 'No issues', 'Mar 01'],
            ],
            ['Security Item', 'Status', 'Assessment', 'Details']
        );
    }

    private function renderEmployerPage(
        string $title,
        string $description,
        array $stats,
        array $rows,
        array $headers
    ) {
        $viewMap = [
            'employer.dashboard' => 'pages.dashboard.employer.dashboard',
            'employer.company.profile' => 'pages.dashboard.employer.company-profile',
            'employer.new-job' => 'pages.dashboard.employer.new-job',
            'employer.manage-jobs' => 'pages.dashboard.employer.manage-jobs',
            'employer.edit-job' => 'pages.dashboard.employer.edit-job',
            'employer.applicants' => 'pages.dashboard.employer.applicants',
            'employer.resumes' => 'pages.dashboard.employer.resumes',
            'employer.resume-alerts' => 'pages.dashboard.employer.resume-alerts',
            'employer.chat' => 'pages.dashboard.employer.messages',
            'employer.change-password' => 'pages.dashboard.employer.change-password',
        ];

        $routeName = request()->route()?->getName();
        $view = $viewMap[$routeName] ?? 'pages.dashboard.section';

        return view($view, [
            'dashboardType' => 'employer',
            'section' => 'Employer Dashboard',
            'pageTitle' => $title,
            'pageDescription' => $description,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Employer Dashboard', 'url' => route('employer.dashboard')],
                ['label' => $title],
            ],
            'stats' => $stats,
            'headers' => $headers,
            'rows' => $rows,
            'primaryAction' => ['label' => 'Post New Job', 'url' => route('employer.new-job')],
            'secondaryAction' => ['label' => 'Manage Jobs', 'url' => route('employer.manage-jobs')],
        ]);
    }
}
