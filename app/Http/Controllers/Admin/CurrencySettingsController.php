<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Currency;
use App\Support\Settings;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CurrencySettingsController extends Controller
{
    public function index(Settings $settings)
    {
        return view('admin.currency.index', [
            'currency' => $settings->group('currency'),
            'allowed'  => Currency::ALLOWED,
        ]);
    }

    public function update(Request $request, Settings $settings)
    {
        $data = $request->validate([
            'default'         => ['required', 'string', Rule::in(Currency::ALLOWED)],
            'usd_to_ngn_rate' => 'required|numeric|min:0.0001',
        ]);

        $map = [
            'currency.default'          => strtoupper($data['default']),
            'currency.usd_to_ngn_rate'  => number_format((float) $data['usd_to_ngn_rate'], 4, '.', ''),
        ];

        foreach ($map as $key => $value) {
            $row = Setting::firstOrNew(['key' => $key]);
            $row->group = 'currency';
            $row->is_encrypted = false;
            $row->value = $value;
            $row->save();
        }

        $settings->flush();

        return back()->with('success', 'Currency settings updated.');
    }
}
