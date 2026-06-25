<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\SessionController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Connected-devices management. These tests authenticate with a *real* Bearer
 * token (not Sanctum::actingAs) so that currentAccessToken() resolves to a
 * persisted row — the only way to exercise the "current session" logic.
 *
 * @see SessionController
 */
class SessionManagementTest extends TestCase
{
    use RefreshDatabase;

    /** Create a persisted session token and return its plaintext for Bearer auth. */
    private function sessionToken(User $user, string $name): string
    {
        $token = $user->createToken($name);
        $token->accessToken->forceFill(['kind' => 'session'])->save();

        return $token->plainTextToken;
    }

    public function test_index_lists_session_tokens_and_flags_the_current_one(): void
    {
        $user = User::factory()->create();
        $current = $this->sessionToken($user, 'this-device');
        $this->sessionToken($user, 'other-device');

        $response = $this->withToken($current)->getJson('/api/sessions')->assertOk()->assertJsonCount(2);

        $sessions = collect($response->json());
        $this->assertSame(1, $sessions->where('is_current', true)->count());
    }

    public function test_api_keys_do_not_appear_among_sessions(): void
    {
        $user = User::factory()->create();
        $current = $this->sessionToken($user, 'browser');
        $apiKey = $user->createToken('api');
        $apiKey->accessToken->forceFill(['kind' => 'api'])->save();

        $this->withToken($current)->getJson('/api/sessions')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonMissing(['user_agent' => 'api']);
    }

    public function test_revoking_a_single_session_removes_only_that_token(): void
    {
        $user = User::factory()->create();
        $current = $this->sessionToken($user, 'keep');
        $victim = $user->createToken('drop');
        $victim->accessToken->forceFill(['kind' => 'session'])->save();

        $this->withToken($current)->deleteJson('/api/sessions/' . $victim->accessToken->id)->assertOk();

        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $victim->accessToken->id]);
        $this->withToken($current)->getJson('/api/sessions')->assertJsonCount(1);
    }

    public function test_destroy_others_keeps_the_current_session_only(): void
    {
        $user = User::factory()->create();
        $current = $this->sessionToken($user, 'this-device');
        $this->sessionToken($user, 'phone');
        $this->sessionToken($user, 'tablet');

        $this->withToken($current)->deleteJson('/api/sessions')->assertOk();

        $remaining = $this->withToken($current)->getJson('/api/sessions')->assertOk()->json();
        $this->assertCount(1, $remaining);
        $this->assertTrue($remaining[0]['is_current']);
    }

    public function test_sessions_endpoints_require_authentication(): void
    {
        $this->getJson('/api/sessions')->assertUnauthorized();
        $this->deleteJson('/api/sessions')->assertUnauthorized();
    }
}
