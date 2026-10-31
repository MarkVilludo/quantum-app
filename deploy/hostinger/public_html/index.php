<?php

/**
 * Hostinger shared hosting bootstrap for public_html/index.php
 *
 * Supports:
 * - hPanel Git → public_html/quantum-app/
 * - SSH clone → ../quantum-app/ or ../DO_NOT_UPLOAD_HERE/ next to public_html
 */

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$publicHtml = __DIR__;
$laravelRoot = null;

$nested = $publicHtml.'/quantum-app';
if (is_file($nested.'/artisan')) {
    $laravelRoot = $nested;
}

if ($laravelRoot === null) {
    $domainRoot = dirname($publicHtml);
    foreach (['quantum-app', 'DO_NOT_UPLOAD_HERE'] as $dir) {
        $candidate = $domainRoot.'/'.$dir;
        if (is_file($candidate.'/artisan')) {
            $laravelRoot = $candidate;
            break;
        }
    }
}

if ($laravelRoot === null) {
    http_response_code(500);
    exit('Laravel application folder not found (expected public_html/quantum-app or ../quantum-app).');
}

if (file_exists($maintenance = $laravelRoot.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $laravelRoot.'/vendor/autoload.php';

$app = require_once $laravelRoot.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
