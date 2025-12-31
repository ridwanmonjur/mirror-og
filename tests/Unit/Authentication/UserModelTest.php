<?php

namespace Tests\Unit\Authentication;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{User, Participant, Organizer, Wallet, NotificationCounter};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_has_participant_relationship()
    {
        $user = $this->createParticipant();

        $this->assertInstanceOf(Participant::class, $user->participant);
        $this->assertEquals($user->id, $user->participant->user_id);
    }

    /** @test */
    public function it_has_organizer_relationship()
    {
        $user = $this->createOrganizer();

        $this->assertInstanceOf(Organizer::class, $user->organizer);
        $this->assertEquals($user->id, $user->organizer->user_id);
    }

    /** @test */
    public function it_has_wallet_relationship()
    {
        $user = $this->createParticipant();

        $this->assertInstanceOf(Wallet::class, $user->wallet);
        $this->assertEquals($user->id, $user->wallet->user_id);
    }

    /** @test */
    public function it_has_notification_counter_relationship()
    {
        $user = $this->createParticipant();

        $this->assertInstanceOf(NotificationCounter::class, $user->notificationCounter);
    }

    /** @test */
    public function admin_can_access_filament_panel()
    {
        $admin = $this->createAdmin();

        $this->assertTrue($admin->canAccessPanel(null));
    }

    /** @test */
    public function participant_cannot_access_filament_panel()
    {
        $participant = $this->createParticipant();

        $this->assertFalse($participant->canAccessPanel(null));
    }

    /** @test */
    public function organizer_cannot_access_filament_panel()
    {
        $organizer = $this->createOrganizer();

        $this->assertFalse($organizer->canAccessPanel(null));
    }

    /** @test */
    public function it_hides_sensitive_attributes_in_array()
    {
        $user = User::factory()->create();
        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    /** @test */
    public function it_casts_email_verified_at_to_datetime()
    {
        $user = User::factory()->create([
            'email_verified_at' => '2024-01-01 12:00:00',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->email_verified_at);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'name', 'email', 'password', 'role', 'email_verified_token',
            'email_verified_at', 'google_id', 'steam_id', 'firebase_uid'
        ];

        $user = new User();
        $actualFillable = $user->getFillable();

        foreach ($fillable as $field) {
            $this->assertContains($field, $actualFillable);
        }
    }

    /** @test */
    public function it_creates_participant_with_correct_role()
    {
        $user = $this->createParticipant();

        $this->assertEquals('PARTICIPANT', $user->role);
        $this->assertNotNull($user->email_verified_at);
    }

    /** @test */
    public function it_creates_organizer_with_correct_role()
    {
        $user = $this->createOrganizer();

        $this->assertEquals('ORGANIZER', $user->role);
        $this->assertNotNull($user->email_verified_at);
    }

    /** @test */
    public function it_creates_admin_with_correct_role()
    {
        $user = $this->createAdmin();

        $this->assertEquals('ADMIN', $user->role);
        $this->assertNotNull($user->email_verified_at);
    }

    /** @test */
    public function it_can_be_unverified()
    {
        $user = $this->createUnverifiedUser();

        $this->assertNull($user->email_verified_at);
    }

    /** @test */
    public function it_stores_google_id()
    {
        $user = User::factory()->create([
            'google_id' => 'google_123456789',
        ]);

        $this->assertEquals('google_123456789', $user->google_id);
    }

    /** @test */
    public function it_stores_steam_id()
    {
        $user = User::factory()->create([
            'steam_id' => '76561198000000000',
        ]);

        $this->assertEquals('76561198000000000', $user->steam_id);
    }

    /** @test */
    public function it_stores_firebase_uid()
    {
        $user = User::factory()->create([
            'firebase_uid' => 'firebase_uid_123',
        ]);

        $this->assertEquals('firebase_uid_123', $user->firebase_uid);
    }
}
