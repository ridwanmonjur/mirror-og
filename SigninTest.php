<!-- <?php
// namespace Tests\Acceptance\Auth;

// use App\Models\User;
// use Illuminate\Foundation\Testing\RefreshDatabase;
// namespace Tests\Browser\Pages;

// use App\Models\User;
// use Laravel\Dusk\Browser;
// use Tests\DuskTestCase;

// class SigninTest extends DuskTestCase
// {

  
  

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
// } 