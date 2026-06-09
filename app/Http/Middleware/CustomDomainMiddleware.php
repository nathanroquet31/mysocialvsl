<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomDomainMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);

        // Si on est sur le domaine principal, pas de traitement custom
        if ($host === $appHost || str_ends_with($host, '.' . $appHost)) {
            return $next($request);
        }

        // Chercher une page avec ce custom_domain
        $page = \App\Models\Page::where('custom_domain', $host)
            ->where('is_active', true)
            ->first();

        if (! $page) {
            return response()->json(['message' => 'Domain not found.'], 404);
        }

        // Injecter le slug dans la request pour que showPublic puisse le traiter
        $request->route()->setParameter('slug', $page->slug);

        return $next($request);
    }
}
