<?php

use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;

// Public VSL pages — served by Laravel so Shield Protection™ (server-side bot
// cloaking) and custom domains run before any JS loads.
Route::get('/p/{slug}', [PublicPageController::class, 'show'])->middleware('custom.domain');

// Root: on a custom domain the middleware resolves it to a page; on the main
// domain it's the SPA home.
Route::get('/', [PublicPageController::class, 'root'])->middleware('custom.domain');

// SPA fallback for the creator dashboard, marketing and any other client route.
// (api/* and built assets are served before this rule.)
Route::get('/{any}', [PublicPageController::class, 'spa'])
    ->where('any', '^(?!api/|storage/|app/).*$');
