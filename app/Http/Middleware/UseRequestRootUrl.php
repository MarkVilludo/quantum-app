<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Use the incoming Host for generated URLs (Ziggy, Vite, redirects).
 * Lets the Android emulator open CMS pages at 10.0.2.2:8000 without broken 127.0.0.1 links.
 */
class UseRequestRootUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->getHttpHost()) {
            URL::forceRootUrl($request->getSchemeAndHttpHost());
        }

        return $next($request);
    }
}
