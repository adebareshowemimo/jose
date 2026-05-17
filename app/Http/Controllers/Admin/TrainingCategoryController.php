<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrainingCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TrainingCategoryController extends Controller
{
    public function index()
    {
        $categories = TrainingCategory::withCount('programs')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.training-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.training-categories.create');
    }

    public function store(Request $request)
    {
        TrainingCategory::create($this->validatedData($request));
        return redirect()->route('admin.training-categories.index')->with('success', 'Category created.');
    }

    public function update(Request $request, TrainingCategory $category)
    {
        $category->update($this->validatedData($request, $category));
        return back()->with('success', 'Category updated.');
    }

    public function destroy(TrainingCategory $category)
    {
        if ($category->programs()->exists()) {
            return back()->with('error', 'Cannot delete: this category still has training programs. Reassign or delete them first.');
        }
        if ($category->hero_image_path) {
            Storage::disk('public')->delete($category->hero_image_path);
        }
        $category->delete();
        return redirect()->route('admin.training-categories.index')->with('success', 'Category deleted.');
    }

    private function validatedData(Request $request, ?TrainingCategory $category = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('training_categories', 'slug')->ignore($category?->id)],
            'icon' => ['nullable', 'string', 'max:100'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'intro_html' => ['nullable', 'string'],
            'bullet_points_text' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = filled($validated['slug'] ?? null)
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $bulletsRaw = (string) ($validated['bullet_points_text'] ?? '');
        $bullets = collect(preg_split("/\r?\n/", $bulletsRaw))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
        $validated['bullet_points'] = $bullets ?: null;

        if ($request->hasFile('image')) {
            if ($category?->hero_image_path) {
                Storage::disk('public')->delete($category->hero_image_path);
            }
            $validated['hero_image_path'] = $request->file('image')->store('training-categories', 'public');
        } elseif ($request->boolean('remove_image') && $category?->hero_image_path) {
            Storage::disk('public')->delete($category->hero_image_path);
            $validated['hero_image_path'] = null;
        }

        unset($validated['image'], $validated['remove_image'], $validated['bullet_points_text']);

        return $validated;
    }
}
