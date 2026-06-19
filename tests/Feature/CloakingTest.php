<?php

namespace Tests\Feature;

use App\Http\Controllers\Concerns\ResolvesPublicPages;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Stevebauman\Location\Facades\Location;
use Tests\TestCase;

/**
 * Shield Protection™ — the product's moat. Bots must get a generic decoy page
 * served server-side (before any JS), while real humans (including the
 * Instagram in-app browser) get the real page payload.
 *
 * @see ResolvesPublicPages::publicPageGuards
 */
class CloakingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Geo lookup is best-effort and would otherwise hit the network; neutralise it.
        // Location::get() is typed Position|bool, so a "no result" is false (not null).
        Location::shouldReceive('get')->andReturn(false);
    }

    private function makePage(array $overrides = []): Page
    {
        $user = User::factory()->create();

        return $user->pages()->create(array_merge([
            'slug' => 'karine-vsl',
            'model_name' => 'Karine',
            'page_type' => 'landing',
            'is_active' => true,
            'bot_protection' => true,
        ], $overrides));
    }

    public static function botUserAgents(): array
    {
        return [
            'Meta facebookexternalhit' => ['facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)'],
            'Meta externalagent' => ['meta-externalagent/1.1'],
            'Google' => ['Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'],
            'Apple' => ['Mozilla/5.0 (compatible; Applebot/0.1; +http://www.apple.com/go/applebot)'],
            'Twitter' => ['Twitterbot/1.0'],
            'Telegram' => ['TelegramBot (like TwitterBot)'],
            'Generic bot' => ['SomeRandomCrawlerBot/2.0'],
            'DuckDuckGo' => ['DuckDuckBot/1.1; (+http://duckduckgo.com/duckduckbot.html)'],
            'Baidu' => ['Mozilla/5.0 (compatible; BaiduSpider/2.0; +http://www.baidu.com/search/spider.html)'],
            'Petal' => ['Mozilla/5.0 (compatible; PetalBot;+https://webmaster.petalsearch.com/site/petalbot)'],
            'Ahrefs' => ['Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)'],
            'Semrush' => ['Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)'],
        ];
    }

    #[DataProvider('botUserAgents')]
    public function test_bots_receive_the_decoy_not_the_real_page(string $ua): void
    {
        $page = $this->makePage();

        $response = $this->get('/api/p/'.$page->slug, ['User-Agent' => $ua]);

        $response->assertOk();
        $response->assertSee('Check out my latest content', false);
        // The decoy must NOT leak the real model identity.
        $response->assertDontSee('Karine');
        $this->assertStringContainsString('text/html', $response->headers->get('Content-Type'));
    }

    public function test_a_blocked_bot_is_recorded_as_analytics_event(): void
    {
        $page = $this->makePage();

        $this->get('/api/p/'.$page->slug, ['User-Agent' => 'Googlebot/2.1']);

        $this->assertDatabaseHas('analytics_events', [
            'page_id' => $page->id,
            'type' => 'bot_blocked',
        ]);
    }

    public function test_real_human_gets_the_real_page_json(): void
    {
        $page = $this->makePage();

        $ua = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 Safari/604.1';
        $response = $this->getJson('/api/p/'.$page->slug, ['User-Agent' => $ua]);

        $response->assertOk();
        $response->assertJsonPath('slug', $page->slug);
        $response->assertJsonPath('model_name', 'Karine');
    }

    public function test_instagram_in_app_browser_is_treated_as_human_not_a_bot(): void
    {
        $page = $this->makePage();

        // Real Instagram in-app webview UA — contains "Instagram" but is a genuine visitor.
        $ua = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/21E236 Instagram 329.0.0.41.96';
        $response = $this->getJson('/api/p/'.$page->slug, ['User-Agent' => $ua]);

        $response->assertOk();
        $response->assertJsonPath('slug', $page->slug);
        $this->assertDatabaseMissing('analytics_events', [
            'page_id' => $page->id,
            'type' => 'bot_blocked',
        ]);
    }

    public function test_cloaking_is_skipped_when_bot_protection_is_off(): void
    {
        $page = $this->makePage(['bot_protection' => false]);

        $response = $this->getJson('/api/p/'.$page->slug, ['User-Agent' => 'Googlebot/2.1']);

        // No protection → even a bot gets the real payload.
        $response->assertOk();
        $response->assertJsonPath('slug', $page->slug);
    }

    public function test_web_route_serves_decoy_to_bots_and_shell_to_humans(): void
    {
        $page = $this->makePage();

        // The bot decoy is a minimal page with no SPA mount point.
        $bot = $this->get('/p/'.$page->slug, ['User-Agent' => 'facebookexternalhit/1.1']);
        $bot->assertOk()->assertSee('Check out my latest content', false);
        $this->assertStringNotContainsString('id="app"', $bot->getContent());

        // A human gets the real SPA shell, which boots on this slug.
        $human = $this->get('/p/'.$page->slug, ['User-Agent' => 'Mozilla/5.0 (Macintosh)']);
        $human->assertOk();
        $this->assertStringContainsString('id="app"', $human->getContent());
        $this->assertStringContainsString($page->slug, $human->getContent());
    }
}
