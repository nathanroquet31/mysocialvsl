<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesPublicPages;
use App\Models\Page;
use Illuminate\Http\Request;

/**
 * Serves the front-end from Laravel (single origin) so that public VSL pages
 * and custom domains run through the server-side Shield Protection™ cloaking
 * before any JS loads. The creator dashboard / marketing routes are served as
 * the plain SPA shell.
 */
class PublicPageController extends Controller
{
    use ResolvesPublicPages;

    /**
     * Public VSL page: /p/{slug} (and custom domains resolved to a slug by
     * CustomDomainMiddleware). Bots get the decoy; humans get the SPA shell
     * booted on the right page.
     */
    public function show(Request $request, string $slug)
    {
        $page = $this->resolvePublicPage($slug);

        if ($guard = $this->publicPageGuards($page, $request)) {
            return $guard;
        }

        return $this->renderApp($slug, $page);
    }

    /**
     * Root "/". On a custom domain the middleware has resolved a slug → serve
     * that page. On the main domain it's just the SPA home.
     */
    public function root(Request $request)
    {
        $slug = $request->route('slug');

        if ($slug) {
            return $this->show($request, $slug);
        }

        return $this->renderApp();
    }

    /**
     * First path segments that are app / marketing routes, never page slugs.
     * Keeps a clean /{slug} link from ever shadowing the dashboard, auth, etc.
     */
    private const RESERVED_PATHS = [
        'p', 'login', 'register', 'forgot-password', 'reset-password',
        'dashboard', 'pages', 'billing', 'privacy', 'terms', 'cookies',
    ];

    /**
     * Catch-all. Clean public links — mysocialvsl.com/{slug} (no /p/) — are
     * served here: a single-segment, non-reserved path that matches an active
     * page gets the full public page (same cloaking as /p/{slug}). Everything
     * else is the SPA shell (dashboard / marketing / unknown routes).
     */
    public function spa(Request $request)
    {
        $path = trim($request->path(), '/');

        if ($path !== '' && ! str_contains($path, '/') && ! in_array($path, self::RESERVED_PATHS, true)) {
            if (Page::where('slug', $path)->where('is_active', true)->exists()) {
                return $this->show($request, $path);
            }
        }

        return $this->renderApp();
    }

    /**
     * Render the built front-end shell. When a page is provided, inject its
     * slug (so custom-domain roots know what to load) and, for non-protected
     * pages, real Open Graph tags for nice link previews. Protected pages keep
     * generic server-side meta — the real content is painted client-side only.
     */
    protected function renderApp(?string $slug = null, $page = null)
    {
        return response()
            ->view('app', [
                'slug' => $slug,
                'page' => $page,
                'assets' => $this->viteAssets(),
            ])
            ->header('X-Robots-Tag', $page && $page->bot_protection ? 'noindex, nofollow' : 'all');
    }

    /**
     * Resolve the built Vite entry assets from the manifest.
     *
     * @return array{js: ?string, css: array<int, string>}
     */
    protected function viteAssets(): array
    {
        $manifestPath = public_path('app/.vite/manifest.json');

        if (! is_file($manifestPath)) {
            // Dev fallback — Vite dev server serves the entry directly.
            return ['js' => env('VITE_DEV_SERVER', 'http://localhost:5173').'/src/main.js', 'css' => [], 'dev' => true];
        }

        $manifest = json_decode((string) file_get_contents($manifestPath), true) ?: [];
        $entry = $manifest['src/main.js'] ?? null;

        return [
            'js'  => $entry ? '/app/'.$entry['file'] : null,
            'css' => collect($entry['css'] ?? [])->map(fn ($f) => '/app/'.$f)->all(),
            'dev' => false,
        ];
    }
}
