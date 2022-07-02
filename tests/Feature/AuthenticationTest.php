<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_fails_if_credentials_dont_exist() {
        $response = $this->postJson('/api/login', ['name' => 'foo', 'password' => 'bar']);
        $response->assertStatus(400);
    }

    /**
     * Tests that the /api/user is blocked for non-authorized requesters.
     *
     * @return void
     */
    public function test_the_api_user_endpoint_has_protection()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }
}
