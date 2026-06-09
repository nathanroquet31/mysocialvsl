<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class GeoRuleController extends Controller
{
    public function index(Request $request, string $pageId)
    {
        $page = $request->user()->pages()->findOrFail($pageId);
        return response()->json($page->geoRules);
    }

    public function sync(Request $request, string $pageId)
    {
        $page = $request->user()->pages()->findOrFail($pageId);

        $request->validate([
            'rules'                  => 'required|array',
            'rules.*.country_code'   => 'required|string|size:2',
            'rules.*.redirect_url'   => 'required|url',
        ]);

        $page->geoRules()->delete();

        foreach ($request->rules as $i => $rule) {
            $page->geoRules()->create([
                'country_code' => strtoupper($rule['country_code']),
                'redirect_url' => $rule['redirect_url'],
                'order'        => $i,
            ]);
        }

        return response()->json($page->geoRules()->get());
    }
}
