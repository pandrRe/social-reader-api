<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_fails_if_credentials_dont_exist() {
        $response = $this->postJson('/login', ['name' => 'foo', 'password' => 'bar']);
        $response->assertStatus(400);
    }

    public function test_login_succeeds_if_credentials_match() {
        $user = User::factory()->create([
            'password' => Hash::make('testpassword')
        ]);
        $response = $this->postJson('/login', ['name' => $user->name, 'password' => 'testpassword']);
        $response->assertOk();
        $response->assertCookie('social_reader_api_session');
    }

    public function test_register_a_user_and_login_with_it() {
        $loginResponse = $this->postJson('/login', ['name' => 'testuser', 'password' => 'testpassword']);
        $loginResponse->assertStatus(400);

        $response = $this->postJson('/api/register', [
            'name' => 'testuser',
            'password' => 'testpassword',
            'password_confirmation' => 'testpassword',
            'email' => 'testemail@example.com'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'name' => 'testuser',
            'email' => 'testemail@example.com'
        ]);

        $loginResponse = $this->postJson('/login', ['name' => 'testuser', 'password' => 'testpassword']);
        $loginResponse->assertOk();
    }

    public function test_cant_create_user_with_repeated_name() {
        $user = User::factory()->create();
        $response = $this->postJson('/api/register', [
            'name' => $user->name,
            'password' => 'random_password_xyz',
            'password_confirmation' => 'random_password_xyz',
            'email' => 'also_random_email_xyz@example.xyz'
        ]);
        $response
            ->assertStatus(422)
            ->assertJson(['message' => 'The name has already been taken.']);
    }

    /**
     * Tests that the /api/user is blocked for non-authorized requesters.
     *
     * @return void
     */
    public function test_the_api_user_endpoint_has_protection() {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    public function test_get_user_data_from_user_api_endpoint() {
        $user = User::factory()->create();
        $user = User::find($user->id); // need to do this to load uuid.
        $response = $this->actingAs($user)->getJson('/api/user');
        $response
            ->assertOk()
            ->assertJson([
                'name' => $user->name,
                'uuid' => $user->uuid,
            ]);
    }
}
