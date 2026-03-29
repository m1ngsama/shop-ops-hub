<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCanManageOperations
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->canManageOperations()) {
            abort(403);
        }

        return $next($request);
    }
}
