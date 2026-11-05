<?php

namespace App\Http\Middleware;

use App\Support\CmsUrl;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Hostinger often terminates TLS at the edge while PHP sees HTTP.
 * Signed absolute URLs fail in that case; relative signed routes are preferred.
 * This still normalizes scheme for redirects and logging on the public CMS host.
 */
class EnsurePublicSiteUsesHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        $base = CmsUrl::base();

        if (! str_starts_with($base, 'https://')) {
            return $next($request);
        }

        $expectedHost = parse_url($base, PHP_URL_HOST);
        if (! is_string($expectedHost) || $expectedHost === '' || $request->getHost() !== $expectedHost) {
            return $next($request);
        }

        if (! $request->isSecure()) {
            $request->server->set('HTTPS', 'on');
            $request->server->set('SERVER_PORT', '443');
        }

        return $next($request);
    }
}
