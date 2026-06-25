<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\UserController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Profile / password / account-deletion endpoints. Password change and account
 * deletion are both gated on re-entering the current password, so the negative
 * cases (wrong password ⇒ 422, nothing happens) matter as much as the happy path.
 *
 * @see UserController
 */
class AccountManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_their_profile(): void
    {
        $user = User::factory()->create(['name' => 'Old', 'timezone' => 'UTC']);
        Sanctum::actingAs($user);

        $this->patchJson('/api/user', [
            'name'     => 'New Name',
            'timezone' => 'Europe/Paris',
        ])->assertOk();

        $this->assertDatabaseHas('users', [
            'id'       => $user->id,
            'name'     => 'New Name',
            'timezone' => 'Europe/Paris',
        ]);
    }

    public function test_email_must_be_unique_when_updating(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);
        Sanctum::actingAs(User::factory()->create());

        $this->patchJson('/api/user', ['email' => 'taken@example.com'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_keeping_your_own_email_is_allowed(): void
    {
        $user = User::factory()->create(['email' => 'me@example.com']);
        Sanctum::actingAs($user);

        // Unique rule ignores the current user's own row.
        $this->patchJson('/api/user', ['email' => 'me@example.com', 'name' => 'Same Email'])
            ->assertOk();
    }

    public function test_invalid_timezone_is_rejected(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->patchJson('/api/user', ['timezone' => 'Mars/Olympus'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('timezone');
    }

    public function test_password_change_requires_the_correct_current_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('original-password')]);
        Sanctum::actingAs($user);

        $this->patchJson('/api/user/password', [
            'current_password'      => 'wrong-password',
            'password'              => 'brand-new-password',
            'password_confirmation' => 'brand-new-password',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('current_password');

        // Password must be untouched.
        $this->assertTrue(Hash::check('original-password', $user->fresh()->password));
    }

    public function test_password_change_succeeds_with_the_correct_current_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('original-password')]);
        Sanctum::actingAs($user);

        $this->patchJson('/api/user/password', [
            'current_password'      => 'original-password',
            'password'              => 'brand-new-password',
            'password_confirmation' => 'brand-new-password',
        ])->assertOk();

        $this->assertTrue(Hash::check('brand-new-password', $user->fresh()->password));
    }

    public function test_account_deletion_requires_the_correct_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('my-password')]);
        Sanctum::actingAs($user);

        $this->deleteJson('/api/user', ['password' => 'not-it'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_account_is_deleted_with_the_correct_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('my-password')]);
        Sanctum::actingAs($user);

        $this->deleteJson('/api/user', ['password' => 'my-password'])
            ->assertOk();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_profile_endpoints_require_authentication(): void
    {
        $this->patchJson('/api/user', ['name' => 'X'])->assertUnauthorized();
        $this->patchJson('/api/user/password', [])->assertUnauthorized();
        $this->deleteJson('/api/user', [])->assertUnauthorized();
    }
}
