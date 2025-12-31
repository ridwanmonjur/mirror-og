<?php

namespace Tests\Feature\Shared;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestEvents};
use Tests\Mocks\MocksFirebase;
use App\Models\Blocks;
use App\Services\CloudFunctionAuthService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;

class FirebaseControllerTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestEvents, MocksFirebase;

    private $participant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->createParticipant();
        $this->mockFirestore();

        // Mock CloudFunctionAuthService to prevent actual Google Cloud calls
        $mockAuthService = Mockery::mock(CloudFunctionAuthService::class);
        $mockAuthService->shouldReceive('getCachedIdentityToken')->andReturn('mock-token');
        $mockAuthService->shouldReceive('clearIdentityTokenCache')->andReturn(null);
        $this->app->instance(CloudFunctionAuthService::class, $mockAuthService);
    }

    /** @test */
    public function participant_can_block_user()
    {
        $blockedUser = $this->createParticipant();

        // Mock HTTP call to Cloud Run
        $this->mockHttp200Response(['success' => true]);

        $response = $this->actingAs($this->participant)
            ->post("/api/user/{$blockedUser->id}/block");

        $response->assertJson(['is_blocked' => true]);

        $this->assertDatabaseHas('blocks', [
            'user_id' => $this->participant->id,
            'blocked_user_id' => $blockedUser->id,
        ]);
    }

    /** @test */
    public function participant_can_unblock_user()
    {
        $blockedUser = $this->createParticipant();

        Blocks::factory()->create([
            'user_id' => $this->participant->id,
            'blocked_user_id' => $blockedUser->id,
        ]);

        // Mock HTTP call to Cloud Run
        $this->mockHttp200Response(['success' => true]);

        $response = $this->actingAs($this->participant)
            ->post("/api/user/{$blockedUser->id}/block");

        $response->assertJson(['is_blocked' => false]);

        $this->assertDatabaseMissing('blocks', [
            'user_id' => $this->participant->id,
            'blocked_user_id' => $blockedUser->id,
        ]);
    }

    /** @test */
    public function organizer_can_seed_event_results_to_firebase()
    {
        $organizer = $this->createOrganizer();
        $event = $this->createEvent([], $organizer);

        // Mock HTTP calls to Cloud Run
        $this->mockHttp200Response(['statusReport' => 'success', 'statusDispute' => 'success']);

        $response = $this->actingAs($organizer)
            ->get("/seed/results/{$event->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function participant_can_seed_results()
    {
        $event = $this->createEvent();

        // Mock HTTP calls to Cloud Run
        $this->mockHttp200Response(['statusReport' => 'success', 'statusDispute' => 'success']);

        $response = $this->actingAs($this->participant)
            ->get("/seed/results/{$event->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function any_user_can_seed_results_for_any_event()
    {
        $organizer1 = $this->createOrganizer();
        $organizer2 = $this->createOrganizer();
        $event = $this->createEvent([], $organizer2);

        // Mock HTTP calls to Cloud Run
        $this->mockHttp200Response(['statusReport' => 'success', 'statusDispute' => 'success']);

        $response = $this->actingAs($organizer1)
            ->get("/seed/results/{$event->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function participant_cannot_block_self()
    {
        $response = $this->actingAs($this->participant)
            ->post("/api/user/{$this->participant->id}/block");

        $response->assertStatus(404);
        $response->assertJson(['is_blocked' => 'False']);
    }

    /** @test */
    public function guest_cannot_access_firebase_endpoints()
    {
        $user = $this->createParticipant();

        $this->post("/api/user/{$user->id}/block")
            ->assertRedirect();
    }

    /**
     * Mock HTTP 200 response for Cloud Run calls
     */
    protected function mockHttp200Response($data = [])
    {
        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response($data, 200),
        ]);
    }
}
