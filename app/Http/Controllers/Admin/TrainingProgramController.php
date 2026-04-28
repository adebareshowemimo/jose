<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrainingProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TrainingProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = TrainingProgram::query()->withCount('enrolments');

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $programs = $query->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.training.index', compact('programs'));
    }

    public function create()
    {
        return view('admin.training.create');
    }

    public function store(Request $request)
    {
        TrainingProgram::create($this->validatedData($request));
        return redirect()->route('admin.training.index')->with('success', 'Training program created.');
    }

    public function update(Request $request, TrainingProgram $program)
    {
        $program->update($this->validatedData($request, $program));
        return back()->with('success', 'Training program updated.');
    }

    public function destroy(TrainingProgram $program)
    {
        if ($program->image_path) {
            Storage::disk('public')->delete($program->image_path);
        }
        $program->delete();
        return redirect()->route('admin.training.index')->with('success', 'Training program deleted.');
    }

    private function validatedData(Request $request, ?TrainingProgram $program = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('training_programs', 'slug')->ignore($program?->id)],
            'type' => ['required', 'in:training,apprenticeship'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'long_description' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'duration' => ['nullable', 'string', 'max:100'],
            'level' => ['nullable', 'string', 'max:100'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'enrol_deadline' => ['nullable', 'date'],
            'category' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['slug'] = filled($validated['slug'] ?? null)
            ? Str::slug($validated['slug'])
            : Str::slug($validated['title']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            if ($program?->image_path) {
                Storage::disk('public')->delete($program->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('training', 'public');
        } elseif ($request->boolean('remove_image') && $program?->image_path) {
            Storage::disk('public')->delete($program->image_path);
            $validated['image_path'] = null;
        }

        unset($validated['image'], $validated['remove_image']);

        return $validated;
    }
}
