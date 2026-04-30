<?php

use App\Support\Currency;
use App\Support\Settings;

if (! function_exists('setting')) {
    /**
     * Get an application setting from the DB-backed settings repository.
     */
    function setting(string $key, $default = null)
    {
        return app(Settings::class)->get($key, $default);
    }
}

if (! function_exists('money')) {
    /**
     * Format an amount in the site's default currency, converting from $currency if needed.
     */
    function money($amount, ?string $currency = null): string
    {
        return Currency::format((float) ($amount ?? 0), $currency);
    }
}
