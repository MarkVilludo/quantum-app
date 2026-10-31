<?php

namespace App\Support;

use Illuminate\Foundation\Application;

class HostingerPaths
{
    /**
     * Hostinger document root is public_html; the Laravel app may live at:
     * - ../quantum-app (clone/rsync layout), or
     * - ./quantum-app under public_html (hPanel Git deploy subfolder).
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

        $appRoot = base_path();
        $parent = dirname($appRoot);

        if (
            in_array(basename($appRoot), ['quantum-app', 'DO_NOT_UPLOAD_HERE'], true)
            && is_file($parent.DIRECTORY_SEPARATOR.'index.php')
        ) {
            $resolved = realpath($parent);
            if ($resolved !== false) {
                $app->usePublicPath($resolved);

                return;
            }
        }

        $sibling = $parent.DIRECTORY_SEPARATOR.'public_html';
        if (is_dir($sibling) && is_file($sibling.DIRECTORY_SEPARATOR.'index.php')) {
            $resolved = realpath($sibling);
            if ($resolved !== false) {
                $app->usePublicPath($resolved);
            }
        }
    }
}
