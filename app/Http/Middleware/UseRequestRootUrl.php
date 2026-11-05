<?php

namespace App\Http\Middleware;

use App\Support\CmsUrl;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Use the incoming Host for generated URLs (Ziggy, Vite, redirects) on local/emulator.
 * Production keeps {@see CmsUrl::base()} so signed email links validate correctly.
 */
class UseRequestRootUrl
{
    private const DEV_HOSTS = ['10.0.2.2', '10.0.3.2', '127.0.0.1', 'localhost'];

    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        if (in_array($host, self::DEV_HOSTS, true)) {
            if ($request->getHttpHost()) {
                URL::forceRootUrl($request->getSchemeAndHttpHost());
            }
        } else {
            CmsUrl::applyRoot();
        }

        return $next($request);
    }
}
