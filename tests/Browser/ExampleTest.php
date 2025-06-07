<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{

    /**
     * A basic browser test example.
     */
    public function testBasicExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee("What's happening?");
        });
    }

    // public function test_participant_can_login_successfully()
    // {
    //     User::factory()->create([
    //         'email' => 'participant@driftwood.gg',
    //         'password' => bcrypt('password123'),
    //         'role' => 'PARTICIPANT',
    //         'email_verified_at' => DB::raw('NOW()'),
    //     ]);

        

    //     $this->browse(function ($browser) {
    //         $browser->visit('/login')
    //             ->assertSee('Login')
    //             ->type('email', 'participant@driftwood.gg')
    //             ->type('password', 'password123')
    //             ->assertChecked('remember')->press('Login')
    //             ->assertPathIs('/participant/home')  
    //             ->assertSee("What's happening?");
    //     });

        
    // }

    // public function test_user_can_view_registration_form()
    // {
    //     $response = $this->get('/participant/signup');

    //     $response->assertStatus(200);
    //     $response->assertViewIs('Auth.ParticipantSignUp');

    //     $response = $this->get('/organizer/signup');

    //     $response->assertStatus(200);
    //     $response->assertViewIs('Auth.OrganizerSignUp');
    // }
}
