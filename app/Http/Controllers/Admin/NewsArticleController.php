<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NewsArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsArticle::query();

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $articles = $query->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = NewsArticle::query()
            ->select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin.news.index', compact('articles', 'categories'));
    }

    public function store(Request $request)
    {
        NewsArticle::create($this->validatedData($request));

        return back()->with('success', 'News article created.');
    }

    public function update(Request $request, NewsArticle $article)
    {
        $article->update($this->validatedData($request, $article));

        return back()->with('success', 'News article updated.');
    }

    public function destroy(NewsArticle $article)
    {
        if ($article->image_path) {
            Storage::disk('public')->delete($article->image_path);
        }
        $article->delete();

        return redirect()->route('admin.news.index')->with('success', 'News article deleted.');
    }

    private function validatedData(Request $request, ?NewsArticle $article = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('news_articles', 'slug')->ignore($article?->id),
            ],
            'excerpt' => ['required', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
            'author' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'published_at' => ['nullable', 'date'],
            'status' => ['required', 'in:published,draft,archived'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = filled($validated['slug'] ?? null)
            ? Str::slug($validated['slug'])
            : Str::slug($validated['title']);
        $validated['content'] = collect(preg_split('/\R{2,}/', trim($validated['content'])))
            ->map(fn ($paragraph) => trim($paragraph))
            ->filter()
            ->values()
            ->all();
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Image handling: replace, remove, or leave as-is.
        if ($request->hasFile('image')) {
            if ($article?->image_path) {
                Storage::disk('public')->delete($article->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('news', 'public');
        } elseif ($request->boolean('remove_image') && $article?->image_path) {
            Storage::disk('public')->delete($article->image_path);
            $validated['image_path'] = null;
        }

        // Don't overwrite image_path with these helper keys.
        unset($validated['image'], $validated['remove_image']);

        return $validated;
    }
}
