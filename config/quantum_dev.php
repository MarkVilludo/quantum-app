<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Development progression tools
    |--------------------------------------------------------------------------
    |
    | When true, admins/teachers can use CMS actions and Artisan commands that
    | instantly complete curriculum progress for a student (local QA only).
    |
    */
    'progression_tools_enabled' => (bool) env(
        'QUANTUM_DEV_TOOLS',
        env('APP_ENV', 'production') === 'local'
    ),

];
