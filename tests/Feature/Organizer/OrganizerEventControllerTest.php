<?php

namespace Tests\Feature\Organizer;

use Tests\TestCase;
use Tests\Traits\{CreatesTestUsers, CreatesTestEvents};
use App\Models\{EventDetail, EventType, EventCategory, EventTier};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrganizerEventControllerTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers, CreatesTestEvents;

    private $organizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->organizer = $this->createOrganizer();
    }

    /** @test */
    public function organizer_can_view_home_dashboard()
    {
        $response = $this->actingAs($this->organizer)->get('/organizer/home');

        $response->assertStatus(200);
        $response->assertViewIs('Organizer.Home');
    }

    /** @test */
    public function organizer_can_view_events_index()
    {
        $event = $this->createEvent(['organizer_id' => $this->organizer->organizer->id]);

        $response = $this->actingAs($this->organizer)->get('/organizer/events');

        $response->assertStatus(200);
        $response->assertViewIs('Organizer.Event.Index');
        $response->assertViewHas('events');
    }

    /** @test */
    public function organizer_can_view_create_event_form()
    {
        EventType::factory()->create();
        EventCategory::factory()->create();
        EventTier::factory()->create();

        $response = $this->actingAs($this->organizer)->get('/organizer/events/create');

        $response->assertStatus(200);
        $response->assertViewIs('Organizer.Event.Create');
        $response->assertViewHas(['eventType', 'eventCategory', 'eventTiers']);
    }

    /** @test */
    public function organizer_can_create_event()
    {
        $eventType = EventType::factory()->create();
        $eventCategory = EventCategory::factory()->create();
        $eventTier = EventTier::factory()->create();

        $eventData = [
            'eventName' => 'Test Tournament',
            'eventBanner' => 'banner.jpg',
            'eventDescription' => 'A test tournament',
            'eventPrize' => '1000',
            'event_type' => $eventType->id,
            'event_category' => $eventCategory->id,
            'event_tier' => $eventTier->id,
            'eventRegion' => 'SEA',
            'eventCountry' => 'Malaysia',
            'eventEntry' => '50',
            'eventOnline' => '1',
            'contactEmail' => 'test@example.com',
        ];

        $response = $this->actingAs($this->organizer)
            ->post('/organizer/events', $eventData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('event_details', [
            'eventName' => 'Test Tournament',
            'organizer_id' => $this->organizer->organizer->id,
        ]);
    }

    /** @test */
    public function organizer_can_view_own_event()
    {
        $event = $this->createEvent(['organizer_id' => $this->organizer->organizer->id]);

        $response = $this->actingAs($this->organizer)
            ->get("/organizer/events/{$event->id}");

        $response->assertStatus(200);
        $response->assertViewIs('Organizer.Event.Show');
        $response->assertViewHas('event');
    }

    /** @test */
    public function organizer_cannot_view_others_event()
    {
        $otherOrganizer = $this->createOrganizer();
        $event = $this->createEvent(['organizer_id' => $otherOrganizer->organizer->id]);

        $response = $this->actingAs($this->organizer)
            ->get("/organizer/events/{$event->id}");

        $response->assertForbidden();
    }

    /** @test */
    public function organizer_can_view_edit_form_for_own_event()
    {
        $event = $this->createEvent([
            'organizer_id' => $this->organizer->organizer->id,
            'eventStatus' => 'draft',
        ]);

        $response = $this->actingAs($this->organizer)
            ->get("/organizer/events/{$event->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('Organizer.Event.Edit');
        $response->assertViewHas('event');
    }

    /** @test */
    public function organizer_can_update_own_event()
    {
        $event = $this->createEvent([
            'organizer_id' => $this->organizer->organizer->id,
            'eventStatus' => 'draft',
        ]);

        $updateData = [
            'eventName' => 'Updated Tournament Name',
            'eventDescription' => 'Updated description',
        ];

        $response = $this->actingAs($this->organizer)
            ->put("/organizer/events/{$event->id}", $updateData);

        $response->assertRedirect();

        $this->assertDatabaseHas('event_details', [
            'id' => $event->id,
            'eventName' => 'Updated Tournament Name',
        ]);
    }

    /** @test */
    public function organizer_cannot_update_others_event()
    {
        $otherOrganizer = $this->createOrganizer();
        $event = $this->createEvent(['organizer_id' => $otherOrganizer->organizer->id]);

        $updateData = ['eventName' => 'Hacked Name'];

        $response = $this->actingAs($this->organizer)
            ->put("/organizer/events/{$event->id}", $updateData);

        $response->assertForbidden();

        $this->assertDatabaseMissing('event_details', [
            'id' => $event->id,
            'eventName' => 'Hacked Name',
        ]);
    }

    /** @test */
    public function organizer_can_delete_own_event()
    {
        $event = $this->createEvent([
            'organizer_id' => $this->organizer->organizer->id,
            'eventStatus' => 'draft',
        ]);

        $response = $this->actingAs($this->organizer)
            ->delete("/organizer/events/{$event->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('event_details', ['id' => $event->id]);
    }

    /** @test */
    public function organizer_cannot_delete_others_event()
    {
        $otherOrganizer = $this->createOrganizer();
        $event = $this->createEvent(['organizer_id' => $otherOrganizer->organizer->id]);

        $response = $this->actingAs($this->organizer)
            ->delete("/organizer/events/{$event->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('event_details', ['id' => $event->id]);
    }

    /** @test */
    public function organizer_can_search_events()
    {
        $this->createEvent([
            'organizer_id' => $this->organizer->organizer->id,
            'eventName' => 'Dota 2 Championship',
        ]);

        $this->createEvent([
            'organizer_id' => $this->organizer->organizer->id,
            'eventName' => 'CS:GO Tournament',
        ]);

        $response = $this->actingAs($this->organizer)
            ->get('/organizer/events/search?search=Dota');

        $response->assertStatus(200);
        $response->assertViewHas('events');
    }

    /** @test */
    public function guest_cannot_access_organizer_event_pages()
    {
        $event = $this->createEvent();

        $this->get('/organizer/home')->assertRedirect('/login');
        $this->get('/organizer/events')->assertRedirect('/login');
        $this->get('/organizer/events/create')->assertRedirect('/login');
        $this->get("/organizer/events/{$event->id}")->assertRedirect('/login');
    }

    /** @test */
    public function participant_cannot_access_organizer_event_pages()
    {
        $participant = $this->createParticipant();
        $event = $this->createEvent();

        $this->actingAs($participant)->get('/organizer/home')->assertForbidden();
        $this->actingAs($participant)->get('/organizer/events')->assertForbidden();
        $this->actingAs($participant)->get('/organizer/events/create')->assertForbidden();
    }

    /** @test */
    public function it_validates_required_fields_on_event_creation()
    {
        $response = $this->actingAs($this->organizer)
            ->post('/organizer/events', []);

        $response->assertSessionHasErrors([
            'eventName',
            'event_type',
            'event_category',
            'event_tier',
        ]);
    }

    /** @test */
    public function organizer_can_view_event_success_page()
    {
        $event = $this->createEvent(['organizer_id' => $this->organizer->organizer->id]);

        $response = $this->actingAs($this->organizer)
            ->get("/organizer/events/{$event->id}/success");

        $response->assertStatus(200);
        $response->assertViewIs('Organizer.Event.Success');
        $response->assertViewHas('event');
    }

    /** @test */
    public function events_are_paginated()
    {
        // Create 15 events
        for ($i = 0; $i < 15; $i++) {
            $this->createEvent(['organizer_id' => $this->organizer->organizer->id]);
        }

        $response = $this->actingAs($this->organizer)
            ->get('/organizer/events');

        $response->assertStatus(200);
        $response->assertViewHas('events');
    }

    /** @test */
    public function organizer_only_sees_own_events_in_index()
    {
        $myEvent = $this->createEvent([
            'organizer_id' => $this->organizer->organizer->id,
            'eventName' => 'My Event',
        ]);

        $otherOrganizer = $this->createOrganizer();
        $otherEvent = $this->createEvent([
            'organizer_id' => $otherOrganizer->organizer->id,
            'eventName' => 'Other Event',
        ]);

        $response = $this->actingAs($this->organizer)
            ->get('/organizer/events');

        $response->assertStatus(200);
        $events = $response->viewData('events');

        $this->assertTrue($events->contains('id', $myEvent->id));
        $this->assertFalse($events->contains('id', $otherEvent->id));
    }
}
