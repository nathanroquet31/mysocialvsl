<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\NotificationController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * In-app notification bell endpoints: listing + unread count, marking read
 * (one / all), and the plan-upgrade popup probe that drives the celebratory
 * modal.
 *
 * @see NotificationController
 */
class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    private function notify(User $user, array $data, bool $read = false): void
    {
        $user->notifications()->create([
            'id'      => (string) Str::uuid(),
            'type'    => 'App\\Notifications\\Generic',
            'data'    => $data,
            'read_at' => $read ? now() : null,
        ]);
    }

    public function test_index_returns_mapped_notifications_and_unread_count(): void
    {
        $user = User::factory()->create();
        $this->notify($user, ['category' => 'billing', 'title' => 'Plan changed', 'description' => 'Now Pro']);
        $this->notify($user, ['category' => 'security', 'title' => 'New login'], read: true);
        Sanctum::actingAs($user);

        $this->getJson('/api/notifications')
            ->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonCount(2, 'notifications')
            ->assertJsonFragment(['title' => 'Plan changed', 'category' => 'billing']);
    }

    public function test_unread_count_is_cheap_and_accurate(): void
    {
        $user = User::factory()->create();
        $this->notify($user, ['category' => 'account', 'title' => 'A']);
        $this->notify($user, ['category' => 'account', 'title' => 'B']);
        Sanctum::actingAs($user);

        $this->getJson('/api/notifications/unread-count')
            ->assertOk()
            ->assertJsonPath('unread_count', 2);
    }

    public function test_read_all_clears_the_badge(): void
    {
        $user = User::factory()->create();
        $this->notify($user, ['category' => 'account', 'title' => 'A']);
        $this->notify($user, ['category' => 'account', 'title' => 'B']);
        Sanctum::actingAs($user);

        $this->postJson('/api/notifications/read-all')->assertOk();

        $this->assertSame(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_marking_a_single_notification_read(): void
    {
        $user = User::factory()->create();
        $this->notify($user, ['category' => 'account', 'title' => 'A']);
        $id = $user->notifications()->first()->id;
        Sanctum::actingAs($user);

        $this->patchJson("/api/notifications/{$id}/read")->assertOk();

        $this->assertNotNull($user->notifications()->first()->read_at);
        $this->assertSame(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_marking_another_users_notification_is_not_found(): void
    {
        $owner = User::factory()->create();
        $this->notify($owner, ['category' => 'account', 'title' => 'Theirs']);
        $foreignId = $owner->notifications()->first()->id;

        Sanctum::actingAs(User::factory()->create());
        $this->patchJson("/api/notifications/{$foreignId}/read")->assertNotFound();
    }

    public function test_plan_upgrade_probe_surfaces_an_unread_upgrade(): void
    {
        $user = User::factory()->create();
        $this->notify($user, [
            'category' => 'billing',
            'title'    => 'Welcome to Pro',
            'meta'     => ['context' => 'upgraded', 'plan' => 'pro'],
        ]);
        Sanctum::actingAs($user);

        $this->getJson('/api/notifications/plan-upgrade')
            ->assertOk()
            ->assertJsonPath('plan', 'pro');
    }

    public function test_plan_upgrade_probe_is_null_without_an_upgrade(): void
    {
        $user = User::factory()->create();
        $this->notify($user, ['category' => 'security', 'title' => 'New login']);
        Sanctum::actingAs($user);

        $this->getJson('/api/notifications/plan-upgrade')
            ->assertOk()
            ->assertJsonPath('id', null);
    }

    public function test_notification_endpoints_require_authentication(): void
    {
        $this->getJson('/api/notifications')->assertUnauthorized();
        $this->getJson('/api/notifications/unread-count')->assertUnauthorized();
        $this->postJson('/api/notifications/read-all')->assertUnauthorized();
    }
}
