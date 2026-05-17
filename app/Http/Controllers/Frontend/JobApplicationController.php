<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\Resume;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    public function store(Request $request, JobListing $jobListing)
    {
        $user = $request->user();
        $candidate = $user?->candidate;

        if (! $candidate) {
            return redirect()->back()
                ->with('error', 'You need a candidate profile before applying. Please complete your candidate signup first.');
        }

        $validated = $request->validate([
            'resume_id' => ['nullable', 'integer'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:4096'],
            'cover_letter' => ['nullable', 'string', 'max:5000'],
        ]);

        $resumeId = null;

        if ($request->hasFile('resume')) {
            $file = $request->file('resume');
            $path = $file->store("resumes/{$candidate->id}", 'public');
            $resume = Resume::create([
                'candidate_id' => $candidate->id,
                'title' => $file->getClientOriginalName(),
                'file_path' => $path,
                'is_default' => $candidate->resumes()->count() === 0,
            ]);
            $resumeId = $resume->id;
        } elseif (! empty($validated['resume_id'])) {
            $resume = $candidate->resumes()->find($validated['resume_id']);
            if (! $resume) {
                return back()->with('error', 'Selected resume not found.');
            }
            $resumeId = $resume->id;
        }

        try {
            JobApplication::create([
                'job_listing_id' => $jobListing->id,
                'candidate_id' => $candidate->id,
                'resume_id' => $resumeId,
                'cover_letter' => $validated['cover_letter'] ?? null,
                'status' => 'applied',
            ]);
        } catch (QueryException $e) {
            return back()->with('error', 'You have already applied to this role.');
        }

        return back()->with('success', 'Application submitted. We will be in touch soon.');
    }
}
