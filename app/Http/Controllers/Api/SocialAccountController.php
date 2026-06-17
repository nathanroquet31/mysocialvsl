<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Social Analytics — architecture ready for the Instagram connection via the
 * official Instagram Graph API. Until the Meta app is configured
 * (services.instagram.client_id), accounts are added manually and stat
 * syncing is pending.
 */
class SocialAccountController extends Controller
{
    public function index(Request $request)
    {
        return response()->json($request->user()->socialAccounts()->orderByDesc('created_at')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'platform' => 'required|in:instagram,tiktok',
            'username' => 'required|string|max:100',
            'tags'     => 'nullable|array',
        ]);

        $account = $request->user()->socialAccounts()->firstOrCreate(
            ['platform' => $request->platform, 'username' => ltrim($request->username, '@')],
            ['tags' => $request->tags ?? [], 'stats' => null]
        );

        return response()->json($account, 201);
    }

    public function update(Request $request, int $id)
    {
        $account = $request->user()->socialAccounts()->findOrFail($id);
        $request->validate(['tags' => 'nullable|array']);
        $account->update($request->only(['tags']));
        return response()->json($account);
    }

    // POST /social-accounts/{id}/sync — placeholder until the official API is wired up
    public function sync(Request $request, int $id)
    {
        $account = $request->user()->socialAccounts()->findOrFail($id);

        if (! config('services.instagram.client_id')) {
            return response()->json([
                'synced'  => false,
                'message' => 'Instagram API connection is not configured yet. Add INSTAGRAM_CLIENT_ID / INSTAGRAM_CLIENT_SECRET to enable official sync.',
            ], 200);
        }

        // Extension point: call the Instagram Graph API here (followers, media, reels…)
        $account->update(['last_synced_at' => now()]);
        return response()->json(['synced' => true, 'account' => $account]);
    }

    public function destroy(Request $request, int $id)
    {
        $request->user()->socialAccounts()->findOrFail($id)->delete();
        return response()->json(['message' => 'Account disconnected.']);
    }
}
