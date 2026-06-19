<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate a route to admins only. Runs after auth:sanctum, so the user is already
 * resolved; we just check the is_admin flag (the same flag that grants ∞ quotas).
 */
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isAdmin()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return $next($request);
    }
}
