<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\ApiKeyController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * v3 API keys: the plaintext token is returned exactly once at mint and never
 * again, scopes are constrained to v3:read/v3:write, and revocation only ever
 * touches the caller's own keys.
 *
 * @see ApiKeyController
 */
class ApiKeyManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_minting_a_key_returns_the_plaintext_once(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/api-keys', ['name' => 'CI key'])
            ->assertCreated()
            ->assertJsonStructure(['id', 'name', 'abilities', 'plain_text_key', 'created_at']);
    }

    public function test_listing_keys_never_exposes_the_secret(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/api-keys', ['name' => 'Visible key'])->assertCreated();

        $response = $this->getJson('/api/api-keys')->assertOk();

        $response->assertJsonMissing(['plain_text_key' => true]);
        $this->assertStringNotContainsString('plain_text_key', $response->getContent());
        $response->assertJsonFragment(['name' => 'Visible key', 'status' => 'active']);
    }

    public function test_default_scopes_are_read_and_write(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/api-keys', ['name' => 'Defaults'])
            ->assertCreated()
            ->assertJsonPath('abilities', ['v3:read', 'v3:write']);
    }

    public function test_unknown_scopes_are_rejected(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/api-keys', [
            'name'   => 'Bad scope',
            'scopes' => ['v3:admin'],
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('scopes.0');
    }

    public function test_only_api_keys_are_listed_not_session_tokens(): void
    {
        $user = User::factory()->create();
        // A session token must never surface in the API-keys list.
        $session = $user->createToken('browser');
        $session->accessToken->forceFill(['kind' => 'session'])->save();
        Sanctum::actingAs($user);

        $this->postJson('/api/api-keys', ['name' => 'Real key'])->assertCreated();

        $this->getJson('/api/api-keys')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Real key'])
            ->assertJsonMissing(['name' => 'browser']);
    }

    public function test_a_user_cannot_revoke_another_users_key(): void
    {
        $owner = User::factory()->create();
        $ownerKey = $owner->createToken('owned', ['v3:read']);
        $ownerKey->accessToken->forceFill(['kind' => 'api'])->save();

        Sanctum::actingAs(User::factory()->create());
        $this->deleteJson('/api/api-keys/' . $ownerKey->accessToken->id)->assertOk();

        // Still there — the delete was scoped to the (other) caller's keys.
        $this->assertDatabaseHas('personal_access_tokens', ['id' => $ownerKey->accessToken->id]);
    }

    public function test_owner_can_revoke_their_key(): void
    {
        $user = User::factory()->create();
        $key = $user->createToken('to-revoke', ['v3:read']);
        $key->accessToken->forceFill(['kind' => 'api'])->save();
        Sanctum::actingAs($user);

        $this->deleteJson('/api/api-keys/' . $key->accessToken->id)->assertOk();

        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $key->accessToken->id]);
    }
}
