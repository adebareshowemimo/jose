<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Certificate uploads
    |--------------------------------------------------------------------------
    |
    | Maximum size (in megabytes) allowed for a certification's certificate
    | file on the resume builder. Used by both server-side validation and the
    | upload hint shown in the UI so they never drift apart.
    |
    */

    'certificate_max_mb' => (int) env('CERTIFICATE_MAX_MB', 5),

];
