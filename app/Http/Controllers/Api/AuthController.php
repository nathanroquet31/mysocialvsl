<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewLogin;
use App\Notifications\PlanChanged;
use App\Notifications\Welcome;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Stevebauman\Location\Facades\Location;

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

        // Beta go-to-market: signing up through a coach/manager's affiliate code
        // grants a card-free Agency trial. Gated on the referrer being a flagged
        // beta partner so trials can't be self-minted by any referrer.
        if ($referrer?->is_beta_partner) {
            $user->startTrial('agency', 60);
        }

        // Account notifications: welcome everyone; announce the beta Agency trial.
        $user->notify(new Welcome());
        if ($referrer?->is_beta_partner) {
            $user->notify(new PlanChanged('agency', 'upgraded'));
        }

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
                'email' => ['These credentials are incorrect.'],
            ]);
        }

        // Security notification: only when this IP hasn't been seen on the
        // account before (checked before the new session token is issued).
        $ip = $request->ip();
        $knownIp = $ip && $user->tokens()
            ->where('kind', 'session')
            ->where('ip_address', $ip)
            ->exists();

        $token = $this->issueSessionToken($user, $request);

        if ($ip && ! $knownIp) {
            $city = $country = null;
            try {
                $position = Location::get($ip);
                if ($position) {
                    $city    = $position->cityName ?: null;
                    $country = $position->countryName ?: null;
                }
            } catch (\Throwable $e) {
                // Geo is best-effort; never block login on it.
            }
            $user->notify(new NewLogin($city, $country, $ip));
        }

        return response()->json(['token' => $token, 'user' => $user]);
    }

    /**
     * Email a password-reset link. Always returns a generic success message so
     * the endpoint can't be used to probe which emails have accounts.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        Password::sendResetLink($request->only('email'));

        return response()->json([
            'message' => 'If an account exists for that email, a reset link is on its way.',
        ]);
    }

    /**
     * Complete the reset: verify the token and set the new password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required|string',
            'email'    => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => [__($status)]]);
        }

        return response()->json(['message' => 'Your password has been reset. You can sign in now.']);
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

        return response()->json(['message' => 'Logged out.']);
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
            'on_trial'         => $user->onTrial(),
            'trial_days_left'  => $user->trialDaysLeft(),
        ]));
    }
}
