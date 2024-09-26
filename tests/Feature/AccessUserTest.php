<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizer_can_login_successfully()
    {
        User::factory()->create([
            'email' => 'organizer@example.com',
            'password' => bcrypt('password123'),
            'role' => 'ORGANIZER',
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/organizer/signin', [
            'email' => 'organizer@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Account signed in successfully as organizer!',
            ]);
    }

    public function test_participant_can_login_successfully()
    {
        $user = User::factory()->create([
            'email' => 'participant@example.com',
            'password' => bcrypt('password123'),
            'role' => 'PARTICIPANT',
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/participant/signin', [
            'email' => 'participant@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Account signed in successfully as participant!',
            ]);
    }

  
    public function test_unverified_email_returns_error()
    {
        $user = User::factory()->create([
            'email' => 'unverified@example.com',
            'password' => bcrypt('password123'),
            'role' => 'ORGANIZER',
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/organizer/signin', [
            'email' => 'unverified@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => false,
                'error' => 'Email not verified. Please verify email first!',
            ]);
    }

    public function test_incorrect_password_returns_error()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correctpassword'),
            'role' => 'ORGANIZER',
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/organizer/signin', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'The email or password you entered is incorrect!',
            ]);
    }

    public function test_incorrect_role_returns_error()
    {
        $user = User::factory()->create([
            'email' => 'participant@example.com',
            'password' => bcrypt('password123'),
            'role' => 'PARTICIPANT',
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/organizer/signin', [
            'email' => 'participant@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid Role for Organizer',
            ]);
    }
}