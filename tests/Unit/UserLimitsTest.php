<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Plan capacity logic: pageLimit() / linkLimit() and the canCreate* gates.
 * These drive both the UI and the 403 plan_limit guard in PageController.
 *
 * @see User
 */
class UserLimitsTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_plan_allows_one_page_and_one_link(): void
    {
        $user = User::factory()->create(['plan' => 'free']);

        $this->assertSame(1, $user->pageLimit());
        $this->assertSame(1, $user->linkLimit());
    }

    public function test_pro_plan_allows_five_pages_and_two_links(): void
    {
        $user = User::factory()->create(['plan' => 'pro']);

        $this->assertSame(5, $user->pageLimit());
        $this->assertSame(2, $user->linkLimit());
    }

    public function test_extra_packs_add_to_base_plan_limits(): void
    {
        $user = User::factory()->create([
            'plan' => 'pro',
            'extra_pages' => 100,
            'extra_links' => 200,
        ]);

        $this->assertSame(105, $user->pageLimit());
        $this->assertSame(202, $user->linkLimit());
    }

    public function test_agency_base_capacity_is_25_when_unconfigured(): void
    {
        // agency_pages / agency_links null = base tier (25 included).
        $user = User::factory()->create([
            'plan' => 'agency',
            'agency_pages' => null,
            'agency_links' => null,
        ]);

        $this->assertSame(25, $user->pageLimit());
        $this->assertSame(25, $user->linkLimit());
    }

    public function test_agency_tier_capacity_is_honoured(): void
    {
        $user = User::factory()->create([
            'plan' => 'agency',
            'agency_pages' => 200,
            'agency_links' => 100,
        ]);

        $this->assertSame(200, $user->pageLimit());
        $this->assertSame(100, $user->linkLimit());
    }

    public function test_agency_zero_capacity_means_unlimited(): void
    {
        // 0 is the sentinel for the unlimited (∞) tier.
        $user = User::factory()->create([
            'plan' => 'agency',
            'agency_pages' => 0,
            'agency_links' => 0,
        ]);

        $this->assertSame(PHP_INT_MAX, $user->pageLimit());
        $this->assertSame(PHP_INT_MAX, $user->linkLimit());
    }

    public function test_admin_is_unlimited_and_can_always_create(): void
    {
        $user = User::factory()->create(['plan' => 'free', 'is_admin' => true]);

        $this->assertSame(PHP_INT_MAX, $user->pageLimit());
        $this->assertSame(PHP_INT_MAX, $user->linkLimit());
        $this->assertTrue($user->canCreatePage());
        $this->assertTrue($user->canCreateLink());
    }

    public function test_can_create_page_flips_false_at_the_limit(): void
    {
        $user = User::factory()->create(['plan' => 'free']); // limit 1 page

        $this->assertTrue($user->canCreatePage());

        $user->pages()->create([
            'slug' => 'only-page',
            'model_name' => 'Karine',
            'page_type' => 'landing',
            'is_active' => true,
        ]);

        $this->assertFalse($user->fresh()->canCreatePage());
    }

    public function test_pages_and_links_count_against_separate_quotas(): void
    {
        $user = User::factory()->create(['plan' => 'free']); // 1 page + 1 link

        // A direct link does not consume the page quota and vice-versa.
        $user->pages()->create([
            'slug' => 'a-link',
            'model_name' => 'Karine',
            'page_type' => 'direct',
            'direct_url' => 'https://onlyfans.com/karine',
            'is_active' => true,
        ]);

        $this->assertTrue($user->fresh()->canCreatePage(), 'a direct link should not consume the page quota');
        $this->assertFalse($user->fresh()->canCreateLink(), 'the single direct-link quota is now used');
    }
}
