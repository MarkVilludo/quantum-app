<?php

/**
 * Hostinger shared: document root is public_html; Laravel is ../quantum-app or ../DO_NOT_UPLOAD_HERE
 */

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$domainRoot = dirname(__DIR__);
$laravelRoot = null;

foreach (['quantum-app', 'DO_NOT_UPLOAD_HERE'] as $dir) {
    $candidate = $domainRoot.'/'.$dir;
    if (is_file($candidate.'/artisan')) {
        $laravelRoot = $candidate;
        break;
    }
}

if ($laravelRoot === null) {
    http_response_code(500);
    exit('Laravel application folder not found next to public_html.');
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
