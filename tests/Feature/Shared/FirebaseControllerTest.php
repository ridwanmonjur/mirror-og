<?php

namespace Tests\Feature\Shared;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestEvents, MocksFirebase};
use App\Models\Blocks;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FirebaseControllerTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestEvents, MocksFirebase;

    private $participant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->createParticipant();
        $this->mockFirestoreService();
    }

    /** @test */
    public function participant_can_block_user()
    {
        $blockedUser = $this->createParticipant();

        $response = $this->actingAs($this->participant)
            ->post("/firebase/{$blockedUser->id}/block");

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('blocks', [
            'blocker_id' => $this->participant->id,
            'blocked_id' => $blockedUser->id,
        ]);
    }

    /** @test */
    public function participant_can_unblock_user()
    {
        $blockedUser = $this->createParticipant();

        Blocks::factory()->create([
            'blocker_id' => $this->participant->id,
            'blocked_id' => $blockedUser->id,
        ]);

        $response = $this->actingAs($this->participant)
            ->post("/firebase/{$blockedUser->id}/block");

        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('blocks', [
            'blocker_id' => $this->participant->id,
            'blocked_id' => $blockedUser->id,
        ]);
    }

    /** @test */
    public function organizer_can_seed_event_results_to_firebase()
    {
        $organizer = $this->createOrganizer();
        $event = $this->createEvent(['organizer_id' => $organizer->organizer->id]);

        $response = $this->actingAs($organizer)
            ->post("/firebase/events/{$event->id}/seed-results");

        $response->assertJson(['success' => true]);
    }

    /** @test */
    public function participant_cannot_seed_results()
    {
        $event = $this->createEvent();

        $response = $this->actingAs($this->participant)
            ->post("/firebase/events/{$event->id}/seed-results");

        $response->assertForbidden();
    }

    /** @test */
    public function organizer_cannot_seed_results_for_others_event()
    {
        $organizer1 = $this->createOrganizer();
        $organizer2 = $this->createOrganizer();
        $event = $this->createEvent(['organizer_id' => $organizer2->organizer->id]);

        $response = $this->actingAs($organizer1)
            ->post("/firebase/events/{$event->id}/seed-results");

        $response->assertForbidden();
    }

    /** @test */
    public function participant_cannot_block_self()
    {
        $response = $this->actingAs($this->participant)
            ->post("/firebase/{$this->participant->id}/block");

        $response->assertJson(['success' => false]);
    }

    /** @test */
    public function guest_cannot_access_firebase_endpoints()
    {
        $user = $this->createParticipant();

        $this->post("/firebase/{$user->id}/block")
            ->assertRedirect('/login');
    }
}
