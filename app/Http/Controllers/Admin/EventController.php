<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::query();

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $events = $query->orderBy('sort_order')
            ->orderByRaw('starts_at is null')
            ->orderBy('starts_at')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        Event::create($this->validatedData($request));

        return redirect()->route('admin.events.index')->with('success', 'Event created.');
    }

    public function update(Request $request, Event $event)
    {
        $event->update($this->validatedData($request, $event));

        return back()->with('success', 'Event updated.');
    }

    public function destroy(Event $event)
    {
        if ($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event deleted.');
    }

    private function validatedData(Request $request, ?Event $event = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:100'],
            'category' => ['required', 'in:hosted,industry'],
            'display_date' => ['required', 'string', 'max:100'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
            'register_url' => ['nullable', 'url', 'max:512'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'questions' => ['nullable', 'array'],
            'questions.*.label' => ['required_with:questions', 'string', 'max:255'],
            'questions.*.type' => ['required_with:questions', 'in:text,textarea,select'],
            'questions.*.required' => ['nullable'],
            'questions.*.options' => ['nullable', 'string'],
            'status' => ['required', 'in:upcoming,active,completed,draft,cancelled'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['price'] = ($validated['price'] ?? null) === '' ? null : ($validated['price'] ?? null);

        // Normalize questions: assign stable id from slug(label), parse options for select.
        $validated['questions'] = collect($validated['questions'] ?? [])
            ->map(function ($q) {
                $label = trim((string) ($q['label'] ?? ''));
                if ($label === '') return null;
                $type = $q['type'] ?? 'text';
                $required = ! empty($q['required']) && $q['required'] !== '0' && $q['required'] !== false;
                $options = null;
                if ($type === 'select') {
                    $options = collect(preg_split('/\R+/', (string) ($q['options'] ?? '')))
                        ->map(fn ($o) => trim($o))
                        ->filter()
                        ->values()
                        ->all();
                }
                return [
                    'id' => \Illuminate\Support\Str::slug($label),
                    'label' => $label,
                    'type' => $type,
                    'required' => $required,
                    'options' => $options,
                ];
            })
            ->filter()
            ->values()
            ->all();
        if (empty($validated['questions'])) {
            $validated['questions'] = null;
        }

        // Image handling: replace, remove, or leave as-is.
        if ($request->hasFile('image')) {
            if ($event?->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('events', 'public');
        } elseif ($request->boolean('remove_image') && $event?->image_path) {
            Storage::disk('public')->delete($event->image_path);
            $validated['image_path'] = null;
        }

        unset($validated['image'], $validated['remove_image']);

        return $validated;
    }
}
