<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRecruitmentRequest;
use App\Models\Category;
use App\Models\JobType;
use App\Models\Location;
use App\Models\RecruitmentRequest;
use App\Models\RecruitmentRequestCandidate;
use App\Models\Role;
use App\Models\User;
use App\Support\EmailDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecruitmentRequestController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->user()->company;
        if (! $company) {
            return redirect()->route('employer.company.profile')
                ->with('error', 'Please complete your company profile before requesting candidates.');
        }

        $requests = RecruitmentRequest::where('company_id', $company->id)
            ->with(['category', 'jobType', 'order'])
            ->latest()
            ->paginate(15);

        return view('pages.dashboard.employer.recruitment-requests.index', compact('requests'));
    }

    public function create(Request $request)
    {
        $company = $request->user()->company;
        if (! $company) {
            return redirect()->route('employer.company.profile')
                ->with('error', 'Please complete your company profile before requesting candidates.');
        }

        $countries = Location::where('type', 'country')->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('pages.dashboard.employer.recruitment-requests.create', [
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'jobTypes' => JobType::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'countries' => $countries,
            'defaultCountryId' => $countries->firstWhere('name', 'Nigeria')?->id,
        ]);
    }

    public function store(StoreRecruitmentRequest $request, EmailDispatcher $dispatcher)
    {
        $user = $request->user();
        $company = $user->company;

        $skills = collect(explode(',', $request->input('skills_input', '')))
            ->map(fn ($s) => trim($s))
            ->filter()
            ->values()
            ->all();

        $recruitment = RecruitmentRequest::create([
            'company_id' => $company->id,
            'requested_by_user_id' => $user->id,
            'service_type' => $request->input('service_type'),
            'cv_count' => $request->input('cv_count'),
            'job_title' => $request->input('job_title'),
            'category_id' => $request->input('category_id'),
            'job_type_id' => $request->input('job_type_id'),
            'location_id' => $request->input('location_id'),
            'experience_level' => $request->input('experience_level'),
            'salary_min' => $request->input('salary_min'),
            'salary_max' => $request->input('salary_max'),
            'salary_currency' => $request->input('salary_currency') ?: 'USD',
            'description' => $request->input('description'),
            'skills_list' => $skills ?: null,
            'needed_by' => $request->input('needed_by'),
            'status' => 'pending',
        ]);

        if ($request->hasFile('jd_file')) {
            $path = $request->file('jd_file')->store("recruitment-requests/{$recruitment->id}/jd", 'public');
            $recruitment->update(['jd_file_path' => $path]);
        }

        // Notify the employer.
        $dispatcher->send('recruitment.request_received', $user, [
            'company_name' => $company->name,
            'service_type' => RecruitmentRequest::SERVICE_TYPES[$recruitment->service_type] ?? $recruitment->service_type,
            'cv_count' => $recruitment->cv_count,
            'job_title' => $recruitment->job_title,
            'request_url' => route('employer.recruitment-requests.show', $recruitment),
        ]);

        // Notify the first available admin.
        $adminRole = Role::where('name', 'admin')->first();
        $adminUser = $adminRole ? User::where('role_id', $adminRole->id)->where('status', 'active')->first() : null;
        if ($adminUser) {
            $dispatcher->send('recruitment.admin_new_request', $adminUser, [
                'company_name' => $company->name,
                'service_type' => RecruitmentRequest::SERVICE_TYPES[$recruitment->service_type] ?? $recruitment->service_type,
                'cv_count' => $recruitment->cv_count,
                'job_title' => $recruitment->job_title,
                'requester_name' => $user->name,
                'requester_email' => $user->email,
                'admin_url' => route('admin.recruitment-requests.show', $recruitment),
            ]);
        }

        return redirect()
            ->route('employer.recruitment-requests.show', $recruitment)
            ->with('success', 'Your recruitment request has been submitted. Our team will be in touch shortly with a quote.');
    }

    public function show(Request $request, RecruitmentRequest $recruitment)
    {
        $this->authorizeOwnership($request, $recruitment);

        $recruitment->load([
            'category', 'jobType', 'location', 'order',
            'candidates.candidate.user',
        ]);

        return view('pages.dashboard.employer.recruitment-requests.show', [
            'recruitment' => $recruitment,
        ]);
    }

    public function cancel(Request $request, RecruitmentRequest $recruitment)
    {
        $this->authorizeOwnership($request, $recruitment);

        if (! $recruitment->isCancellable()) {
            return back()->with('error', 'This request can no longer be cancelled.');
        }

        $recruitment->update(['status' => 'cancelled']);

        return back()->with('success', 'Request cancelled.');
    }

    public function decide(Request $request, RecruitmentRequest $recruitment, RecruitmentRequestCandidate $candidate)
    {
        $this->authorizeOwnership($request, $recruitment);

        if ($candidate->recruitment_request_id !== $recruitment->id) {
            abort(404);
        }

        $data = $request->validate([
            'decision' => 'required|in:pending,shortlisted,contacted,rejected,hired',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $candidate->update([
            'employer_decision' => $data['decision'],
            'employer_feedback' => $data['feedback'] ?? $candidate->employer_feedback,
        ]);

        return back()->with('success', "Marked as {$data['decision']}.");
    }

    protected function authorizeOwnership(Request $request, RecruitmentRequest $recruitment): void
    {
        $company = $request->user()->company;
        if (! $company || $recruitment->company_id !== $company->id) {
            abort(403);
        }
    }
}
