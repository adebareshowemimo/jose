<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Show the candidate profile page
     */
    public function show()
    {
        $user = Auth::user();
        $candidate = $user->candidate ?? $this->createCandidateProfile($user);
        $locations = Location::where('type', 'country')->where('is_active', true)->orderBy('name')->get();

        return view('pages.dashboard.candidate.profile', [
            'dashboardType' => 'candidate',
            'section' => 'Candidate Dashboard',
            'pageTitle' => 'My Profile',
            'pageDescription' => 'Manage your personal details, experience, education, and certifications.',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Candidate Dashboard', 'url' => route('user.dashboard')],
                ['label' => 'My Profile'],
            ],
            'user' => $user,
            'candidate' => $candidate,
            'locations' => $locations,
        ]);
    }

    /**
     * Update basic info (personal & professional details)
     */
    public function updateBasicInfo(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'location_id' => ['nullable', \Illuminate\Validation\Rule::exists('locations', 'id')->where('type', 'country')->where('is_active', true)],
            'title' => 'nullable|string|max:255',
            'experience_years' => 'nullable|string|max:50',
            'preferred_vessel_type' => 'nullable|string|max:255',
            'expected_salary' => 'nullable|numeric|min:0',
            'bio' => 'nullable|string|max:5000',
        ]);

        $user = Auth::user();
        
        // Update user basic info
        $user->update([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // Get or create candidate profile
        $candidate = $user->candidate ?? $this->createCandidateProfile($user);

        // Update candidate profile
        $candidate->update([
            'title' => $request->title,
            'slug' => Str::slug($user->name . '-' . $user->id),
            'bio' => $request->bio,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'experience_years' => $request->experience_years,
            'expected_salary' => $request->expected_salary,
            'location_id' => $request->location_id,
        ]);

        // Store preferred vessel type in a JSON field or as part of experience
        // For now, we'll store it as metadata in experience array if needed

        return back()->with('success', 'Basic information updated successfully.');
    }

    /**
     * Update avatar/profile photo
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $filename);
            
            $user->update(['avatar' => 'uploads/avatars/' . $filename]);
        }

        return back()->with('success', 'Profile photo updated successfully.');
    }

    /**
     * Add experience entry
     */
    public function addExperience(Request $request)
    {
        $request->validate([
            'position' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'vessel_type' => 'nullable|string|max:255',
            'vessel_name' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'nullable|boolean',
            'description' => 'nullable|string|max:2000',
        ]);

        $user = Auth::user();
        $candidate = $user->candidate ?? $this->createCandidateProfile($user);

        $experience = $candidate->experience ?? [];
        $experience[] = [
            'id' => Str::uuid()->toString(),
            'position' => $request->position,
            'company' => $request->company,
            'vessel_type' => $request->vessel_type,
            'vessel_name' => $request->vessel_name,
            'start_date' => $request->start_date,
            'end_date' => $request->is_current ? null : $request->end_date,
            'is_current' => $request->boolean('is_current'),
            'description' => $request->description,
        ];

        $candidate->update(['experience' => $experience]);

        return back()->with('success', 'Experience added successfully.');
    }

    /**
     * Update experience entry
     */
    public function updateExperience(Request $request, string $expId)
    {
        $request->validate([
            'position' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'vessel_type' => 'nullable|string|max:255',
            'vessel_name' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'nullable|boolean',
            'description' => 'nullable|string|max:2000',
        ]);

        $user = Auth::user();
        $candidate = $user->candidate;

        if (!$candidate) {
            return back()->with('error', 'Profile not found.');
        }

        $experience = $candidate->experience ?? [];
        foreach ($experience as &$exp) {
            if (($exp['id'] ?? null) === $expId) {
                $exp['position'] = $request->position;
                $exp['company'] = $request->company;
                $exp['vessel_type'] = $request->vessel_type;
                $exp['vessel_name'] = $request->vessel_name;
                $exp['start_date'] = $request->start_date;
                $exp['end_date'] = $request->is_current ? null : $request->end_date;
                $exp['is_current'] = $request->boolean('is_current');
                $exp['description'] = $request->description;
                break;
            }
        }
        unset($exp);

        $candidate->update(['experience' => $experience]);

        return back()->with('success', 'Experience updated successfully.');
    }

    /**
     * Delete experience entry
     */
    public function deleteExperience(string $expId)
    {
        $user = Auth::user();
        $candidate = $user->candidate;

        if (!$candidate) {
            return back()->with('error', 'Profile not found.');
        }

        $experience = collect($candidate->experience ?? [])->reject(fn($exp) => ($exp['id'] ?? null) === $expId)->values()->all();
        $candidate->update(['experience' => $experience]);

        return back()->with('success', 'Experience deleted successfully.');
    }

    /**
     * Add education entry
     */
    public function addEducation(Request $request)
    {
        $request->validate([
            'degree' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'start_year' => 'required|integer|min:1950|max:' . (date('Y') + 5),
            'end_year' => 'nullable|integer|min:1950|max:' . (date('Y') + 10),
            'description' => 'nullable|string|max:2000',
        ]);

        $user = Auth::user();
        $candidate = $user->candidate ?? $this->createCandidateProfile($user);

        $education = $candidate->education ?? [];
        $education[] = [
            'id' => Str::uuid()->toString(),
            'degree' => $request->degree,
            'institution' => $request->institution,
            'field_of_study' => $request->field_of_study,
            'start_year' => $request->start_year,
            'end_year' => $request->end_year,
            'description' => $request->description,
        ];

        $candidate->update(['education' => $education]);

        return back()->with('success', 'Education added successfully.');
    }

    /**
     * Update education entry
     */
    public function updateEducation(Request $request, string $eduId)
    {
        $request->validate([
            'degree' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'start_year' => 'required|integer|min:1950|max:' . (date('Y') + 5),
            'end_year' => 'nullable|integer|min:1950|max:' . (date('Y') + 10),
            'description' => 'nullable|string|max:2000',
        ]);

        $user = Auth::user();
        $candidate = $user->candidate;

        if (!$candidate) {
            return back()->with('error', 'Profile not found.');
        }

        $education = $candidate->education ?? [];
        foreach ($education as &$edu) {
            if (($edu['id'] ?? null) === $eduId) {
                $edu['degree'] = $request->degree;
                $edu['institution'] = $request->institution;
                $edu['field_of_study'] = $request->field_of_study;
                $edu['start_year'] = $request->start_year;
                $edu['end_year'] = $request->end_year;
                $edu['description'] = $request->description;
                break;
            }
        }
        unset($edu);

        $candidate->update(['education' => $education]);

        return back()->with('success', 'Education updated successfully.');
    }

    /**
     * Delete education entry
     */
    public function deleteEducation(string $eduId)
    {
        $user = Auth::user();
        $candidate = $user->candidate;

        if (!$candidate) {
            return back()->with('error', 'Profile not found.');
        }

        $education = collect($candidate->education ?? [])->reject(fn($edu) => ($edu['id'] ?? null) === $eduId)->values()->all();
        $candidate->update(['education' => $education]);

        return back()->with('success', 'Education deleted successfully.');
    }

    /**
     * Add certification/award entry
     */
    public function addCertification(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'credential_id' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $candidate = $user->candidate ?? $this->createCandidateProfile($user);

        $awards = $candidate->awards ?? [];
        $awards[] = [
            'id' => Str::uuid()->toString(),
            'name' => $request->name,
            'issuer' => $request->issuer,
            'issue_date' => $request->issue_date,
            'expiry_date' => $request->expiry_date,
            'credential_id' => $request->credential_id,
        ];

        $candidate->update(['awards' => $awards]);

        return back()->with('success', 'Certification added successfully.');
    }

    /**
     * Update certification entry
     */
    public function updateCertification(Request $request, string $certId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'credential_id' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $candidate = $user->candidate;

        if (!$candidate) {
            return back()->with('error', 'Profile not found.');
        }

        $awards = $candidate->awards ?? [];
        foreach ($awards as &$cert) {
            if (($cert['id'] ?? null) === $certId) {
                $cert['name'] = $request->name;
                $cert['issuer'] = $request->issuer;
                $cert['issue_date'] = $request->issue_date;
                $cert['expiry_date'] = $request->expiry_date;
                $cert['credential_id'] = $request->credential_id;
                break;
            }
        }
        unset($cert);

        $candidate->update(['awards' => $awards]);

        return back()->with('success', 'Certification updated successfully.');
    }

    /**
     * Delete certification entry
     */
    public function deleteCertification(string $certId)
    {
        $user = Auth::user();
        $candidate = $user->candidate;

        if (!$candidate) {
            return back()->with('error', 'Profile not found.');
        }

        $awards = collect($candidate->awards ?? [])->reject(fn($cert) => ($cert['id'] ?? null) === $certId)->values()->all();
        $candidate->update(['awards' => $awards]);

        return back()->with('success', 'Certification deleted successfully.');
    }

    /**
     * Update social links
     */
    public function updateSocialLinks(Request $request)
    {
        $request->validate([
            'linkedin' => 'nullable|url|max:255',
            'website' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'github' => 'nullable|url|max:255',
        ]);

        $user = Auth::user();
        $candidate = $user->candidate ?? $this->createCandidateProfile($user);

        $candidate->update([
            'website' => $request->website,
            'social_links' => [
                'linkedin' => $request->linkedin,
                'twitter' => $request->twitter,
                'github' => $request->github,
            ],
        ]);

        return back()->with('success', 'Social links updated successfully.');
    }

    /**
     * Update professional summary/bio
     */
    public function updateSummary(Request $request)
    {
        $request->validate([
            'bio' => 'required|string|max:5000',
        ]);

        $user = Auth::user();
        $candidate = $user->candidate ?? $this->createCandidateProfile($user);

        $candidate->update([
            'bio' => $request->bio,
        ]);

        return back()->with('success', 'Professional summary updated successfully.');
    }

    /**
     * Update skills (stored as JSON array since no skills table relation)
     */
    public function updateSkills(Request $request)
    {
        $request->validate([
            'skills' => 'nullable|string|max:2000',
        ]);

        $user = Auth::user();
        $candidate = $user->candidate ?? $this->createCandidateProfile($user);

        // Parse skills from comma-separated string
        $skillsArray = [];
        if ($request->skills) {
            $skillsArray = array_map('trim', explode(',', $request->skills));
            $skillsArray = array_filter($skillsArray); // Remove empty values
            $skillsArray = array_values(array_unique($skillsArray)); // Remove duplicates
        }

        $candidate->update([
            'skills_list' => $skillsArray,
        ]);

        return back()->with('success', 'Skills updated successfully.');
    }

    /**
     * Create a new candidate profile for the user
     */
    private function createCandidateProfile($user): Candidate
    {
        return Candidate::create([
            'user_id' => $user->id,
            'title' => null,
            'slug' => Str::slug($user->name . '-' . $user->id),
            'bio' => null,
            'education' => [],
            'experience' => [],
            'awards' => [],
            'languages' => [],
            'social_links' => [],
            'is_available' => true,
            'allow_search' => true,
        ]);
    }
}
