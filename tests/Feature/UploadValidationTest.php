<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\UploadController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * The VSL video upload is the single most important guard-rail of the product:
 * a .mov (iPhone HEVC) uploads fine but renders as a black box in
 * Chrome/Firefox/Android, silently killing the fan's conversion. The endpoint
 * must therefore accept only browser-safe MP4/WebM and reject everything else.
 *
 * @see UploadController::video
 * @see UploadController::image
 */
class UploadValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // No R2 configured in tests → the controller falls back to the public disk.
        Storage::fake('public');
    }

    public function test_video_upload_accepts_mp4(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/upload/video', [
            'file' => UploadedFile::fake()->create('vsl.mp4', 2048, 'video/mp4'),
        ])
            ->assertOk()
            ->assertJsonStructure(['url', 'filename', 'size']);
    }

    public function test_video_upload_accepts_webm(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/upload/video', [
            'file' => UploadedFile::fake()->create('vsl.webm', 2048, 'video/webm'),
        ])->assertOk();
    }

    public function test_video_upload_rejects_quicktime_mov(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/upload/video', [
            'file' => UploadedFile::fake()->create('iphone.mov', 2048, 'video/quicktime'),
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('file');
    }

    public function test_video_upload_rejects_oversized_file(): void
    {
        Sanctum::actingAs(User::factory()->create());

        // Limit is 100MB (102400 KB); 110MB must be refused.
        $this->postJson('/api/upload/video', [
            'file' => UploadedFile::fake()->create('huge.mp4', 110 * 1024, 'video/mp4'),
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('file');
    }

    public function test_video_upload_requires_authentication(): void
    {
        $this->postJson('/api/upload/video', [
            'file' => UploadedFile::fake()->create('vsl.mp4', 1024, 'video/mp4'),
        ])->assertUnauthorized();
    }

    public function test_image_upload_accepts_a_real_image_and_requires_a_type(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/upload/image', [
            'file' => UploadedFile::fake()->image('avatar.jpg'),
            'type' => 'avatar',
        ])
            ->assertOk()
            ->assertJsonStructure(['url']);
    }

    public function test_image_upload_rejects_a_non_image(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/upload/image', [
            'file' => UploadedFile::fake()->create('document.pdf', 200, 'application/pdf'),
            'type' => 'avatar',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('file');
    }

    public function test_image_upload_rejects_an_invalid_type(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/upload/image', [
            'file' => UploadedFile::fake()->image('avatar.jpg'),
            'type' => 'banner', // only avatar|background are allowed
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('type');
    }
}
