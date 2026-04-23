<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Internship Configuration
    |--------------------------------------------------------------------------
    */

    // Default geofence radius in meters
    'geofence_radius' => env('INTERNSHIP_GEOFENCE_RADIUS', 500),

    // Maximum file sizes (in KB)
    'max_file_sizes' => [
        'cv' => 10240,           // 10MB
        'agreement' => 10240,    // 10MB
        'laporan' => 20480,      // 20MB
        'signature' => 2048,     // 2MB
    ],

    // Certificate number prefix
    'certificate_prefix' => env('CERTIFICATE_PREFIX', 'CERT'),

    // Allowed internship durations (in months)
    'min_duration_months' => 3,
    'max_duration_months' => 12,

];
