<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Programmatic REST API v3 (Bearer API key). The descriptor root is public;
 * everything else demands a token carrying a v3 ability and only ever exposes
 * the caller's own pages/links.
 *
 * @see \App\Http\Controllers\Api\V3\LinkController
 */
class V3ApiTest extends TestCase
{
    use RefreshDatabase;

    private function pageFor(User $user, array $overrides = []): void
    {
        $user->pages()->create(array_merge([
            'slug'       => 'slug-' . uniqid(),
            'model_name' => 'Karine',
            'page_type'  => 'landing',
            'is_active'  => true,
        ], $overrides));
    }

    public function test_root_descriptor_is_public(): void
    {
        $this->getJson('/api/v3')
            ->assertOk()
            ->assertJsonPath('version', 'v3')
            ->assertJsonStructure(['name', 'auth', 'endpoints']);
    }

    public function test_me_requires_a_token(): void
    {
        $this->getJson('/api/v3/me')->assertUnauthorized();
    }

    public function test_a_read_token_can_list_its_own_links(): void
    {
        $user = User::factory()->create();
        $this->pageFor($user, ['slug' => 'mine']);

        Sanctum::actingAs($user, ['v3:read']);

        $this->getJson('/api/v3/links')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'mine');
    }

    public function test_me_returns_the_authenticated_identity(): void
    {
        $user = User::factory()->create(['plan' => 'agency']);
        Sanctum::actingAs($user, ['v3:read']);

        $this->getJson('/api/v3/me')
            ->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('plan', 'agency');
    }

    public function test_a_token_without_a_v3_ability_is_forbidden(): void
    {
        $user = User::factory()->create();
        // A session-style token with no v3 ability must not reach v3.
        Sanctum::actingAs($user, ['session']);

        $this->getJson('/api/v3/links')->assertForbidden();
    }

    public function test_links_are_isolated_between_users(): void
    {
        $owner = User::factory()->create();
        $this->pageFor($owner, ['slug' => 'owned', 'model_name' => 'Owner']);

        $other = User::factory()->create();
        Sanctum::actingAs($other, ['v3:read']);

        // The other user has no pages → empty data, never the owner's slug.
        $this->getJson('/api/v3/links')
            ->assertOk()
            ->assertJsonCount(0, 'data')
            ->assertJsonMissing(['slug' => 'owned']);
    }

    public function test_showing_someone_elses_link_is_not_found(): void
    {
        $owner = User::factory()->create();
        $owner->pages()->create([
            'slug' => 'secret', 'model_name' => 'Owner', 'page_type' => 'landing', 'is_active' => true,
        ]);
        $ownerPageId = $owner->pages()->first()->id;

        Sanctum::actingAs(User::factory()->create(), ['v3:read']);

        $this->getJson('/api/v3/links/' . $ownerPageId)->assertNotFound();
    }
}
