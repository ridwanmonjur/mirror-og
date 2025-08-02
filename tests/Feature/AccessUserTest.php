<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AccessUserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

    }

    public function test_organizer_can_login_successfully()
    {
        User::factory()->create([
            'email' => 'organizer@driftwood.gg',
            'password' => bcrypt('password123'),
            'role' => 'ORGANIZER',
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/organizer/signin', [
            'email' => 'organizer@driftwood.gg',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Account signed in successfully as organizer!',
            ]);
    }

    public function test_user_cannot_login_with_incorrect_credentials()
    {
        User::createOrFirst([
            'email' => 'test2@driftwood.gg',
            'password' => bcrypt('password123'),
            'role' => 'ORGANIZER',
        ]);

        User::createOrFirst([
            'email' => 'test3@driftwood.gg',
            'password' => bcrypt('password123'),
            'role' => 'PARTICIPANT',
        ]);

        $response = $this->post('/participant/signin', [
            'email' => 'test2@driftwood.gg',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422);
        $this->assertGuest();

        $response = $this->post('/organizer/signin', [
            'email' => 'test3@driftwood.gg',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422);
        $this->assertGuest();

    }

    public function test_participant_can_login_successfully()
    {
        $user = User::factory()->create([
            'email' => 'participant@driftwood.gg',
            'password' => bcrypt('password123'),
            'role' => 'PARTICIPANT',
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/participant/signin', [
            'email' => 'participant@driftwood.gg',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Account signed in successfully as participant!',
            ]);
    }

    public function test_unverified_email_returns_error()
    {
        $user = User::factory()->create([
            'email' => 'unverified@driftwood.gg',
            'password' => bcrypt('password123'),
            'role' => 'ORGANIZER',
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/organizer/signin', [
            'email' => 'unverified@driftwood.gg',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => false,
                'message' => 'Email not verified. Please verify email first!',
            ]);
    }

    public function test_incorrect_password_returns_error()
    {
        $user = User::factory()->create([
            'email' => 'test@driftwood.gg',
            'password' => bcrypt('correctpassword'),
            'role' => 'ORGANIZER',
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/organizer/signin', [
            'email' => 'test@driftwood.gg',
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
            'email' => 'participant2@driftwood.gg',
            'password' => bcrypt('password123'),
            'role' => 'PARTICIPANT',
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/organizer/signin', [
            'email' => 'participant2@driftwood.gg',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid Role for Organizer',
            ]);
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::createOrFirst([
            'email' => 'test4@driftwood.gg',
            'password' => bcrypt('password123'),
            'role' => 'ORGANIZER',
        ]);

        $response = $this->post('/organizer/signin', [
            'email' => 'test4@driftwood.gg',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message']);
        $this->get('/logout');
        $this->assertGuest();

        $user = User::createOrFirst([
            'email' => 'test5@driftwood.gg',
            'password' => bcrypt('password123'),
            'role' => 'PARTICIPANT',
        ]);

        $response = $this->post('/participant/signin', [
            'email' => 'test5@driftwood.gg',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message']);
        $this->get('/logout');
        $this->assertGuest();
    }
}
