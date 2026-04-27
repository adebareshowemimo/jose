<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Candidate;
use App\Models\JobAlert;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CandidateDashboardController extends BasePageController
{
    protected function getCandidate()
    {
        return Candidate::where('user_id', Auth::id())->first();
    }

    public function dashboard()
    {
        $candidate = $this->getCandidate();
        $user = Auth::user();

        // Real stats
        $appliedJobsCount = $candidate ? JobApplication::where('candidate_id', $candidate->id)->count() : 0;
        $jobAlertsCount = JobAlert::where('user_id', $user->id)->where('is_active', true)->count();
        $savedJobsCount = Wishlist::where('user_id', $user->id)
            ->where('wishlistable_type', JobListing::class)
            ->count();
        $messagesCount = 0; // Placeholder until chat system is implemented

        // Recent applications
        $recentApplications = $candidate 
            ? JobApplication::with(['jobListing.company'])
                ->where('candidate_id', $candidate->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
            : collect();

        // Calculate profile completion
        $profileCompletion = $this->calculateProfileCompletion($candidate, $user);

        return view('pages.dashboard.candidate.dashboard', [
            'appliedJobs' => $appliedJobsCount,
            'jobAlerts' => $jobAlertsCount,
            'messages' => $messagesCount,
            'savedJobs' => $savedJobsCount,
            'recentApplications' => $recentApplications,
            'profileCompletion' => $profileCompletion,
        ]);
    }

    public function profile()
    {
        // Profile is handled by ProfileController
        return redirect()->route('user.candidate.profile');
    }

    public function appliedJobs()
    {
        $candidate = $this->getCandidate();
        
        $applications = $candidate 
            ? JobApplication::with(['jobListing.company', 'jobListing.location', 'jobListing.jobType'])
                ->where('candidate_id', $candidate->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10)
            : collect();

        // Stats
        $totalApplications = $candidate ? JobApplication::where('candidate_id', $candidate->id)->count() : 0;
        $pendingCount = $candidate ? JobApplication::where('candidate_id', $candidate->id)->where('status', 'pending')->count() : 0;
        $interviewCount = $candidate ? JobApplication::where('candidate_id', $candidate->id)->where('status', 'interview')->count() : 0;
        $acceptedCount = $candidate ? JobApplication::where('candidate_id', $candidate->id)->where('status', 'accepted')->count() : 0;

        return view('pages.dashboard.candidate.applied-jobs', [
            'applications' => $applications,
            'totalApplications' => $totalApplications,
            'pendingCount' => $pendingCount,
            'interviewCount' => $interviewCount,
            'acceptedCount' => $acceptedCount,
        ]);
    }

    public function jobAlerts()
    {
        $user = Auth::user();
        
        $alerts = JobAlert::with(['category', 'jobType', 'location'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $activeCount = JobAlert::where('user_id', $user->id)->where('is_active', true)->count();
        $totalCount = JobAlert::where('user_id', $user->id)->count();

        return view('pages.dashboard.candidate.job-alerts', [
            'alerts' => $alerts,
            'activeCount' => $activeCount,
            'totalCount' => $totalCount,
        ]);
    }

    public function bookmarks()
    {
        $user = Auth::user();
        
        $savedJobs = Wishlist::with(['wishlistable.company', 'wishlistable.location', 'wishlistable.jobType'])
            ->where('user_id', $user->id)
            ->where('wishlistable_type', JobListing::class)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalSaved = Wishlist::where('user_id', $user->id)
            ->where('wishlistable_type', JobListing::class)
            ->count();

        return view('pages.dashboard.candidate.bookmarks', [
            'savedJobs' => $savedJobs,
            'totalSaved' => $totalSaved,
        ]);
    }

    public function resumeBuilder()
    {
        $candidate = $this->getCandidate();
        $user = Auth::user();

        return view('pages.dashboard.candidate.resume-builder', [
            'candidate' => $candidate,
            'user' => $user,
        ]);
    }

    public function changePassword()
    {
        return view('pages.dashboard.candidate.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }

    public function messages()
    {
        // Placeholder until chat system is fully implemented
        return view('pages.dashboard.candidate.messages', [
            'conversations' => collect(),
        ]);
    }

    public function plans()
    {
        return view('pages.dashboard.candidate.plans');
    }

    public function wallet()
    {
        return view('pages.dashboard.candidate.wallet');
    }

    public function payout()
    {
        return view('pages.dashboard.candidate.payout');
    }

    // Job Alert Management
    public function storeAlert(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'keywords' => 'nullable|string|max:500',
            'salary_min' => 'nullable|numeric|min:0',
            'frequency' => 'required|in:instant,daily,weekly',
        ]);

        JobAlert::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'keywords' => $request->keywords,
            'salary_min' => $request->salary_min,
            'frequency' => $request->frequency,
            'email_notifications' => true,
            'is_active' => true,
        ]);

        return back()->with('success', 'Job alert created successfully.');
    }

    public function toggleAlert(JobAlert $alert)
    {
        if ($alert->user_id !== Auth::id()) {
            abort(403);
        }

        $alert->is_active = !$alert->is_active;
        $alert->save();

        return back()->with('success', 'Alert ' . ($alert->is_active ? 'activated' : 'paused') . ' successfully.');
    }

    public function deleteAlert(JobAlert $alert)
    {
        if ($alert->user_id !== Auth::id()) {
            abort(403);
        }

        $alert->delete();

        return back()->with('success', 'Job alert deleted successfully.');
    }

    // Bookmark Management
    public function toggleBookmark(JobListing $job)
    {
        $user = Auth::user();
        
        $existing = Wishlist::where('user_id', $user->id)
            ->where('wishlistable_type', JobListing::class)
            ->where('wishlistable_id', $job->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', 'Job removed from saved jobs.');
        }

        Wishlist::create([
            'user_id' => $user->id,
            'wishlistable_type' => JobListing::class,
            'wishlistable_id' => $job->id,
        ]);

        return back()->with('success', 'Job saved successfully.');
    }

    public function removeBookmark(Wishlist $wishlist)
    {
        if ($wishlist->user_id !== Auth::id()) {
            abort(403);
        }

        $wishlist->delete();

        return back()->with('success', 'Job removed from saved jobs.');
    }

    private function calculateProfileCompletion($candidate, $user): int
    {
        $completion = 0;
        $fields = 0;

        // User fields
        $fields += 3;
        if ($user->name) $completion++;
        if ($user->email) $completion++;
        if ($user->avatar_id) $completion++;

        if ($candidate) {
            // Candidate fields
            $fields += 8;
            if ($candidate->title) $completion++;
            if ($candidate->bio) $completion++;
            if ($candidate->education && count($candidate->education)) $completion++;
            if ($candidate->experience && count($candidate->experience)) $completion++;
            if ($candidate->skills()->count()) $completion++;
            if ($candidate->location_id) $completion++;
            if ($candidate->resumes()->count()) $completion++;
            if ($candidate->social_links && count($candidate->social_links)) $completion++;
        }

        return $fields > 0 ? round(($completion / $fields) * 100) : 0;
    }
}
