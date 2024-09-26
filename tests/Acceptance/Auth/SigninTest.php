<?php
namespace Tests\Acceptance\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SigninTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_login_form()
    {
        $response = $this->get('/participant/signin');

        $response->assertStatus(200);
        $response->assertViewIs('Auth.ParticipantSignIn');
        $response = $this->get('/organizer/signin');
        $response->assertStatus(200);
        $response->assertViewIs('Auth.OrganizerSignIn');
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::createOrFirst([
            'email' => 'test2@example.com',
            'password' => bcrypt('password123'),
            'role' => 'ORGANIZER'
        ]);

        $this->post('/organizer/signin', [
            'email' => 'test2@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $this->get('/logout');
        $this->assertGuest();

        $user = User::createOrFirst([
            'email' => 'test3@example.com',
            'password' => bcrypt('password123'),
            'role' => 'PARTICIPANT'
        ]);

        $this->post('/participant/signin', [
            'email' => 'test3@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $this->get('/logout');
        $this->assertGuest();
    }

    public function test_user_cannot_login_with_incorrect_credentials()
    {
        User::createOrFirst([
            'email' => 'test2@example.com',
            'password' => bcrypt('password123'),
            'role' => 'ORGANIZER'
        ]);

        User::createOrFirst([
            'email' => 'test3@example.com',
            'password' => bcrypt('password123'),
            'role' => 'PARTICIPANT'
        ]);

        $response = $this->post('/participant/signin', [
            'email' => 'test2@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422);
        $this->assertGuest();

        $response = $this->post('/organizer/signin', [
            'email' => 'test3@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422);
        $this->assertGuest();

    }

    public function test_user_can_view_registration_form()
    {
        $response = $this->get('/participant/signup');

        $response->assertStatus(200);
        $response->assertViewIs('Auth.ParticipantSignUp');

        $response = $this->get('/organizer/signup');

        $response->assertStatus(200);
        $response->assertViewIs('Auth.OrganizerSignUp');
    }

    // public function test_user_can_register_with_valid_data()
    // {
    //     $userPost = [
    //         'username' => 'Test Signup Participant',
    //         'email' => 'Participant@example.com',
    //         'password' => 'password123',
    //         'confirmPassword' => "password123"
    //     ];
        
    //     $headers = [
    //         'Accept' => 'application/json',
    //     ];

    //     $response = $this->post('/participant/signup', $userPost, [
    //         'headers' => $headers
    //     ]);
    
    //     $response->assertSessionHas('success', 'Participant account created and verification email sent. Please verify email now!');
    //     $response->assertSessionHas('email', $userPost['email']);
        
    //     $this->assertDatabaseHas('users', [
    //         'email' => $userPost['email']
    //     ]);


    //     // $this->assertDatabaseHas('participants', [
    //     //     'user_id' => $user->get
    //     // ]);

    //     $userPost = [
    //         'username' => 'Test Signup Organizer',
    //         'email' => 'organizer@example.com',
    //         'password' => 'password123',
    //         'companyDescription' => 'zzzzzzzzzzzzzzzzzzzzzzzzzzz',
    //         'companyName' => 'companyZZZZZZZZZZZZZZZZZZZ',
    //         'confirmPassword' => "password123"
    //     ];

    //     $this->post('/organizer/signup', $userPost, [
    //         'headers' => $headers
    //     ]);
        
    //     // $response->assertSessionHas('success', 'Participant account created and verification email sent. Please verify email now!');
    //     $response->assertSessionHas('email', $userPost['email']);

    //     // $user = User::where(
    //     //     'email', $userPost['email'],
    //     // )->first();

    //     // $this->assertEquals('ORGANIZER', $user->role);

    //     // $this->assertDatabaseHas('organizers', [
    //     //     'user_id' => $user->id
    //     // ]);

    // }

    // public function test_user_cannot_register_with_invalid_data()
    // {
    //     $response = $this->post('/participant/signup', [
    //         'name' => '',
    //         'email' => 'invalid-email',
    //         'password' => 'short',
    //         'password_confirmation' => 'nomatch',
    //     ]);

    //     $response->assertSessionHasErrors('password');
    //     $this->assertGuest();
    // }
}