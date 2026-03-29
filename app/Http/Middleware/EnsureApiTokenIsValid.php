<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiTokenIsValid
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isAdmin()) {
            return $next($request);
        }

        $configuredToken = trim((string) config('shop_ops.api_token'));
        $providedToken = trim((string) ($request->bearerToken() ?: $request->header('X-Shop-Ops-Token')));

        if ($configuredToken === '' || $providedToken === '' || ! hash_equals($configuredToken, $providedToken)) {
            return new JsonResponse(['message' => 'Unauthenticated.'], 401);
        }

        return $next($request);
    }
}
