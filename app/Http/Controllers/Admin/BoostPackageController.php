<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoostPackage;
use Illuminate\Http\Request;

class BoostPackageController extends Controller
{
    /**
     * Perks an admin may toggle. Only list flags the application actually
     * honours - advertising a perk nothing implements is worse than not
     * offering it at all.
     */
    private const PERKS = [
        'top_of_search' => 'Top of candidate search results',
        'most_popular' => 'Show the "Most popular" badge',
    ];

    public function index()
    {
        return view('admin.boosts.packages.index', [
            'packages' => BoostPackage::ordered()->withCount('boosts')->get(),
            'perkOptions' => self::PERKS,
        ]);
    }

    public function create()
    {
        return view('admin.boosts.packages.form', [
            'package' => new BoostPackage(['is_active' => true, 'sort_order' => 0]),
            'perkOptions' => self::PERKS,
        ]);
    }

    public function store(Request $request)
    {
        $package = BoostPackage::create($this->validated($request));

        return redirect()
            ->route('admin.boosts.packages.index')
            ->with('success', "Package \"{$package->label}\" created.");
    }

    public function edit(BoostPackage $package)
    {
        return view('admin.boosts.packages.form', [
            'package' => $package,
            'perkOptions' => self::PERKS,
        ]);
    }

    public function update(Request $request, BoostPackage $package)
    {
        $package->update($this->validated($request));

        return redirect()
            ->route('admin.boosts.packages.index')
            ->with('success', "Package \"{$package->label}\" updated.");
    }

    /**
     * Packages that have been bought are deactivated rather than deleted, so
     * historical boosts keep pointing at the tier they were sold under.
     */
    public function destroy(BoostPackage $package)
    {
        if ($package->boosts()->exists()) {
            $package->update(['is_active' => false]);

            return redirect()
                ->route('admin.boosts.packages.index')
                ->with('success', "\"{$package->label}\" has been sold before, so it was deactivated rather than deleted. Existing boosts keep their history.");
        }

        $label = $package->label;
        $package->delete();

        return redirect()
            ->route('admin.boosts.packages.index')
            ->with('success', "Package \"{$label}\" deleted.");
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:160'],
            'days' => ['required', 'integer', 'min:1', 'max:3650'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
            'perks' => ['nullable', 'array'],
            'perks.*' => ['string'],
        ], [
            'days.min' => 'A boost must run for at least one day.',
        ]);

        // Checkbox lists arrive as the keys that were ticked; store them as a
        // flag map so the model can answer hasPerk() directly.
        $perks = [];
        foreach (array_keys(self::PERKS) as $key) {
            if (in_array($key, $request->input('perks', []), true)) {
                $perks[$key] = true;
            }
        }

        $data['perks'] = $perks;
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
