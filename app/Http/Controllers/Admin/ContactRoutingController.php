<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\ContactRoutes;
use App\Support\Settings;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactRoutingController extends Controller
{
    public function index(ContactRoutes $routes)
    {
        return view('admin.contact-routing.index', [
            'subjects' => $routes->subjects(),
            'defaultEmail' => $routes->defaultEmail(),
        ]);
    }

    public function update(Request $request, Settings $settings)
    {
        if ($request->boolean('reset')) {
            return $this->reset($settings);
        }

        $data = $request->validate([
            'default_email' => ['required', 'email', 'max:255'],
            'subjects' => ['required', 'array', 'min:1'],
            'subjects.*.label' => ['required', 'string', 'max:255'],
            'subjects.*.email' => ['required', 'email', 'max:255'],
        ], [], [
            'subjects.*.label' => 'subject label',
            'subjects.*.email' => 'subject email',
        ]);

        $labels = array_map(fn ($row) => mb_strtolower(trim($row['label'])), $data['subjects']);
        if (count($labels) !== count(array_unique($labels))) {
            return back()
                ->withInput()
                ->withErrors(['subjects' => 'Duplicate subject labels are not allowed.']);
        }

        $clean = array_map(fn ($row) => [
            'label' => trim($row['label']),
            'email' => trim($row['email']),
        ], $data['subjects']);

        $this->saveSetting('contact.subject_routes', $clean);
        $this->saveSetting('contact.default_email', trim($data['default_email']));

        $settings->flush();

        return redirect()->route('admin.contact-routing.index')->with('success', 'Contact routing updated.');
    }

    private function reset(Settings $settings)
    {
        $this->saveSetting('contact.subject_routes', ContactRoutes::DEFAULT_ROUTES);
        $this->saveSetting('contact.default_email', ContactRoutes::DEFAULT_FALLBACK_EMAIL);
        $settings->flush();

        return redirect()->route('admin.contact-routing.index')->with('success', 'Contact routing reset to defaults.');
    }

    private function saveSetting(string $key, mixed $value): void
    {
        $row = Setting::firstOrNew(['key' => $key]);
        $row->group = 'contact_routing';
        $row->is_encrypted = false;
        $row->value = $value;
        $row->save();
    }
}
