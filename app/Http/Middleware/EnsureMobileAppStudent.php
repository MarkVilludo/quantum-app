<?php

namespace App\Http\Middleware;

use App\Support\MobileAppAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMobileAppStudent
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! MobileAppAccess::isStudent($user)) {
            if ($user !== null) {
                $user->currentAccessToken()?->delete();
            }

            return MobileAppAccess::studentOnlyResponse();
        }

        return $next($request);
    }
}
