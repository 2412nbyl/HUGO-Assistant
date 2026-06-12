<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_avatar_upload_works(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'notaris']);

        // Generate a valid base64 data URL with binary size >= 100 bytes
        $binary = str_repeat('A', 150);
        $base64Image = 'data:image/png;base64,' . base64_encode($binary);

        $response = $this->actingAs($user)->postJson('/profile/avatar', [
            'image' => $base64Image,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $user->refresh();
        $this->assertNotNull($user->avatar_url);

        // Extract path and check if file exists on fake disk
        $path = parse_url($user->avatar_url, PHP_URL_PATH);
        $filename = basename($path);
        Storage::disk('public')->assertExists('avatars/' . $filename);
    }
}
