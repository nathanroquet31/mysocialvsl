<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\GeoRuleController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Per-page geo redirect rules. Sync fully replaces the rule set, country codes
 * are normalised to upper-case, payloads are validated, and a manager can only
 * ever touch their own pages.
 *
 * @see GeoRuleController
 */
class GeoRuleTest extends TestCase
{
    use RefreshDatabase;

    private function makePage(User $user)
    {
        return $user->pages()->create([
            'slug'       => 'geo-' . uniqid(),
            'model_name' => 'Karine',
            'page_type'  => 'landing',
            'is_active'  => true,
        ]);
    }

    public function test_rules_are_synced_and_country_codes_upper_cased(): void
    {
        $user = User::factory()->create();
        $page = $this->makePage($user);
        Sanctum::actingAs($user);

        $this->putJson("/api/pages/{$page->id}/geo-rules", [
            'rules' => [
                ['country_code' => 'fr', 'redirect_url' => 'https://onlyfans.com/fr'],
                ['country_code' => 'us', 'redirect_url' => 'https://onlyfans.com/us'],
            ],
        ])
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment(['country_code' => 'FR'])
            ->assertJsonFragment(['country_code' => 'US']);
    }

    public function test_sync_replaces_the_previous_rule_set(): void
    {
        $user = User::factory()->create();
        $page = $this->makePage($user);
        Sanctum::actingAs($user);

        $this->putJson("/api/pages/{$page->id}/geo-rules", [
            'rules' => [['country_code' => 'FR', 'redirect_url' => 'https://example.com/a']],
        ])->assertOk();

        $this->putJson("/api/pages/{$page->id}/geo-rules", [
            'rules' => [['country_code' => 'DE', 'redirect_url' => 'https://example.com/b']],
        ])
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['country_code' => 'DE'])
            ->assertJsonMissing(['country_code' => 'FR']);

        $this->getJson("/api/pages/{$page->id}/geo-rules")
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['country_code' => 'DE']);
    }

    public function test_invalid_country_code_is_rejected(): void
    {
        $user = User::factory()->create();
        $page = $this->makePage($user);
        Sanctum::actingAs($user);

        $this->putJson("/api/pages/{$page->id}/geo-rules", [
            'rules' => [['country_code' => 'FRA', 'redirect_url' => 'https://example.com']],
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('rules.0.country_code');
    }

    public function test_non_url_redirect_is_rejected(): void
    {
        $user = User::factory()->create();
        $page = $this->makePage($user);
        Sanctum::actingAs($user);

        $this->putJson("/api/pages/{$page->id}/geo-rules", [
            'rules' => [['country_code' => 'FR', 'redirect_url' => 'not-a-url']],
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('rules.0.redirect_url');
    }

    public function test_a_manager_cannot_set_rules_on_someone_elses_page(): void
    {
        $owner = User::factory()->create();
        $page = $this->makePage($owner);

        Sanctum::actingAs(User::factory()->create());

        $this->putJson("/api/pages/{$page->id}/geo-rules", [
            'rules' => [['country_code' => 'FR', 'redirect_url' => 'https://example.com']],
        ])->assertNotFound();
    }

    public function test_geo_rule_endpoints_require_authentication(): void
    {
        $this->getJson('/api/pages/1/geo-rules')->assertUnauthorized();
        $this->putJson('/api/pages/1/geo-rules', [])->assertUnauthorized();
    }
}
