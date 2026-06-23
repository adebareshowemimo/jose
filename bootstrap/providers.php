<?php

use App\Providers\AdminBroadcastServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\SettingsServiceProvider;
use App\Providers\SocialiteEventServiceProvider;

return [
    AppServiceProvider::class,
    SettingsServiceProvider::class,
    SocialiteEventServiceProvider::class,
    AdminBroadcastServiceProvider::class,
];
