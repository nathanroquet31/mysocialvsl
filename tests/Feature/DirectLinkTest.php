<?php

namespace Tests\Feature;

use App\Http\Controllers\Concerns\ResolvesPublicPages;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stevebauman\Location\Facades\Location;
use Tests\TestCase;

/**
 * Direct links (page_type = "direct") must NOT do a raw 302 — that would trap
 * the visitor inside the Instagram/TikTok in-app webview. Instead a tiny HTML
 * bounce page (direct-redirect.blade.php) replays the deep-link bypass so the
 * visitor escapes the in-app browser. The bypass is gated on deep_link_enabled.
 *
 * @see ResolvesPublicPages::publicPageGuards
 */
class DirectLinkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Location::get() is typed Position|bool — a "no result" is false, not null.
        Location::shouldReceive('get')->andReturn(false);
    }

    private function makeDirectPage(array $overrides = []): Page
    {
        $user = User::factory()->create();

        return $user->pages()->create(array_merge([
            'slug' => 'go-now',
            'model_name' => 'Karine',
            'page_type' => 'direct',
            'direct_url' => 'https://onlyfans.com/karine',
            'is_active' => true,
            'bot_protection' => false, // isolate the direct-link behaviour from cloaking
            'deep_link_enabled' => true,
        ], $overrides));
    }

    public function test_direct_link_serves_html_bounce_page_not_a_raw_redirect(): void
    {
        $page = $this->makeDirectPage();

        $response = $this->get('/api/p/'.$page->slug, ['User-Agent' => 'Mozilla/5.0 (iPhone)']);

        $response->assertOk(); // 200 HTML bounce, NOT a 302
        $this->assertStringContainsString('https://onlyfans.com/karine', $response->getContent());
        // It must not be served as an indexable page.
        $this->assertStringContainsString('noindex', strtolower($response->headers->get('X-Robots-Tag') ?? ''));
    }

    public function test_direct_link_is_not_a_302_status(): void
    {
        $page = $this->makeDirectPage();

        $response = $this->get('/api/p/'.$page->slug, ['User-Agent' => 'Mozilla/5.0 (Android)']);

        $this->assertNotEquals(302, $response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_bounce_page_carries_deeplink_flag_when_enabled(): void
    {
        $page = $this->makeDirectPage(['deep_link_enabled' => true]);

        $response = $this->get('/api/p/'.$page->slug, ['User-Agent' => 'Mozilla/5.0 (iPhone)']);
        $body = $response->getContent();

        // When deep-link bypass is on, the bounce page wires the in-app escape
        // (intent:// on Android / extbrowser on iOS). The destination is always present.
        $response->assertOk();
        $this->assertStringContainsString('https://onlyfans.com/karine', $body);
    }

    public function test_landing_page_is_not_treated_as_direct(): void
    {
        $user = User::factory()->create();
        $page = $user->pages()->create([
            'slug' => 'landing-one',
            'model_name' => 'Karine',
            'page_type' => 'landing',
            'is_active' => true,
            'bot_protection' => false,
        ]);

        // A landing page returns the JSON payload, never the bounce page.
        $this->getJson('/api/p/'.$page->slug, ['User-Agent' => 'Mozilla/5.0 (iPhone)'])
            ->assertOk()
            ->assertJsonPath('page_type', 'landing');
    }
}
