<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    // GET /api-keys — liste des clés API v3 (jamais le secret, affiché une seule fois au mint)
    public function index(Request $request)
    {
        $keys = $request->user()->tokens()
            ->where('kind', 'api')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($t) => [
                'id'           => $t->id,
                'name'         => $t->name,
                'abilities'    => $t->abilities,
                'status'       => 'active',
                'created_at'   => $t->created_at,
                'last_used_at' => $t->last_used_at,
            ]);

        return response()->json($keys);
    }

    // POST /api-keys — mint une clé v3 ; le token en clair n'est retourné qu'une fois
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:100',
            'scopes' => 'nullable|array',
            'scopes.*' => 'in:v3:read,v3:write',
        ]);

        $abilities = $request->scopes ?: ['v3:read', 'v3:write'];
        $result = $request->user()->createToken($request->name, $abilities);

        $result->accessToken->forceFill([
            'kind'       => 'api',
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 500),
        ])->save();

        return response()->json([
            'id'        => $result->accessToken->id,
            'name'      => $result->accessToken->name,
            'abilities' => $abilities,
            // Affiché une seule fois — non récupérable ensuite
            'plain_text_key' => $result->plainTextToken,
            'created_at'     => $result->accessToken->created_at,
        ], 201);
    }

    // DELETE /api-keys/{id} — révocation
    public function destroy(Request $request, int $id)
    {
        $request->user()->tokens()->where('kind', 'api')->where('id', $id)->delete();
        return response()->json(['message' => 'API key revoked.']);
    }
}
