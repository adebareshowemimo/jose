<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobTypeController extends Controller
{
    public function index()
    {
        $jobTypes = JobType::orderByDesc('is_active')->orderBy('name')->get();

        return view('admin.job-types.index', compact('jobTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255']]);
        JobType::create([
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Job type created.');
    }

    public function update(Request $request, JobType $jobType)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255']]);
        $payload = [
            'name' => $data['name'],
            'is_active' => $request->boolean('is_active'),
        ];

        if ($jobType->name !== $data['name']) {
            $payload['slug'] = $this->uniqueSlug($data['name'], $jobType->id);
        }

        $jobType->update($payload);

        return back()->with('success', 'Job type updated.');
    }

    public function destroy(JobType $jobType)
    {
        $jobType->delete();

        return back()->with('success', 'Job type deleted.');
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (JobType::where('slug', $slug)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
