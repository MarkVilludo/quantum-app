<?php

namespace App\Support;

use Illuminate\Foundation\Application;

class HostingerPaths
{
    /**
     * Hostinger serves the site from ../public_html while the app lives in quantum-app/ or DO_NOT_UPLOAD_HERE/.
     */
    public static function useSiblingPublicHtmlIfPresent(Application $app): void
    {
        if ($override = env('LARAVEL_PUBLIC_PATH')) {
            $path = realpath($override);
            if ($path !== false && is_dir($path)) {
                $app->usePublicPath($path);

                return;
            }
        }

        $sibling = dirname(base_path()).DIRECTORY_SEPARATOR.'public_html';
        if (is_dir($sibling) && is_file($sibling.DIRECTORY_SEPARATOR.'index.php')) {
            $resolved = realpath($sibling);
            if ($resolved !== false) {
                $app->usePublicPath($resolved);
            }
        }
    }
}
