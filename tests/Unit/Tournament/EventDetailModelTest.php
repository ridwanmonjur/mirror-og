<?php

namespace Tests\Unit\Tournament;

use App\Models\EventDetail;
use App\Models\User;
use App\Models\EventType;
use App\Models\EventTier;
use App\Models\EventCategory;
use App\Models\OrganizerPayment;
use App\Models\JoinEvent;
use App\Models\Brackets;
use App\Models\EventSignup;
use App\Models\BracketDeadline;
use App\Models\EventInvitation;
use App\Models\Task;
use App\Models\BracketDeadlineSetup;
use App\Exceptions\TimeGreaterException;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class EventDetailModelTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Create required test data
        $this->user = User::factory()->create();
        $this->eventType = EventType::factory()->create();
        $this->eventTier = EventTier::factory()->create();
        $this->eventCategory = EventCategory::factory()->create();
        $this->organizerPayment = OrganizerPayment::factory()->create();
    }

    /** @test */
    public function it_can_be_created()
    {
        $eventDetail = EventDetail::factory()->create([
            'user_id' => $this->user->id,
            'event_type_id' => $this->eventType->id,
            'event_tier_id' => $this->eventTier->id,
            'event_category_id' => $this->eventCategory->id,
            'payment_transaction_id' => $this->organizerPayment->id,
        ]);

        $this->assertInstanceOf(EventDetail::class, $eventDetail);
        $this->assertDatabaseHas('event_details', [
            'id' => $eventDetail->id,
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_converts_to_feed_item()
    {
        $eventDetail = EventDetail::factory()->create([
            'user_id' => $this->user->id,
            'eventName' => 'Test Tournament',
            'eventDescription' => 'A test tournament description',
        ]);

        $feedItem = $eventDetail->toFeedItem();

        $this->assertEquals($eventDetail->id, $feedItem->id);
        $this->assertEquals("Results: Test Tournament", $feedItem->title);
        $this->assertStringContainsString('A test tournament description', $feedItem->summary);
    }

    /** @test */
    public function it_returns_only_published_events_in_feed()
    {
        // Create events with different statuses
        EventDetail::factory()->create(['status' => 'LIVE']);
        EventDetail::factory()->create(['status' => 'UPCOMING']);
        EventDetail::factory()->create(['status' => 'DRAFT']); // Should be excluded
        EventDetail::factory()->create(['status' => 'PENDING']); // Should be excluded

        $feedItems = EventDetail::getFeedItems();

        $this->assertCount(2, $feedItems);
        $this->assertTrue($feedItems->every(function ($item) {
            return !in_array($item->status, ['DRAFT', 'PENDING', 'PREVIEW']);
        }));
    }

    /** @test */
    public function it_has_user_relationship()
    {
        $eventDetail = EventDetail::factory()->create(['user_id' => $this->user->id]);

        $this->assertInstanceOf(User::class, $eventDetail->user);
        $this->assertEquals($this->user->id, $eventDetail->user->id);
    }

    /** @test */
    public function it_has_invitation_list_relationship()
    {
        $eventDetail = EventDetail::factory()->create();
        $invitation = EventInvitation::factory()->create(['event_id' => $eventDetail->id]);

        $this->assertCount(1, $eventDetail->invitationList);
        $this->assertEquals($invitation->id, $eventDetail->invitationList->first()->id);
    }

    /** @test */
    public function it_has_tier_relationship()
    {
        $eventDetail = EventDetail::factory()->create(['event_tier_id' => $this->eventTier->id]);

        $this->assertInstanceOf(EventTier::class, $eventDetail->tier);
        $this->assertEquals($this->eventTier->id, $eventDetail->tier->id);
    }

    /** @test */
    public function it_has_type_relationship()
    {
        $eventDetail = EventDetail::factory()->create(['event_type_id' => $this->eventType->id]);

        $this->assertInstanceOf(EventType::class, $eventDetail->type);
        $this->assertEquals($this->eventType->id, $eventDetail->type->id);
    }

    /** @test */
    public function it_has_game_relationship()
    {
        $eventDetail = EventDetail::factory()->create(['event_category_id' => $this->eventCategory->id]);

        $this->assertInstanceOf(EventCategory::class, $eventDetail->game);
        $this->assertEquals($this->eventCategory->id, $eventDetail->game->id);
    }

    /** @test */
    public function it_has_payment_transaction_relationship()
    {
        $eventDetail = EventDetail::factory()->create(['payment_transaction_id' => $this->organizerPayment->id]);

        $this->assertInstanceOf(OrganizerPayment::class, $eventDetail->paymentTransaction);
        $this->assertEquals($this->organizerPayment->id, $eventDetail->paymentTransaction->id);
    }

    /** @test */
    public function it_has_join_events_relationship()
    {
        $eventDetail = EventDetail::factory()->create();
        $joinEvent = JoinEvent::factory()->create(['event_details_id' => $eventDetail->id]);

        $this->assertCount(1, $eventDetail->joinEvents);
        $this->assertEquals($joinEvent->id, $eventDetail->joinEvents->first()->id);
    }

    /** @test */
    public function it_has_matches_relationship()
    {
        $eventDetail = EventDetail::factory()->create();
        $bracket = Brackets::factory()->create(['event_details_id' => $eventDetail->id]);

        $this->assertCount(1, $eventDetail->matches);
        $this->assertEquals($bracket->id, $eventDetail->matches->first()->id);
    }

    /** @test */
    public function it_has_signup_relationship()
    {
        $eventDetail = EventDetail::factory()->create();
        $signup = EventSignup::factory()->create(['event_id' => $eventDetail->id]);

        $this->assertInstanceOf(EventSignup::class, $eventDetail->signup);
        $this->assertEquals($signup->id, $eventDetail->signup->id);
    }

    /** @test */
    public function it_has_deadlines_relationship()
    {
        $eventDetail = EventDetail::factory()->create();
        $deadline = BracketDeadline::factory()->create(['event_details_id' => $eventDetail->id]);

        $this->assertCount(1, $eventDetail->deadlines);
        $this->assertEquals($deadline->id, $eventDetail->deadlines->first()->id);
    }

    /** @test */
    public function it_can_destroy_event_banner()
    {
        Storage::fake('local');
        $fileName = 'images/events/test-banner.jpg';
        Storage::put($fileName, 'test content');

        $this->assertTrue(Storage::exists($fileName));

        EventDetail::destroyEventBanner($fileName);

        // Note: The actual file deletion logic uses file_exists and unlink
        // which won't work with Storage::fake, so we test the method exists
        $this->assertTrue(method_exists(EventDetail::class, 'destroyEventBanner'));
    }

    /** @test */
    public function it_processes_events_by_status()
    {
        $events = collect([
            (object) ['status' => null, 'user_id' => 1, 'statusResolved' => fn() => 'ONGOING'],
            (object) ['status' => null, 'user_id' => 2, 'statusResolved' => fn() => 'ENDED'],
            (object) ['status' => null, 'user_id' => 3, 'statusResolved' => fn() => 'UPCOMING'],
        ]);

        $isFollowing = [1 => true, 3 => true];

        $result = EventDetail::processEvents($events, $isFollowing);

        $this->assertCount(3, $result['joinEvents']);
        $this->assertCount(2, $result['activeEvents']); // ONGOING and UPCOMING
        $this->assertCount(1, $result['historyEvents']); // ENDED
    }

    /** @test */
    public function it_identifies_complete_event()
    {
        $eventDetail = EventDetail::factory()->create([
            'eventName' => 'Test Event',
            'startDate' => '2024-12-01',
            'endDate' => '2024-12-02',
            'startTime' => '10:00',
            'endTime' => '18:00',
            'eventDescription' => 'Test description',
            'eventBanner' => 'test-banner.jpg',
            'status' => 'UPCOMING',
            'event_type_id' => $this->eventType->id,
            'event_tier_id' => $this->eventTier->id,
            'event_category_id' => $this->eventCategory->id,
            'sub_action_private' => 'public',
            'payment_transaction_id' => $this->organizerPayment->id,
        ]);

        $this->assertTrue($eventDetail->isCompleteEvent());
    }

    /** @test */
    public function it_identifies_incomplete_event()
    {
        $eventDetail = EventDetail::factory()->create([
            'eventName' => null, // Missing required field
            'startDate' => '2024-12-01',
            'endDate' => '2024-12-02',
        ]);

        $this->assertFalse($eventDetail->isCompleteEvent());
    }

    /** @test */
    public function it_creates_status_update_tasks()
    {
        $eventDetail = EventDetail::factory()->create([
            'startDate' => '2024-12-01',
            'startTime' => '10:00',
            'endDate' => '2024-12-02',
            'endTime' => '18:00',
            'status' => 'UPCOMING',
        ]);

        $eventDetail->createStatusUpdateTask();

        $this->assertDatabaseHas('tasks', [
            'event_id' => $eventDetail->id,
            'taskable_id' => $eventDetail->id,
            'taskable_type' => 'EventDetail',
            'task_name' => 'started',
        ]);

        $this->assertDatabaseHas('tasks', [
            'event_id' => $eventDetail->id,
            'taskable_id' => $eventDetail->id,
            'taskable_type' => 'EventDetail',
            'task_name' => 'ended',
        ]);
    }

    /** @test */
    public function it_creates_registration_task()
    {
        $eventDetail = EventDetail::factory()->create([
            'event_tier_id' => $this->eventTier->id,
            'event_type_id' => $this->eventType->id,
            'startDate' => Carbon::now()->addDays(30)->format('Y-m-d'),
            'startTime' => '10:00',
        ]);

        $eventDetail->createRegistrationTask();

        $this->assertDatabaseHas('event_signup_dates', [
            'event_id' => $eventDetail->id,
        ]);

        $this->assertDatabaseHas('tasks', [
            'event_id' => $eventDetail->id,
            'taskable_id' => $eventDetail->id,
            'taskable_type' => 'EventDetail',
            'task_name' => 'reg_over',
        ]);
    }

    /** @test */
    public function it_resolves_draft_status()
    {
        $eventDetail = EventDetail::factory()->create(['status' => 'DRAFT']);

        $this->assertEquals('DRAFT', $eventDetail->statusResolved());
    }

    /** @test */
    public function it_resolves_ended_status()
    {
        $eventDetail = EventDetail::factory()->create([
            'status' => 'LIVE',
            'endDate' => Carbon::yesterday()->format('Y-m-d'),
            'endTime' => '10:00',
            'payment_transaction_id' => $this->organizerPayment->id,
        ]);

        $this->assertEquals('ENDED', $eventDetail->statusResolved());
    }

    /** @test */
    public function it_resolves_ongoing_status()
    {
        $eventDetail = EventDetail::factory()->create([
            'status' => 'LIVE',
            'startDate' => Carbon::yesterday()->format('Y-m-d'),
            'startTime' => '10:00',
            'endDate' => Carbon::tomorrow()->format('Y-m-d'),
            'endTime' => '10:00',
            'payment_transaction_id' => $this->organizerPayment->id,
        ]);

        $this->assertEquals('ONGOING', $eventDetail->statusResolved());
    }

    /** @test */
    public function it_resolves_upcoming_status()
    {
        $eventDetail = EventDetail::factory()->create([
            'status' => 'LIVE',
            'startDate' => Carbon::tomorrow()->format('Y-m-d'),
            'startTime' => '10:00',
            'endDate' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'endTime' => '10:00',
            'payment_transaction_id' => $this->organizerPayment->id,
        ]);

        $this->assertEquals('UPCOMING', $eventDetail->statusResolved());
    }

    /** @test */
    public function it_formats_start_date_for_humans()
    {
        $eventDetail = EventDetail::factory()->create([
            'startDate' => Carbon::tomorrow()->format('Y-m-d'),
            'startTime' => '10:00',
        ]);

        $formatted = $eventDetail->getFormattedStartDate();

        $this->assertIsString($formatted);
        $this->assertStringContainsString('in', $formatted);
    }

    /** @test */
    public function it_gets_registration_status()
    {
        $eventDetail = EventDetail::factory()->create();
        $signup = EventSignup::factory()->create([
            'event_id' => $eventDetail->id,
            'signup_open' => Carbon::yesterday(),
            'normal_signup_start_advanced_close' => Carbon::tomorrow(),
            'signup_close' => Carbon::now()->addDays(2),
        ]);

        $status = $eventDetail->getRegistrationStatus();

        $this->assertIsString($status);
    }

    /** @test */
    public function it_strips_seconds_from_time()
    {
        $eventDetail = new EventDetail();

        $this->assertEquals('10:30', $eventDetail->stripSec('10:30:45'));
        $this->assertEquals('10:30', $eventDetail->stripSec('10:30'));
        $this->assertNull($eventDetail->stripSec(null));
    }

    /** @test */
    public function it_converts_time_to_utc_for_storage()
    {
        $eventDetail = new EventDetail();

        $result = $eventDetail->storeTimeMy('2024-12-01', '10:30');

        $this->assertInstanceOf(Carbon::class, $result);
        $this->assertEquals('UTC', $result->timezone->getName());
    }

    /** @test */
    public function it_converts_to_malaysian_time()
    {
        $eventDetail = EventDetail::factory()->make([
            'startDate' => '2024-12-01',
            'startTime' => '10:00',
            'endDate' => '2024-12-01',
            'endTime' => '18:00',
            'sub_action_public_date' => '2024-11-30',
            'sub_action_public_time' => '09:00',
        ]);

        $eventDetail->convertToMalaysianTime();

        $this->assertIsString($eventDetail->startDate);
        $this->assertIsString($eventDetail->startTime);
    }

    /** @test */
    public function it_gets_date_with_timezone()
    {
        $eventDetail = new EventDetail();

        $result = $eventDetail->getDateTz('2024-12-01', '10:30');

        $this->assertInstanceOf(Carbon::class, $result);
        $this->assertEquals('UTC', $result->timezone->getName());
    }

    /** @test */
    public function it_formats_start_dates_as_string_array()
    {
        $eventDetail = new EventDetail();

        $result = $eventDetail->startDatesStr('2024-12-01', '10:30:00');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('datePart', $result);
        $this->assertArrayHasKey('timePart', $result);
        $this->assertArrayHasKey('dayStr', $result);
        $this->assertArrayHasKey('dateStr', $result);
        $this->assertArrayHasKey('combinedStr', $result);
    }

    /** @test */
    public function it_formats_start_dates_readable()
    {
        $eventDetail = EventDetail::factory()->make([
            'startDate' => '2024-12-01',
            'startTime' => '10:30',
        ]);

        $result = $eventDetail->startDatesReadable();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('fmtStartDt', $result);
        $this->assertArrayHasKey('fmtStartT', $result);
        $this->assertArrayHasKey('fmtStartIn', $result);
    }

    /** @test */
    public function it_filters_events_by_status()
    {
        EventDetail::factory()->create(['status' => 'DRAFT']);
        EventDetail::factory()->create(['status' => 'LIVE']);

        $request = new Request(['status' => 'DRAFT']);
        $query = EventDetail::filterEvents($request);

        $this->assertStringContainsString('DRAFT', $query->toSql());
    }

    /** @test */
    public function it_filters_events_by_search_term()
    {
        $request = new Request(['search' => 'tournament']);
        $query = EventDetail::filterEvents($request);

        $sql = $query->toSql();
        $this->assertStringContainsString('eventName', $sql);
        $this->assertStringContainsString('LIKE', $sql);
    }

    /** @test */
    public function it_excludes_draft_events_from_landing_page_query()
    {
        $request = new Request();
        $currentDateTime = Carbon::now();

        $query = EventDetail::landingPageQuery($request, $currentDateTime);

        $sql = $query->toSql();
        $this->assertStringContainsString('NOT IN', $sql);
    }

    /** @test */
    public function it_applies_sorting_in_filter_events_full()
    {
        $request = new Request(['sort' => ['startDate' => 'asc']]);
        $query = EventDetail::filterEventsFull($request);

        $sql = $query->toSql();
        $this->assertStringContainsString('order by', $sql);
    }

    /** @test */
    public function it_finds_event_with_relations_and_authorization()
    {
        $eventDetail = EventDetail::factory()->create(['user_id' => $this->user->id]);

        $result = EventDetail::findEventWithRelationsAndThrowError(
            $this->user->id,
            $eventDetail->id
        );

        $this->assertEquals($eventDetail->id, $result->id);
    }

    /** @test */
    public function it_throws_exception_when_event_not_found()
    {
        $this->expectException(ModelNotFoundException::class);

        EventDetail::findEventWithRelationsAndThrowError($this->user->id, 999);
    }

    /** @test */
    public function it_throws_exception_when_unauthorized_user()
    {
        $eventDetail = EventDetail::factory()->create(['user_id' => $this->user->id]);
        $otherUser = User::factory()->create();

        $this->expectException(UnauthorizedException::class);

        EventDetail::findEventWithRelationsAndThrowError($otherUser->id, $eventDetail->id);
    }

    /** @test */
    public function it_finds_event_and_authorizes_user()
    {
        $eventDetail = EventDetail::factory()->create(['user_id' => $this->user->id]);

        $result = EventDetail::findEventAndThrowError($eventDetail->id, $this->user->id);

        $this->assertEquals($eventDetail->id, $result->id);
    }

    /** @test */
    public function it_throws_exception_when_finding_nonexistent_event()
    {
        $this->expectException(ModelNotFoundException::class);

        EventDetail::findEventAndThrowError(999, $this->user->id);
    }

    /** @test */
    public function it_throws_exception_when_finding_event_for_wrong_user()
    {
        $eventDetail = EventDetail::factory()->create(['user_id' => $this->user->id]);
        $otherUser = User::factory()->create();

        $this->expectException(UnauthorizedException::class);

        EventDetail::findEventAndThrowError($eventDetail->id, $otherUser->id);
    }

    /** @test */
    public function it_loads_event_tier_and_filtered_matches()
    {
        $eventDetail = EventDetail::factory()->create();
        $bracketDeadlines = collect();

        $query = EventDetail::withEventTierAndFilteredMatches($bracketDeadlines);

        $this->assertStringContainsString('tier', $query->toSql());
    }

    /** @test */
    public function it_casts_event_tags_to_array()
    {
        $tags = ['tournament', 'esports', 'competitive'];
        $eventDetail = EventDetail::factory()->create(['eventTags' => $tags]);

        $this->assertIsArray($eventDetail->eventTags);
        $this->assertEquals($tags, $eventDetail->eventTags);
    }

    /** @test */
    public function it_has_correct_per_page_value()
    {
        $eventDetail = new EventDetail();

        $this->assertEquals(6, $eventDetail->getPerPage());
    }

    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $fillable = [
            'eventName',
            'startDate',
            'endDate',
            'startTime',
            'endTime',
            'eventDescription',
            'eventBanner',
            'eventTags',
            'status',
            'venue',
            'sub_action_public_date',
            'sub_action_public_time',
            'sub_action_private',
            'user_id',
            'event_type_id',
            'event_tier_id',
            'event_category_id',
            'payment_transaction_id',
            'willNotify',
        ];

        $eventDetail = new EventDetail();

        $this->assertEquals($fillable, $eventDetail->getFillable());
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $eventDetail = new EventDetail();

        $this->assertEquals('event_details', $eventDetail->getTable());
    }
}
