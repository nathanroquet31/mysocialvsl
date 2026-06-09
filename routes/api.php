<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\GeoRuleController;
use App\Http\Controllers\Api\StripeController;
use Illuminate\Support\Facades\Route;

// Auth (public)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Stripe webhook (public, no CSRF/auth)
Route::post('/billing/webhook', [StripeController::class, 'webhook']);

// Pages publiques — vues par les fans (public)
Route::get('/p/{slug}',          [PageController::class, 'showPublic'])->middleware('custom.domain');
Route::post('/p/{slug}/event',   [AnalyticsController::class, 'track'])->middleware(['throttle:30,1', 'custom.domain']);

// Routes protégées — manager connecté
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me',     [AuthController::class, 'me']);
    Route::post('/logout',[AuthController::class, 'logout']);

    Route::apiResource('pages', PageController::class);
    Route::get('/pages/{id}/analytics', [AnalyticsController::class, 'stats']);
    Route::get('/slugs/{slug}/available', [PageController::class, 'checkSlug']);

    Route::patch('/user',          [UserController::class, 'update']);
    Route::patch('/user/password', [UserController::class, 'updatePassword']);
    Route::delete('/user',         [UserController::class, 'destroy']);

    Route::post('/upload/image', [UploadController::class, 'image']);
    Route::post('/upload/video', [UploadController::class, 'video']);

    Route::post('/billing/checkout', [StripeController::class, 'checkout']);
    Route::post('/billing/portal',   [StripeController::class, 'portal']);

    Route::get('/pages/{pageId}/geo-rules',  [GeoRuleController::class, 'index']);
    Route::put('/pages/{pageId}/geo-rules',  [GeoRuleController::class, 'sync']);
});
