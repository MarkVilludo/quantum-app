<?php

return [

    /*
    | Optional path for `php artisan mobile-app:import-apk` (local Gradle output).
    | Example (Windows): C:\Users\you\AppData\Local\QuantumRealm-build\outputs\apk\debug\app-debug.apk
    */
    'source_apk' => env('MOBILE_APP_SOURCE_APK'),

    'download_filename' => env('MOBILE_APP_DOWNLOAD_NAME', 'QuantumRealm.apk'),

    'storage_directory' => 'mobile-app',

    /*
    | Git-tracked APK under storage/app/public (registered via mobile-app:ensure-bundled).
    */
    'bundled_disk_path' => env('MOBILE_APP_BUNDLED_PATH', 'mobile-app/QuantumRealm.apk'),

    'bundled_version_name' => env('MOBILE_APP_VERSION_NAME', '1.0'),

    'bundled_version_code' => (int) env('MOBILE_APP_VERSION_CODE', 1),
];
