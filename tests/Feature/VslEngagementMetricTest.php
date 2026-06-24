<?php

namespace Tests\Feature;

use App\Models\AnalyticsEvent;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Engaged CTR is the fair "this VSL converts" metric: of the sessions that
 * actually played the video, the % that clicked the CTA. It excludes drive-by
 * visitors who never engaged, so it reads higher (and truer) than raw CTR
 * (clicks ÷ all views) — which a VSL is structurally penalised on because of
 * its video step. Bounded 0–100 (clickers ⊆ watchers).
 *
 * @see \App\Http\Controllers\Api\AnalyticsController::buildVslEngagement
 */
class VslEngagementMetricTest extends TestCase
{
    use RefreshDatabase;

    private function event(Page $page, string $type, string $session): void
    {
        AnalyticsEvent::create([
            'page_id'    => $page->id,
            'type'       => $type,
            'session_id' => $session,
        ]);
    }

    public function test_engaged_ctr_counts_clicks_among_video_watchers_only(): void
    {
        $user = User::factory()->create(['plan' => 'agency']);
        $page = $user->pages()->create([
            'slug'       => 'vsl-engagement',
            'model_name' => 'Test',
            'page_type'  => 'landing',
            'is_active'  => true,
        ]);

        // Session A: viewed, watched, clicked.
        $this->event($page, 'page_view', 'sess-a');
        $this->event($page, 'video_play', 'sess-a');
        $this->event($page, 'link_click', 'sess-a');
        // Session B: viewed, watched, did NOT click.
        $this->event($page, 'page_view', 'sess-b');
        $this->event($page, 'video_play', 'sess-b');
        // Session C: viewed only — a drive-by that never engaged.
        $this->event($page, 'page_view', 'sess-c');

        Sanctum::actingAs($user);

        $this->getJson('/api/analytics/dashboard')
            ->assertOk()
            // 2 watchers (A, B), 1 of them clicked → 50%.
            ->assertJsonPath('vsl.watchers', 2)
            ->assertJsonPath('vsl.engaged_clicks', 1)
            ->assertJsonPath('vsl.engaged_ctr', 50)
            // Raw CTR is diluted by the drive-by visitor: 1 click ÷ 3 views = 33.3%.
            // The whole point — engaged CTR (50%) reads higher and fairer than raw.
            ->assertJsonPath('ctr', 33.3);
    }
}
