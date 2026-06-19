<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\PageController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * The PageController must reject page/link creation past the plan capacity with
 * a 403 + `plan_limit` error code (the front-end keys upgrade prompts off it).
 *
 * @see PageController::store
 */
class QuotaEnforcementTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_user_can_create_their_first_page(): void
    {
        Sanctum::actingAs(User::factory()->create(['plan' => 'free']));

        $this->postJson('/api/pages', ['model_name' => 'Karine'])
            ->assertCreated();
    }

    public function test_free_user_is_blocked_on_the_second_page_with_plan_limit(): void
    {
        $user = User::factory()->create(['plan' => 'free']);
        $user->pages()->create([
            'slug' => 'first',
            'model_name' => 'Karine',
            'page_type' => 'landing',
            'is_active' => true,
        ]);
        Sanctum::actingAs($user);

        $this->postJson('/api/pages', ['model_name' => 'Second'])
            ->assertStatus(403)
            ->assertJsonPath('error', 'plan_limit')
            ->assertJsonPath('limit', 1);
    }

    public function test_free_user_is_blocked_on_the_second_direct_link(): void
    {
        $user = User::factory()->create(['plan' => 'free']);
        $user->pages()->create([
            'slug' => 'link-1',
            'model_name' => 'Karine',
            'page_type' => 'direct',
            'direct_url' => 'https://onlyfans.com/karine',
            'is_active' => true,
        ]);
        Sanctum::actingAs($user);

        $this->postJson('/api/pages', [
            'model_name' => 'Link2',
            'page_type' => 'direct',
            'direct_url' => 'https://onlyfans.com/other',
        ])
            ->assertStatus(403)
            ->assertJsonPath('error', 'plan_limit');
    }

    public function test_pro_user_can_create_up_to_five_pages_then_is_blocked(): void
    {
        $user = User::factory()->create(['plan' => 'pro']);
        for ($i = 1; $i <= 5; $i++) {
            $user->pages()->create([
                'slug' => "page-$i",
                'model_name' => "Model $i",
                'page_type' => 'landing',
                'is_active' => true,
            ]);
        }
        Sanctum::actingAs($user);

        $this->postJson('/api/pages', ['model_name' => 'Sixth'])
            ->assertStatus(403)
            ->assertJsonPath('error', 'plan_limit')
            ->assertJsonPath('limit', 5);
    }

    public function test_admin_is_never_blocked(): void
    {
        $user = User::factory()->create(['plan' => 'free', 'is_admin' => true]);
        $user->pages()->create([
            'slug' => 'existing',
            'model_name' => 'Karine',
            'page_type' => 'landing',
            'is_active' => true,
        ]);
        Sanctum::actingAs($user);

        $this->postJson('/api/pages', ['model_name' => 'Another'])
            ->assertCreated();
    }

    public function test_creating_a_page_requires_authentication(): void
    {
        $this->postJson('/api/pages', ['model_name' => 'Karine'])
            ->assertUnauthorized();
    }
}
