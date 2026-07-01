<?php

namespace Tests\Feature;

use App\Models\AnalyticsEvent;
use App\Models\Page;
use App\Models\User;
use App\Services\NetworkAnalytics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * The cross-agency analytics engine: overview volume, benchmark percentiles,
 * form → outcome pooling, engaged CTR, and admin-only gating on the endpoint.
 *
 * @see \App\Services\NetworkAnalytics
 */
class NetworkAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private function event(int $pageId, string $type, ?string $session, ?float $value = null): void
    {
        AnalyticsEvent::create([
            'page_id'    => $pageId,
            'type'       => $type,
            'session_id' => $session,
            'value'      => $value,
            'created_at' => now(),
        ]);
    }

    public function test_engine_computes_overview_benchmarks_and_shape(): void
    {
        $user = User::factory()->create(['plan' => 'agency']);
        $page = Page::create([
            'user_id'           => $user->id,
            'slug'              => 'p1',
            'model_name'        => 'P1',
            'template'          => 'vsl-bandeau',
            'video_url'         => 'https://v/x.mp4',
            'deep_link_enabled' => true,
            'is_active'         => true,
        ]);

        // 25 distinct visits, 20 plays, 10 clicks (sessions overlap so engaged CTR is exact).
        for ($i = 1; $i <= 25; $i++) $this->event($page->id, 'page_view', "s$i");
        for ($i = 1; $i <= 20; $i++) $this->event($page->id, 'video_play', "s$i");
        for ($i = 1; $i <= 10; $i++) $this->event($page->id, 'link_click', "s$i", 12.0);

        $snap = app(NetworkAnalytics::class)->snapshot(null, null);

        // Overview: real visits, not raw events.
        $o = $snap['overview'];
        $this->assertSame(25, $o['page_views']);
        $this->assertSame(10, $o['link_clicks']);
        $this->assertSame(20, $o['video_plays']);
        $this->assertSame(40.0, $o['ctr']);
        $this->assertSame(80.0, $o['play_rate']);
        // watchers = 20, clicks among watchers = 10 → engaged CTR 50%.
        $this->assertSame(50.0, $o['engaged_ctr']);
        $this->assertSame(1, $o['active_pages']);

        // Benchmarks: the page clears the 20-view threshold, so it enters the distribution.
        $this->assertSame(1, $snap['benchmarks']['sample_pages']);
        $this->assertSame(40.0, $snap['benchmarks']['ctr']['median']);
        $this->assertSame(80.0, $snap['benchmarks']['play_rate']['median']);

        // Shape: pooled CTR per template.
        $tpl = collect($snap['by_shape']['by_template'])->firstWhere('key', 'vsl-bandeau');
        $this->assertNotNull($tpl);
        $this->assertSame(40.0, $tpl['ctr']);
        $this->assertSame(25, $tpl['views']);

        // Feature adoption.
        $this->assertSame(100.0, $snap['feature_adoption']['features']['video']);
        $this->assertSame(100.0, $snap['feature_adoption']['features']['bandeau']);

        // Top pages.
        $this->assertCount(1, $snap['top_pages']);
        $this->assertSame(40.0, $snap['top_pages'][0]['ctr']);
    }

    public function test_low_volume_pages_stay_out_of_the_benchmark(): void
    {
        $user = User::factory()->create();
        $page = Page::create([
            'user_id' => $user->id, 'slug' => 'p2', 'model_name' => 'P2', 'is_active' => true,
        ]);
        // Only 5 views → under the threshold → benchmark sample stays empty.
        for ($i = 1; $i <= 5; $i++) $this->event($page->id, 'page_view', "v$i");

        $snap = app(NetworkAnalytics::class)->snapshot(null, null);

        $this->assertSame(0, $snap['benchmarks']['sample_pages']);
        $this->assertNull($snap['benchmarks']['ctr']['median']);
    }

    public function test_network_endpoint_is_admin_only(): void
    {
        Sanctum::actingAs(User::factory()->create(['is_admin' => false]));
        $this->getJson('/api/admin/network')->assertForbidden();

        Sanctum::actingAs(User::factory()->create(['is_admin' => true]));
        $this->getJson('/api/admin/network?preset=all')
            ->assertOk()
            ->assertJsonStructure([
                'overview', 'benchmarks', 'by_shape', 'top_pages', 'growth', 'feature_adoption',
            ]);
    }
}
