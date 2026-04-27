<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::where('type', 'country')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('admin.locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $data = $this->validateLocation($request);
        Location::create($data + [
            'slug' => $this->uniqueSlug($data['name']),
            'type' => 'country',
            'parent_id' => null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Country created.');
    }

    public function update(Request $request, Location $location)
    {
        abort_unless($location->type === 'country', 404);

        $data = $this->validateLocation($request);
        $payload = $data + [
            'type' => 'country',
            'parent_id' => null,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($location->name !== $data['name']) {
            $payload['slug'] = $this->uniqueSlug($data['name'], $location->id);
        }

        $location->update($payload);

        return back()->with('success', 'Country updated.');
    }

    public function destroy(Location $location)
    {
        abort_unless($location->type === 'country', 404);
        $location->delete();

        return back()->with('success', 'Country deleted.');
    }

    private function validateLocation(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'zipcode' => ['nullable', 'string', 'max:50'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (Location::where('slug', $slug)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
