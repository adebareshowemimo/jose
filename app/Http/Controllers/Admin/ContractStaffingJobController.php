<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\JobListing;
use App\Models\JobType;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContractStaffingJobController extends Controller
{
    public function index(Request $request)
    {
        $query = JobListing::contractStaffing()
            ->with(['category', 'jobType', 'location'])
            ->withCount('applications');

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('status', 'active');
            } elseif ($status === 'paused') {
                $query->where('status', 'paused');
            }
        }

        $jobs = $query->orderByDesc('is_featured')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.contract-staffing.index', compact('jobs', 'categories'));
    }

    public function create()
    {
        return view('admin.contract-staffing.create', $this->formData());
    }

    public function store(Request $request)
    {
        JobListing::create($this->validatedData($request));
        return redirect()->route('admin.contract-staffing.index')->with('success', 'Contract staffing role created.');
    }

    public function edit(JobListing $job)
    {
        abort_unless($job->is_contract_staffing, 404);
        return view('admin.contract-staffing.edit', array_merge($this->formData(), ['job' => $job]));
    }

    public function update(Request $request, JobListing $job)
    {
        abort_unless($job->is_contract_staffing, 404);
        $job->update($this->validatedData($request, $job));
        return redirect()->route('admin.contract-staffing.index')->with('success', 'Contract staffing role updated.');
    }

    public function destroy(JobListing $job)
    {
        abort_unless($job->is_contract_staffing, 404);
        if ($job->thumbnail) {
            Storage::disk('public')->delete($job->thumbnail);
        }
        foreach (($job->gallery ?? []) as $path) {
            Storage::disk('public')->delete($path);
        }
        $job->delete();
        return redirect()->route('admin.contract-staffing.index')->with('success', 'Contract staffing role deleted.');
    }

    private function formData(): array
    {
        return [
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
            'jobTypes' => JobType::query()->where('is_active', true)->orderBy('name')->get(),
            'locations' => Location::query()->orderBy('name')->get(),
        ];
    }

    private function validatedData(Request $request, ?JobListing $job = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'job_type_id' => ['nullable', 'exists:job_types,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'qualification' => ['nullable', 'string', 'max:500'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'salary_type' => ['nullable', 'in:hourly,monthly,yearly'],
            'experience_required' => ['nullable', 'string', 'max:100'],
            'gender_preference' => ['nullable', 'in:any,male,female'],
            'deadline' => ['nullable', 'date'],
            'vacancies' => ['nullable', 'integer', 'min:1'],
            'hours' => ['nullable', 'string', 'max:100'],
            'hours_type' => ['nullable', 'in:full-time,part-time'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'apply_method' => ['required', 'in:internal,external_link,email'],
            'apply_url' => ['nullable', 'url', 'max:255', 'required_if:apply_method,external_link'],
            'apply_email' => ['nullable', 'email', 'max:255', 'required_if:apply_method,email'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
            'gallery' => ['nullable', 'array', 'max:6'],
            'gallery.*' => ['image', 'mimes:jpeg,jpg,png,webp,gif', 'max:4096'],
            'remove_gallery' => ['nullable', 'array'],
            'remove_gallery.*' => ['string'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_urgent' => ['nullable', 'boolean'],
        ]);

        // Slug is derived from the title on create and kept stable on edit so the
        // public URL doesn't break. There is no manual slug field.
        if ($job) {
            $validated['slug'] = $job->slug;
        } else {
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $counter = 1;
            while (JobListing::where('slug', $slug)->exists()) {
                $slug = $baseSlug.'-'.$counter++;
            }
            $validated['slug'] = $slug;
        }

        $validated['is_contract_staffing'] = true;
        $validated['company_id'] = null;
        $validated['posted_by'] = $job?->posted_by ?? auth()->id();
        $validated['is_approved'] = true;
        $validated['status'] = $request->boolean('is_active', true) ? 'active' : 'paused';
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_urgent'] = $request->boolean('is_urgent');

        $validated['gender_preference'] = $validated['gender_preference'] ?? 'any';

        // Application method drives which contact field is kept; clear the others.
        $method = $validated['apply_method'] ?? 'internal';
        $validated['apply_method'] = $method;
        $validated['apply_url'] = $method === 'external_link' ? ($validated['apply_url'] ?? null) : null;
        $validated['apply_email'] = $method === 'email' ? ($validated['apply_email'] ?? null) : null;

        unset($validated['is_active']);

        // Cover image (single).
        if ($request->hasFile('image')) {
            if ($job?->thumbnail) {
                Storage::disk('public')->delete($job->thumbnail);
            }
            $validated['thumbnail'] = $request->file('image')->store('job-listings', 'public');
        } elseif ($request->boolean('remove_image') && $job?->thumbnail) {
            Storage::disk('public')->delete($job->thumbnail);
            $validated['thumbnail'] = null;
        }

        // Gallery (multiple) — drop removed images, append new uploads.
        $gallery = $job?->gallery ?? [];
        foreach ((array) $request->input('remove_gallery', []) as $path) {
            if (in_array($path, $gallery, true)) {
                Storage::disk('public')->delete($path);
                $gallery = array_filter($gallery, fn ($p) => $p !== $path);
            }
        }
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $gallery[] = $file->store('job-listings', 'public');
            }
        }
        $validated['gallery'] = array_values($gallery);

        unset($validated['image'], $validated['remove_image'], $validated['remove_gallery']);

        return $validated;
    }
}
