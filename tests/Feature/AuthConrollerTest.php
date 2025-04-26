<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthConrollerTest extends TestCase
{
    // use RefreshDatabase;

    public function test_logout_action()
    {
        $user = User::createOrFirst(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'role' => 'ORGANIZER'
            ]
        );

        $this->actingAs($user);

        $response = $this->get('/logout');

        $response->assertRedirect('/home');

        $this->assertGuest();
    }

    public function test_country_list()
    {

        $response = $this->getJson('/countries', );

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => ['name', 'emoji_flag', 'id']
                     ]
                 ]);

        $response->assertJson(['success' => true]);

    }
}