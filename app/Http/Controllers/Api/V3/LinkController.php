<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * REST API v3 — accès programmatique aux liens/pages et analytics.
 * Auth : Bearer token (clé API v3, scopes v3:read / v3:write).
 */
class LinkController extends Controller
{
    // GET /api/v3 — descripteur de l'API
    public function root()
    {
        return response()->json([
            'name'    => 'MySocialVSL API',
            'version' => 'v3',
            'auth'    => 'Bearer token (mint an API key from the dashboard — API Access)',
            'mcp_endpoint' => rtrim(config('app.url'), '/') . '/api/v3/mcp',
            'endpoints' => [
                'GET /api/v3/me',
                'GET /api/v3/links',
                'GET /api/v3/links/{id}',
                'GET /api/v3/links/{id}/analytics?period=7|30|90',
            ],
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'plan'  => $user->plan,
        ]);
    }

    public function index(Request $request)
    {
        $pages = $request->user()->pages()->with('links')->get()->map(fn ($p) => $this->serialize($p));
        return response()->json(['data' => $pages]);
    }

    public function show(Request $request, string $id)
    {
        $page = $request->user()->pages()->with('links')->findOrFail($id);
        return response()->json(['data' => $this->serialize($page)]);
    }

    private function serialize($page): array
    {
        return [
            'id'         => $page->id,
            'type'       => $page->page_type,
            'slug'       => $page->slug,
            'url'        => rtrim(config('app.url'), '/') . '/p/' . $page->slug,
            'name'       => $page->model_name,
            'is_active'  => (bool) $page->is_active,
            'custom_domain' => $page->custom_domain,
            'direct_url' => $page->direct_url,
            'created_at' => $page->created_at,
            'links'      => $page->links->map(fn ($l) => [
                'id' => $l->id, 'type' => $l->type, 'label' => $l->label,
                'url' => $l->url, 'is_visible' => (bool) $l->is_visible, 'order' => $l->order,
            ]),
        ];
    }
}
