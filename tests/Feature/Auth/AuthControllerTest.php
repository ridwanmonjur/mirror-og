<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{User, Organizer, Participant};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\{Mail, Session, Hash};
use App\Mail\VerifyUserMail;
use Laravel\Socialite\Facades\Socialite;
use Mockery;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /** @test */
    public function it_redirects_to_google_for_organizer()
    {
        $socialiteMock = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $socialiteMock->shouldReceive('redirect')->once()->andReturn(redirect('https://accounts.google.com'));

        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($socialiteMock);

        $response = $this->get('/organizer/auth/google');

        $this->assertEquals('ORGANIZER', Session::get('role'));
        $this->assertEquals(302, $response->getStatusCode());
    }

    /** @test */
    public function it_redirects_to_google_for_participant()
    {
        $socialiteMock = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $socialiteMock->shouldReceive('redirect')->once()->andReturn(redirect('https://accounts.google.com'));

        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($socialiteMock);

        $response = $this->get('/participant/auth/google');

        $this->assertEquals('PARTICIPANT', Session::get('role'));
        $this->assertEquals(302, $response->getStatusCode());
    }

    /** @test */
    public function it_registers_new_participant()
    {
        $data = [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'confirmPassword' => 'password123',
        ];

        $response = $this->post('/participant/signup', $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $response->assertSessionHas('verify', true);

        $this->assertDatabaseHas('users', [
            'name' => 'newuser',
            'email' => 'newuser@example.com',
            'role' => 'PARTICIPANT',
        ]);

        $user = User::where('email', 'newuser@example.com')->first();
        $this->assertDatabaseHas('participants', ['user_id' => $user->id]);
        $this->assertDatabaseHas('notification_counters', ['user_id' => $user->id]);

        Mail::assertQueued(VerifyUserMail::class);
    }

    /** @test */
    public function it_registers_new_organizer()
    {
        $data = [
            'username' => 'neworg',
            'email' => 'neworg@example.com',
            'password' => 'password123',
            'confirmPassword' => 'password123',
            'companyName' => 'Test Company',
            'companyDescription' => 'A test company',
        ];

        $response = $this->post('/organizer/signup', $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'neworg',
            'email' => 'neworg@example.com',
            'role' => 'ORGANIZER',
        ]);

        $user = User::where('email', 'neworg@example.com')->first();
        $this->assertDatabaseHas('organizers', [
            'user_id' => $user->id,
            'companyName' => 'Test Company',
            'companyDescription' => 'A test company',
        ]);

        Mail::assertQueued(VerifyUserMail::class);
    }

    /** @test */
    public function it_validates_required_fields_on_registration()
    {
        $response = $this->post('/participant/signup', []);

        $response->assertSessionHasErrors(['username', 'email', 'password', 'confirmPassword']);
    }

    /** @test */
    public function it_validates_password_confirmation()
    {
        $data = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123',
            'confirmPassword' => 'different',
        ];

        $response = $this->post('/participant/signup', $data);

        $response->assertSessionHasErrors('confirmPassword');
    }

    /** @test */
    public function it_validates_minimum_password_length()
    {
        $data = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => '12345',
            'confirmPassword' => '12345',
        ];

        $response = $this->post('/participant/signup', $data);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function it_validates_unique_username()
    {
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        // The validation rule checks if username value exists in users.email column
        $data = [
            'username' => 'existing@example.com', // This will fail unique check
            'email' => 'newemail@example.com',
            'password' => 'password123',
            'confirmPassword' => 'password123',
        ];

        $response = $this->post('/participant/signup', $data);

        $response->assertSessionHasErrors('username');
    }

    /** @test */
    public function it_validates_email_format()
    {
        $data = [
            'username' => 'testuser',
            'email' => 'not-an-email',
            'password' => 'password123',
            'confirmPassword' => 'password123',
        ];

        $response = $this->post('/participant/signup', $data);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function it_logs_in_verified_user()
    {
        $user = User::factory()->create([
            'role' => 'PARTICIPANT',
            'password' => 'password123', // Will be auto-hashed by 'hashed' cast
            'email_verified_at' => now(),
        ]);
        Participant::factory()->create(['user_id' => $user->id]);

        $response = $this->post('/participant/signin', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_rejects_unverified_user_login()
    {
        $user = User::factory()->create([
            'role' => 'PARTICIPANT',
            'password' => 'password123', // Will be auto-hashed by 'hashed' cast
            'email_verified_at' => null,
        ]);
        Participant::factory()->create(['user_id' => $user->id]);

        $response = $this->post('/participant/signin', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'verify' => true,
        ]);
        $this->assertGuest();
    }

    /** @test */
    public function it_rejects_invalid_credentials()
    {
        $user = User::factory()->create([
            'role' => 'PARTICIPANT',
            'password' => 'password123', // Will be auto-hashed by 'hashed' cast
            'email_verified_at' => now(),
        ]);
        Participant::factory()->create(['user_id' => $user->id]);

        $response = $this->post('/participant/signin', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertGuest();
    }

    /** @test */
    public function it_prevents_wrong_role_login()
    {
        $user = User::factory()->create([
            'role' => 'ORGANIZER',
            'password' => 'password123', // Will be auto-hashed by 'hashed' cast
            'email_verified_at' => now(),
        ]);
        Organizer::factory()->create(['user_id' => $user->id]);

        // Try to login as participant
        $response = $this->post('/participant/signin', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Invalid Role for Participant',
        ]);
    }

    /** @test */
    public function it_logs_out_authenticated_user()
    {
        $user = $this->createParticipant();

        $this->actingAs($user);
        $this->assertAuthenticated();

        $response = $this->get('/logout');

        $response->assertRedirect();
        $this->assertGuest();
    }

    /** @test */
    public function organizer_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'role' => 'ORGANIZER',
            'password' => 'password123', // Will be auto-hashed by 'hashed' cast
            'email_verified_at' => now(),
        ]);
        Organizer::factory()->create(['user_id' => $user->id]);

        $response = $this->post('/organizer/signin', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_sends_verification_email_on_registration()
    {
        $data = [
            'username' => 'emailtest',
            'email' => 'emailtest@example.com',
            'password' => 'password123',
            'confirmPassword' => 'password123',
        ];

        $this->post('/participant/signup', $data);

        Mail::assertQueued(VerifyUserMail::class, function ($mail) {
            return $mail->hasTo('emailtest@example.com');
        });
    }

    /** @test */
    public function it_rolls_back_on_registration_error()
    {
        // Create a user with a conflicting email
        User::factory()->create(['email' => 'conflict@example.com']);

        $data = [
            'username' => 'newuser',
            'email' => 'conflict@example.com', // Duplicate email will cause constraint violation
            'password' => 'password123',
            'confirmPassword' => 'password123',
        ];

        $response = $this->post('/participant/signup', $data);

        // Should redirect back with error
        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Should not create participant or notification counter
        $this->assertDatabaseMissing('users', ['name' => 'newuser']);
    }

    /** @test */
    public function it_generates_verification_token_on_registration()
    {
        $data = [
            'username' => 'tokentest',
            'email' => 'tokentest@example.com',
            'password' => 'password123',
            'confirmPassword' => 'password123',
        ];

        $this->post('/participant/signup', $data);

        $user = User::where('email', 'tokentest@example.com')->first();

        $this->assertNotNull($user->email_verified_token);
        $this->assertIsString($user->email_verified_token);
    }
}
