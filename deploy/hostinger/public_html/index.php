<?php

/**
 * Hostinger shared hosting: document root is public_html; Laravel lives in ../DO_NOT_UPLOAD_HERE
 * Upload this file to: ~/domains/quantumrealm.tech/public_html/index.php
 */

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$laravelRoot = dirname(__DIR__).'/DO_NOT_UPLOAD_HERE';

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
