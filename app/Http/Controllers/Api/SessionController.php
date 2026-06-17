<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    // GET /sessions — connected devices/sessions (tokens kind=session)
    public function index(Request $request)
    {
        $currentId = $request->user()->currentAccessToken()?->id;

        $sessions = $request->user()->tokens()
            ->where('kind', 'session')
            ->orderByDesc('last_used_at')
            ->get()
            ->map(fn ($t) => [
                'id'           => $t->id,
                'ip_address'   => $t->ip_address,
                'user_agent'   => $t->user_agent,
                'created_at'   => $t->created_at,
                'last_used_at' => $t->last_used_at,
                'is_current'   => $t->id === $currentId,
            ]);

        return response()->json($sessions);
    }

    // DELETE /sessions/{id} — revoke one session
    public function destroy(Request $request, int $id)
    {
        $request->user()->tokens()->where('kind', 'session')->where('id', $id)->delete();
        return response()->json(['message' => 'Session revoked.']);
    }

    // DELETE /sessions — revoke all other sessions
    public function destroyOthers(Request $request)
    {
        $currentId = $request->user()->currentAccessToken()?->id;
        $request->user()->tokens()
            ->where('kind', 'session')
            ->where('id', '!=', $currentId)
            ->delete();
        return response()->json(['message' => 'All other sessions have been revoked.']);
    }
}
