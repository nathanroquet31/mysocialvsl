<?php

namespace Tests\Feature;

use App\Http\Controllers\Concerns\ResolvesPublicPages;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Per-page white-label toggle: pages.show_branding lets an Agency account hide
 * the "Powered by MySocialVSL" footer on a given page (set in the page editor,
 * step 4). Default false = white-label, so existing Agency pages keep today's
 * behaviour. It's Agency-only and enforced server-side: any other plan always
 * shows the footer, whatever is stored, and can't set the field via the API.
 *
 * @see ResolvesPublicPages::resolvePublicPage
 * @see \App\Http\Controllers\Api\PageController::update
 */
class WhiteLabelBrandingTest extends TestCase
{
    use RefreshDatabase;

    private function makePage(User $user, array $overrides = []): Page
    {
        return $user->pages()->create(array_merge([
            'slug'       => 'wl-' . uniqid(),
            'model_name' => 'Test',
            'page_type'  => 'landing',
            'is_active'  => true,
        ], $overrides));
    }

    private function resolver()
    {
        return new class
        {
            use ResolvesPublicPages;

            public function resolve(string $slug)
            {
                return $this->resolvePublicPage($slug);
            }
        };
    }

    public function test_agency_user_can_set_page_show_branding(): void
    {
        $user = User::factory()->create(['plan' => 'agency']);
        $page = $this->makePage($user);
        Sanctum::actingAs($user);

        $this->patchJson("/api/pages/{$page->id}", ['show_branding' => true])
            ->assertOk();

        $this->assertTrue($page->refresh()->show_branding);
    }

    public function test_non_agency_user_cannot_set_page_show_branding(): void
    {
        // A Pro user firing the field must never flip it — the API ignores it.
        $user = User::factory()->create(['plan' => 'pro']);
        $page = $this->makePage($user, ['show_branding' => false]);
        Sanctum::actingAs($user);

        $this->patchJson("/api/pages/{$page->id}", ['show_branding' => true])
            ->assertOk();

        $this->assertFalse($page->refresh()->show_branding);
    }

    public function test_agency_page_respects_stored_show_branding(): void
    {
        $user = User::factory()->create(['plan' => 'agency']);

        $hidden = $this->makePage($user, ['slug' => 'wl-hidden', 'show_branding' => false]);
        $shown  = $this->makePage($user, ['slug' => 'wl-shown',  'show_branding' => true]);

        $this->assertFalse($this->resolver()->resolve($hidden->slug)->show_branding);
        $this->assertTrue($this->resolver()->resolve($shown->slug)->show_branding);
    }

    public function test_non_agency_page_always_shows_branding_regardless_of_column(): void
    {
        // Even if the column were somehow true, a non-Agency page must show the
        // footer — the resolver forces it, so the perk can't leak across plans.
        $user = User::factory()->create(['plan' => 'pro']);
        $page = $this->makePage($user, ['slug' => 'wl-pro', 'show_branding' => true]);

        $this->assertTrue($this->resolver()->resolve($page->slug)->show_branding);
    }
}
