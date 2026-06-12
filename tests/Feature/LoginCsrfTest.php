<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginCsrfTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test that the login page loads with CSRF token fields.
     */
    public function test_login_page_renders_with_csrf()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('_token');
    }

    /**
     * Test login submission fails (throws 419) when CSRF token is missing.
     * We do this by forcing the route middleware to run.
     */
    public function test_login_post_fails_without_csrf()
    {
        $this->app->singleton(\App\Http\Middleware\VerifyCsrfToken::class, function ($app) {
            return new class($app, $app->make(\Illuminate\Contracts\Encryption\Encrypter::class)) extends \App\Http\Middleware\VerifyCsrfToken {
                protected function runningUnitTests() { return false; }
            };
        });

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'password',
        ]);
        
        $response->assertStatus(419);
    }
}
