<?php

namespace Tests\Unit\Authentication;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Services\AuthService;
use App\Models\{User, Organizer, Participant, NotificationCounter};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};
use Mockery;

class AuthServiceTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    private AuthService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AuthService();
    }

    /** @test */
    public function it_creates_participant_user()
    {
        $data = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ];

        $user = $this->service->createUser($data, 'PARTICIPANT');

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('testuser', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('PARTICIPANT', $user->role);
        $this->assertNotNull($user->email_verified_token);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $this->assertDatabaseHas('notification_counters', ['user_id' => $user->id]);
    }

    /** @test */
    public function it_creates_organizer_user()
    {
        $data = [
            'username' => 'organizer',
            'email' => 'organizer@example.com',
            'password' => bcrypt('password123'),
        ];

        $user = $this->service->createUser($data, 'ORGANIZER');

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('ORGANIZER', $user->role);
        $this->assertDatabaseHas('users', ['email' => 'organizer@example.com', 'role' => 'ORGANIZER']);
    }

    /** @test */
    public function it_generates_email_verification_token_on_create()
    {
        $data = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ];

        $user = $this->service->createUser($data, 'PARTICIPANT');

        $this->assertNotNull($user->email_verified_token);
        $this->assertIsString($user->email_verified_token);
    }

    /** @test */
    public function it_determines_organizer_role_from_request()
    {
        $request = Request::create('/organizer/register', 'GET');

        $result = $this->service->determineUserRole($request);

        $this->assertIsArray($result);
        $this->assertEquals('organizer', $result['role']);
        $this->assertEquals('ORGANIZER', $result['roleCapital']);
        $this->assertEquals('Organizer', $result['roleFirstCapital']);
    }

    /** @test */
    public function it_determines_participant_role_from_request()
    {
        $request = Request::create('/participant/register', 'GET');

        $result = $this->service->determineUserRole($request);

        $this->assertIsArray($result);
        $this->assertEquals('participant', $result['role']);
        $this->assertEquals('PARTICIPANT', $result['roleCapital']);
        $this->assertEquals('Participant', $result['roleFirstCapital']);
    }

    /** @test */
    public function it_throws_exception_for_invalid_registration_path()
    {
        $request = Request::create('/invalid/register', 'GET');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid registration path');

        $this->service->determineUserRole($request);
    }

    /** @test */
    public function it_determines_organizer_role_from_url()
    {
        $role = $this->service->putRoleInSessionBasedOnRoute('/organizer/home');

        $this->assertEquals('ORGANIZER', $role);
    }

    /** @test */
    public function it_determines_participant_role_from_url()
    {
        $role = $this->service->putRoleInSessionBasedOnRoute('/participant/events');

        $this->assertEquals('PARTICIPANT', $role);
    }

    /** @test */
    public function it_determines_admin_role_from_other_url()
    {
        $role = $this->service->putRoleInSessionBasedOnRoute('/admin/dashboard');

        $this->assertEquals('ADMIN', $role);
    }

    /** @test */
    public function it_handles_redirection_with_error()
    {
        $response = $this->service->handleUserRedirection(null, 'Invalid credentials', 'ORGANIZER');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect());
    }

    /** @test */
    public function it_redirects_participant_to_home()
    {
        $user = User::factory()->create(['role' => 'PARTICIPANT']);

        $response = $this->service->handleUserRedirection($user, null, 'PARTICIPANT');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect(route('participant.home.view')));
    }

    /** @test */
    public function it_redirects_organizer_to_home()
    {
        $user = User::factory()->create(['role' => 'ORGANIZER']);

        $response = $this->service->handleUserRedirection($user, null, 'ORGANIZER');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect(route('organizer.home.view')));
    }

    /** @test */
    public function it_logs_in_existing_google_user()
    {
        $user = User::factory()->create([
            'role' => 'PARTICIPANT',
            'google_id' => '12345',
        ]);

        $googleUser = (object) [
            'id' => '12345',
            'email' => $user->email,
            'name' => 'Test User',
        ];

        $result = $this->service->registerOrLoginUserForSocialAuth($googleUser, 'google', 'PARTICIPANT');

        $this->assertNotNull($result['finduser']);
        $this->assertNull($result['error']);
        $this->assertEquals($user->id, $result['finduser']->id);
    }

    /** @test */
    public function it_returns_error_for_wrong_role_social_auth()
    {
        $user = User::factory()->create([
            'role' => 'PARTICIPANT',
            'google_id' => '12345',
        ]);

        $googleUser = (object) [
            'id' => '12345',
            'email' => $user->email,
            'name' => 'Test User',
        ];

        $result = $this->service->registerOrLoginUserForSocialAuth($googleUser, 'google', 'ORGANIZER');

        $this->assertNull($result['finduser']);
        $this->assertNotNull($result['error']);
        $this->assertStringContainsString('Only organizers can sign in', $result['error']);
    }

    /** @test */
    public function it_links_google_id_to_existing_email_user()
    {
        $user = User::factory()->create([
            'role' => 'PARTICIPANT',
            'email' => 'existing@example.com',
            'google_id' => null,
        ]);

        $googleUser = (object) [
            'id' => 'new-google-id',
            'email' => 'existing@example.com',
            'name' => 'Test User',
        ];

        $result = $this->service->registerOrLoginUserForSocialAuth($googleUser, 'google', 'PARTICIPANT');

        $this->assertNotNull($result['finduser']);
        $this->assertNull($result['error']);

        $user->refresh();
        $this->assertEquals('new-google-id', $user->google_id);
        $this->assertNotNull($user->email_verified_at);
    }

    /** @test */
    public function it_creates_new_participant_for_google_auth()
    {
        $googleUser = (object) [
            'id' => 'google-123',
            'email' => 'newuser@example.com',
            'name' => 'New User',
        ];

        $result = $this->service->registerOrLoginUserForSocialAuth($googleUser, 'google', 'PARTICIPANT');

        $this->assertNotNull($result['finduser']);
        $this->assertNull($result['error']);
        $this->assertEquals('PARTICIPANT', $result['finduser']->role);
        $this->assertEquals('google-123', $result['finduser']->google_id);
        $this->assertNotNull($result['finduser']->email_verified_at);
        $this->assertStringContainsString('New User', $result['finduser']->name);

        // Check participant record created
        $this->assertDatabaseHas('participants', ['user_id' => $result['finduser']->id]);
        $this->assertDatabaseHas('notification_counters', ['user_id' => $result['finduser']->id]);
    }

    /** @test */
    public function it_creates_new_organizer_for_google_auth()
    {
        $googleUser = (object) [
            'id' => 'google-456',
            'email' => 'neworg@example.com',
            'name' => 'New Organizer',
        ];

        $result = $this->service->registerOrLoginUserForSocialAuth($googleUser, 'google', 'ORGANIZER');

        $this->assertNotNull($result['finduser']);
        $this->assertNull($result['error']);
        $this->assertEquals('ORGANIZER', $result['finduser']->role);
        $this->assertEquals('google-456', $result['finduser']->google_id);

        // Check organizer record created
        $this->assertDatabaseHas('organizers', ['user_id' => $result['finduser']->id]);
    }

    /** @test */
    public function it_handles_steam_social_auth()
    {
        $steamUser = (object) [
            'id' => 'steam-789',
            'email' => 'steam@example.com',
            'name' => 'Steam User',
        ];

        $result = $this->service->registerOrLoginUserForSocialAuth($steamUser, 'steam', 'PARTICIPANT');

        $this->assertNotNull($result['finduser']);
        $this->assertNull($result['error']);
        $this->assertEquals('steam-789', $result['finduser']->steam_id);
    }

    /** @test */
    public function it_links_steam_id_to_existing_user()
    {
        $user = User::factory()->create([
            'role' => 'PARTICIPANT',
            'email' => 'steamlink@example.com',
            'steam_id' => null,
        ]);

        $steamUser = (object) [
            'id' => 'steam-new',
            'email' => 'steamlink@example.com',
            'name' => 'Steam User',
        ];

        $result = $this->service->registerOrLoginUserForSocialAuth($steamUser, 'steam', 'PARTICIPANT');

        $user->refresh();
        $this->assertEquals('steam-new', $user->steam_id);
    }

    /** @test */
    public function it_generates_unique_username_with_timestamp()
    {
        $googleUser = (object) [
            'id' => 'google-unique',
            'email' => 'unique@example.com',
            'name' => 'UniqueUser',
        ];

        $result = $this->service->registerOrLoginUserForSocialAuth($googleUser, 'google', 'PARTICIPANT');

        // Name should include original name plus timestamp
        $this->assertStringContainsString('UniqueUser_', $result['finduser']->name);
        $this->assertMatchesRegularExpression('/UniqueUser_\d+/', $result['finduser']->name);
    }

    /** @test */
    public function it_rolls_back_on_social_auth_error()
    {
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollBack')->once();
        DB::shouldReceive('raw')->andReturn(now());

        // Force an exception by using invalid data
        $googleUser = (object) [
            'id' => 'google-error',
            'email' => null, // This should cause an error
            'name' => 'Error User',
        ];

        $this->expectException(\Exception::class);

        $this->service->registerOrLoginUserForSocialAuth($googleUser, 'google', 'PARTICIPANT');
    }
}
