<?php

namespace Tests\Feature\Organizer;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrganizerControllerTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    private $organizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->organizer = $this->createOrganizer();
    }

    /** @test */
    public function organizer_can_view_own_profile()
    {
        $response = $this->actingAs($this->organizer)
            ->get('/organizer/profile');

        $response->assertStatus(200);
        $response->assertViewIs('Organizer.Profile.Show');
        $response->assertViewHas('organizer');
    }

    /** @test */
    public function organizer_can_view_another_organizers_profile()
    {
        $otherOrganizer = $this->createOrganizer();

        $response = $this->actingAs($this->organizer)
            ->get("/organizer/profile/{$otherOrganizer->organizer->id}");

        $response->assertStatus(200);
        $response->assertViewIs('Organizer.Profile.Show');
        $response->assertViewHas('organizer');
    }

    /** @test */
    public function organizer_can_update_own_profile()
    {
        $updateData = [
            'companyName' => 'Updated Company',
            'companyDescription' => 'Updated description',
            'phone' => '+60123456789',
            'address' => '123 Test Street',
        ];

        $response = $this->actingAs($this->organizer)
            ->put('/organizer/profile', $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('organizers', [
            'user_id' => $this->organizer->id,
            'companyName' => 'Updated Company',
            'companyDescription' => 'Updated description',
        ]);
    }

    /** @test */
    public function it_validates_required_fields_on_profile_update()
    {
        $response = $this->actingAs($this->organizer)
            ->put('/organizer/profile', []);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function guest_cannot_access_organizer_profile_pages()
    {
        $this->get('/organizer/profile')
            ->assertRedirect('/login');

        $this->put('/organizer/profile', [])
            ->assertRedirect('/login');
    }

    /** @test */
    public function participant_cannot_access_organizer_profile_pages()
    {
        $participant = $this->createParticipant();

        $this->actingAs($participant)
            ->get('/organizer/profile')
            ->assertForbidden();

        $this->actingAs($participant)
            ->put('/organizer/profile', [])
            ->assertForbidden();
    }

    /** @test */
    public function organizer_profile_displays_correct_information()
    {
        $this->organizer->organizer->update([
            'companyName' => 'Test Gaming Company',
            'companyDescription' => 'We organize esports events',
        ]);

        $response = $this->actingAs($this->organizer)
            ->get('/organizer/profile');

        $response->assertStatus(200);
        $response->assertSee('Test Gaming Company');
        $response->assertSee('We organize esports events');
    }

    /** @test */
    public function viewing_nonexistent_organizer_profile_returns_404()
    {
        $response = $this->actingAs($this->organizer)
            ->get('/organizer/profile/99999');

        $response->assertStatus(404);
    }
}
