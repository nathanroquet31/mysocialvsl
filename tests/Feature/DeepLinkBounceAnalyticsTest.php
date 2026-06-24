<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Stevebauman\Location\Facades\Location;
use Tests\TestCase;

/**
 * deeplink_bounce marks a page load that happened inside a social in-app webview,
 * which the front-end bounces straight out to the system browser. That throwaway
 * load must be recorded (so we can quantify how many views are bounce duplicates)
 * WITHOUT counting as a page_view — otherwise one human shows up as two views
 * while only the real browser session can click, structurally halving CTR.
 *
 * @see \App\Http\Controllers\Api\AnalyticsController::track
 */
class DeepLinkBounceAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    /** Realistic mobile UA so the server-side bot filter doesn't drop the event. */
    private const UA = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15';

    protected function setUp(): void
    {
        parent::setUp();
        // Location::get() is typed Position|bool — "no result" is false, not null.
        Location::shouldReceive('get')->andReturn(false);
    }

    private function makePage(): array
    {
        $user = User::factory()->create();
        $page = $user->pages()->create([
            'slug'       => 'bounce-test',
            'model_name' => 'Test',
            'page_type'  => 'landing',
            'is_active'  => true,
        ]);

        return [$user, $page];
    }

    public function test_deeplink_bounce_event_is_accepted_and_stored(): void
    {
        [, $page] = $this->makePage();

        $this->withHeaders(['User-Agent' => self::UA])
            ->postJson("/api/p/{$page->slug}/event", [
                'type'       => 'deeplink_bounce',
                'session_id' => 'sess-webview',
            ])
            ->assertOk();

        $this->assertDatabaseHas('analytics_events', [
            'page_id' => $page->id,
            'type'    => 'deeplink_bounce',
        ]);
    }

    public function test_deeplink_bounce_is_not_counted_as_a_view_or_in_ctr(): void
    {
        [$user, $page] = $this->makePage();

        // One real browser visit that views + clicks (session A) …
        $this->withHeaders(['User-Agent' => self::UA])
            ->postJson("/api/p/{$page->slug}/event", ['type' => 'page_view', 'session_id' => 'sess-a'])
            ->assertOk();
        $this->withHeaders(['User-Agent' => self::UA])
            ->postJson("/api/p/{$page->slug}/event", ['type' => 'link_click', 'session_id' => 'sess-a'])
            ->assertOk();

        // … plus a throwaway in-app webview load that got bounced out (session B).
        $this->withHeaders(['User-Agent' => self::UA])
            ->postJson("/api/p/{$page->slug}/event", ['type' => 'deeplink_bounce', 'session_id' => 'sess-b'])
            ->assertOk();

        Sanctum::actingAs($user);

        $this->getJson("/api/pages/{$page->id}/analytics?period=7")
            ->assertOk()
            // The bounce must NOT inflate views: 1 real visit, not 2.
            ->assertJsonPath('page_views', 1)
            ->assertJsonPath('link_clicks', 1)
            // CTR stays honest at 100% (1 click / 1 view), not 50% (1 / 2).
            ->assertJsonPath('ctr', 100);
    }
}
