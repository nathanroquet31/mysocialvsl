<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'ref'      => 'nullable|string|max:32',
        ]);

        $referrer = $request->ref
            ? User::where('affiliate_code', strtoupper($request->ref))->first()
            : null;

        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'referred_by' => $referrer?->id,
        ]);

        $token = $this->issueSessionToken($user, $request);

        return response()->json(['token' => $token, 'user' => $user], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Ces identifiants sont incorrects.'],
            ]);
        }

        $token = $this->issueSessionToken($user, $request);

        return response()->json(['token' => $token, 'user' => $user]);
    }

    private function issueSessionToken(User $user, Request $request): string
    {
        $result = $user->createToken('auth_token');
        $result->accessToken->forceFill([
            'kind'       => 'session',
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 500),
        ])->save();

        return $result->plainTextToken;
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnecté.']);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $pages = $user->pages()->selectRaw("page_type, count(*) as cnt")->groupBy('page_type')->pluck('cnt', 'page_type');
        return response()->json(array_merge($user->toArray(), [
            'page_limit'   => $user->pageLimit(),
            'link_limit'   => $user->linkLimit(),
            'pages_count'  => $pages->except('direct')->sum(),
            'direct_count' => $pages->get('direct', 0),
            'extra_pages'  => (int) ($user->extra_pages ?? 0),
            'extra_links'  => (int) ($user->extra_links ?? 0),
        ]));
    }
}
