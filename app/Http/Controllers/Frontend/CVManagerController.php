<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CVManagerController extends Controller
{
    /**
     * Display the CV manager page
     */
    public function index()
    {
        $user = Auth::user();
        $candidate = $user->candidate;
        
        // Get resumes if candidate exists
        $resumes = $candidate ? $candidate->resumes()->orderByDesc('is_default')->orderByDesc('created_at')->get() : collect();

        return view('pages.dashboard.candidate.cv-manager', [
            'dashboardType' => 'candidate',
            'section' => 'Candidate Dashboard',
            'pageTitle' => 'CV Manager',
            'pageDescription' => 'Upload and manage your CV/Resume files.',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Candidate Dashboard', 'url' => route('user.dashboard')],
                ['label' => 'CV Manager'],
            ],
            'resumes' => $resumes,
        ]);
    }

    /**
     * Upload a new CV
     */
    public function upload(Request $request)
    {
        $request->validate([
            'cv_file' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB max
            'title' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $candidate = $user->candidate;

        // Create candidate profile if doesn't exist
        if (!$candidate) {
            $candidate = Candidate::create([
                'user_id' => $user->id,
                'title' => null,
                'slug' => Str::slug($user->name . '-' . $user->id),
                'is_available' => true,
                'allow_search' => true,
            ]);
        }

        $file = $request->file('cv_file');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        
        // Generate unique filename
        $filename = 'cv_' . $user->id . '_' . time() . '.' . $extension;
        
        // Store file
        $file->move(public_path('uploads/resumes'), $filename);
        $filePath = 'uploads/resumes/' . $filename;

        // Check if this is the first CV (make it default)
        $isFirst = $candidate->resumes()->count() === 0;

        // Create resume record
        Resume::create([
            'candidate_id' => $candidate->id,
            'title' => $request->title ?: pathinfo($originalName, PATHINFO_FILENAME),
            'file_path' => $filePath,
            'is_default' => $isFirst,
        ]);

        return back()->with('success', 'CV uploaded successfully.');
    }

    /**
     * Download a CV
     */
    public function download(Resume $resume)
    {
        $user = Auth::user();
        
        // Check ownership
        if (!$user->candidate || $resume->candidate_id !== $user->candidate->id) {
            abort(403);
        }

        $filePath = public_path($resume->file_path);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'File not found.');
        }

        return response()->download($filePath, $resume->title . '.' . pathinfo($resume->file_path, PATHINFO_EXTENSION));
    }

    /**
     * Set a CV as default
     */
    public function setDefault(Resume $resume)
    {
        $user = Auth::user();
        
        // Check ownership
        if (!$user->candidate || $resume->candidate_id !== $user->candidate->id) {
            abort(403);
        }

        // Remove default from all other resumes
        $user->candidate->resumes()->update(['is_default' => false]);
        
        // Set this one as default
        $resume->update(['is_default' => true]);

        return back()->with('success', 'Default CV updated.');
    }

    /**
     * Delete a CV
     */
    public function destroy(Resume $resume)
    {
        $user = Auth::user();
        
        // Check ownership
        if (!$user->candidate || $resume->candidate_id !== $user->candidate->id) {
            abort(403);
        }

        // Delete file
        $filePath = public_path($resume->file_path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $wasDefault = $resume->is_default;
        $resume->delete();

        // If deleted was default, make the most recent one default
        if ($wasDefault) {
            $nextResume = $user->candidate->resumes()->orderByDesc('created_at')->first();
            if ($nextResume) {
                $nextResume->update(['is_default' => true]);
            }
        }

        return back()->with('success', 'CV deleted successfully.');
    }
}
