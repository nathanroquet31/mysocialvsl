<?php

use App\Http\Controllers\Api\AffiliateController;
use App\Http\Controllers\Api\ApiKeyController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\SocialAccountController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\GeoRuleController;
use App\Http\Controllers\Api\StripeController;
use App\Http\Controllers\Api\V3\LinkController as V3LinkController;
use Illuminate\Support\Facades\Route;

// Auth (public) — throttled to block brute-force
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('/login',    [AuthController::class, 'login'])->middleware('throttle:10,1');

// Stripe webhook (public, no CSRF/auth)
Route::post('/billing/webhook', [StripeController::class, 'webhook']);

// Public pages — seen by fans (public)
Route::get('/p/{slug}',          [PageController::class, 'showPublic'])->middleware('custom.domain');
Route::post('/p/{slug}/event',   [AnalyticsController::class, 'track'])->middleware(['throttle:30,1', 'custom.domain']);

// Protected routes — authenticated manager
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me',     [AuthController::class, 'me']);
    Route::post('/logout',[AuthController::class, 'logout']);

    Route::apiResource('pages', PageController::class);
    Route::get('/pages/{id}/analytics', [AnalyticsController::class, 'stats']);
    Route::get('/analytics/dashboard',  [AnalyticsController::class, 'dashboard']);
    Route::get('/analytics/live',       [AnalyticsController::class, 'live']);
    Route::get('/slugs/{slug}/available', [PageController::class, 'checkSlug']);

    Route::patch('/user',          [UserController::class, 'update']);
    Route::patch('/user/password', [UserController::class, 'updatePassword']);
    Route::delete('/user',         [UserController::class, 'destroy']);

    Route::post('/upload/image', [UploadController::class, 'image']);
    Route::post('/upload/video', [UploadController::class, 'video']);

    Route::post('/billing/checkout', [StripeController::class, 'checkout']);
    Route::post('/billing/portal',   [StripeController::class, 'portal']);
    Route::post('/billing/addons',   [StripeController::class, 'updateAddons']);

    Route::get('/pages/{pageId}/geo-rules',  [GeoRuleController::class, 'index']);
    Route::put('/pages/{pageId}/geo-rules',  [GeoRuleController::class, 'sync']);

    // API keys (REST API v3) — key shown only once at mint
    Route::get('/api-keys',         [ApiKeyController::class, 'index']);
    Route::post('/api-keys',        [ApiKeyController::class, 'store']);
    Route::delete('/api-keys/{id}', [ApiKeyController::class, 'destroy']);

    // Active sessions / connected devices
    Route::get('/sessions',         [SessionController::class, 'index']);
    Route::delete('/sessions',      [SessionController::class, 'destroyOthers']);
    Route::delete('/sessions/{id}', [SessionController::class, 'destroy']);

    // Affiliate program
    Route::get('/affiliate', [AffiliateController::class, 'show']);

    // Social Analytics (Instagram/TikTok accounts)
    Route::get('/social-accounts',            [SocialAccountController::class, 'index']);
    Route::post('/social-accounts',           [SocialAccountController::class, 'store']);
    Route::patch('/social-accounts/{id}',     [SocialAccountController::class, 'update']);
    Route::post('/social-accounts/{id}/sync', [SocialAccountController::class, 'sync']);
    Route::delete('/social-accounts/{id}',    [SocialAccountController::class, 'destroy']);
});

// REST API v3 — programmatic access via API key (Bearer), scopes v3:read / v3:write
Route::prefix('v3')->group(function () {
    Route::get('/', [V3LinkController::class, 'root']);

    Route::middleware(['auth:sanctum', 'ability:v3:read,v3:write'])->group(function () {
        Route::get('/me',    [V3LinkController::class, 'me']);
        Route::get('/links', [V3LinkController::class, 'index']);
        Route::get('/links/{id}', [V3LinkController::class, 'show']);
        Route::get('/links/{id}/analytics', [AnalyticsController::class, 'stats']);
    });
});
