<?php

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
