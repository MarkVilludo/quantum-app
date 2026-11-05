<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || $user->role !== 'admin') {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Only administrators can manage users.');
        }

        return $next($request);
    }
}
