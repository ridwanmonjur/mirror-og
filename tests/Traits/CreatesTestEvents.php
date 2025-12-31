<?php

namespace Tests\Traits;

use App\Models\EventDetail;
use App\Models\EventTier;
use App\Models\EventType;
use App\Models\EventCategory;
use App\Models\EventSignup;
use App\Models\OrganizerPayment;
use Carbon\Carbon;

trait CreatesTestEvents
{
    /**
     * Create a basic event with required relationships
     */
    protected function createEvent(array $attributes = [], $user = null)
    {
        if (!$user) {
            $user = $this->createOrganizer();
        }

        $tier = EventTier::factory()->create();
        $type = EventType::factory()->create();
        $category = EventCategory::factory()->create();
        $payment = OrganizerPayment::factory()->create();

        return EventDetail::factory()->create(array_merge([
            'user_id' => $user->id,
            'event_tier_id' => $tier->id,
            'event_type_id' => $type->id,
            'event_category_id' => $category->id,
            'payment_transaction_id' => $payment->id,
            'status' => 'LIVE',
        ], $attributes));
    }

    /**
     * Create a draft event
     */
    protected function createDraftEvent($user = null)
    {
        return $this->createEvent(['status' => 'DRAFT'], $user);
    }

    /**
     * Create a live event
     */
    protected function createLiveEvent($user = null)
    {
        return $this->createEvent([
            'status' => 'LIVE',
            'startDate' => Carbon::tomorrow()->format('Y-m-d'),
            'startTime' => '10:00',
            'endDate' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'endTime' => '18:00',
        ], $user);
    }

    /**
     * Create an upcoming event
     */
    protected function createUpcomingEvent($user = null)
    {
        return $this->createEvent([
            'status' => 'LIVE',
            'startDate' => Carbon::tomorrow()->format('Y-m-d'),
            'startTime' => '10:00',
            'endDate' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'endTime' => '18:00',
        ], $user);
    }

    /**
     * Create an ongoing event
     */
    protected function createOngoingEvent($user = null)
    {
        return $this->createEvent([
            'status' => 'LIVE',
            'startDate' => Carbon::yesterday()->format('Y-m-d'),
            'startTime' => '10:00',
            'endDate' => Carbon::tomorrow()->format('Y-m-d'),
            'endTime' => '18:00',
        ], $user);
    }

    /**
     * Create an ended event
     */
    protected function createEndedEvent($user = null)
    {
        return $this->createEvent([
            'status' => 'LIVE',
            'startDate' => Carbon::now()->subDays(3)->format('Y-m-d'),
            'startTime' => '10:00',
            'endDate' => Carbon::yesterday()->format('Y-m-d'),
            'endTime' => '18:00',
        ], $user);
    }

    /**
     * Create event with registration setup
     */
    protected function createEventWithRegistration($user = null)
    {
        $event = $this->createLiveEvent($user);

        EventSignup::factory()->create([
            'event_id' => $event->id,
            'signup_open' => Carbon::now()->subDays(7),
            'normal_signup_start_advanced_close' => Carbon::now()->addDays(7),
            'signup_close' => Carbon::now()->addDays(14),
        ]);

        return $event->fresh(['signup']);
    }

    /**
     * Create event with brackets (for tournament testing)
     */
    protected function createEventWithBrackets($teamSize = 8, $user = null)
    {
        $event = $this->createLiveEvent($user);

        // Create bracket structure would go here
        // This depends on BracketDataService integration

        return $event;
    }

    /**
     * Create a complete event (all fields filled)
     */
    protected function createCompleteEvent($user = null)
    {
        return $this->createEvent([
            'eventName' => 'Complete Test Tournament',
            'startDate' => Carbon::tomorrow()->format('Y-m-d'),
            'endDate' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'startTime' => '10:00',
            'endTime' => '18:00',
            'eventDescription' => 'A complete test tournament with all fields',
            'eventBanner' => 'test-banner.jpg',
            'status' => 'LIVE',
            'venue' => 'Online',
            'sub_action_private' => 'public',
            'eventTags' => ['test', 'tournament', 'esports'],
        ], $user);
    }

    /**
     * Create multiple events
     */
    protected function createEvents($count = 3, array $attributes = [], $user = null)
    {
        $events = [];

        for ($i = 0; $i < $count; $i++) {
            $events[] = $this->createEvent($attributes, $user);
        }

        return collect($events);
    }
}
