<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Settings;
use Illuminate\Http\Request;

class BoostSettingsController extends Controller
{
    /**
     * key => [default, cast]
     *
     * Booleans are stored as '1'/'0' so they round-trip through the settings
     * table, which holds strings.
     */
    private const FIELDS = [
        'boost.enabled' => [true, 'bool'],
        'boost.reminders_enabled' => [true, 'bool'],
        'boost.reminder_lead_days' => [3, 'int'],
        'boost.require_verified_email' => [false, 'bool'],
        'boost.require_cv' => [false, 'bool'],
        'boost.min_profile_completion' => [0, 'int'],
        'boost.block_when_active' => [false, 'bool'],
        'boost.max_stacked_days' => [0, 'int'],
        'boost.cooldown_days' => [0, 'int'],
        'boost.max_per_year' => [0, 'int'],
    ];

    public function index(Settings $settings)
    {
        $values = [];
        foreach (self::FIELDS as $key => [$default, $cast]) {
            $raw = $settings->get($key, $default);
            $values[$key] = $cast === 'bool' ? (bool) $raw : (int) $raw;
        }

        return view('admin.boosts.settings', ['values' => $values]);
    }

    public function update(Request $request, Settings $settings)
    {
        $data = $request->validate([
            'boost_enabled' => ['nullable', 'boolean'],
            'reminders_enabled' => ['nullable', 'boolean'],
            'reminder_lead_days' => ['required', 'integer', 'min:1', 'max:60'],
            'require_verified_email' => ['nullable', 'boolean'],
            'require_cv' => ['nullable', 'boolean'],
            'min_profile_completion' => ['required', 'integer', 'min:0', 'max:100'],
            'block_when_active' => ['nullable', 'boolean'],
            'max_stacked_days' => ['required', 'integer', 'min:0', 'max:3650'],
            'cooldown_days' => ['required', 'integer', 'min:0', 'max:365'],
            'max_per_year' => ['required', 'integer', 'min:0', 'max:365'],
        ], [], [
            'min_profile_completion' => 'minimum profile completion',
            'max_stacked_days' => 'maximum stacked days',
        ]);

        $map = [
            'boost.enabled' => $request->boolean('boost_enabled'),
            'boost.reminders_enabled' => $request->boolean('reminders_enabled'),
            'boost.reminder_lead_days' => (int) $data['reminder_lead_days'],
            'boost.require_verified_email' => $request->boolean('require_verified_email'),
            'boost.require_cv' => $request->boolean('require_cv'),
            'boost.min_profile_completion' => (int) $data['min_profile_completion'],
            'boost.block_when_active' => $request->boolean('block_when_active'),
            'boost.max_stacked_days' => (int) $data['max_stacked_days'],
            'boost.cooldown_days' => (int) $data['cooldown_days'],
            'boost.max_per_year' => (int) $data['max_per_year'],
        ];

        foreach ($map as $key => $value) {
            $settings->set($key, is_bool($value) ? ($value ? '1' : '0') : (string) $value, 'boost');
        }

        return redirect()
            ->route('admin.boosts.settings.index')
            ->with('success', 'Boost settings saved.');
    }
}
