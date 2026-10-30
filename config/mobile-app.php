<?php

return [

    /*
    | Optional path for `php artisan mobile-app:import-apk` (local Gradle output).
    | Example (Windows): C:\Users\you\AppData\Local\QuantumRealm-build\outputs\apk\debug\app-debug.apk
    */
    'source_apk' => env('MOBILE_APP_SOURCE_APK'),

    'download_filename' => env('MOBILE_APP_DOWNLOAD_NAME', 'QuantumRealm.apk'),

    'storage_directory' => 'mobile-app',
];
