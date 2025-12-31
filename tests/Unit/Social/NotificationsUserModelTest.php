<?php

namespace Tests\Unit\Social;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{NotifcationsUser, User, NotificationCounter};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NotificationsUserModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createParticipant();

        $notification = NotifcationsUser::create([
            'user_id' => $user->id,
            'type' => 'event',
            'icon_type' => 'info',
            'html' => '<p>Test notification</p>',
            'link' => '/test',
            'is_read' => false,
        ]);

        $this->assertInstanceOf(User::class, $notification->user);
        $this->assertEquals($user->id, $notification->user->id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createParticipant();

        $notification = NotifcationsUser::create([
            'user_id' => $user->id,
            'type' => 'event',
            'icon_type' => 'success',
            'html' => '<p>Test notification</p>',
            'link' => '/events/1',
            'is_read' => false,
            'img_src' => '/images/test.jpg',
        ]);

        $this->assertEquals($user->id, $notification->user_id);
        $this->assertEquals('event', $notification->type);
        $this->assertEquals('success', $notification->icon_type);
        $this->assertEquals('<p>Test notification</p>', $notification->html);
        $this->assertEquals('/events/1', $notification->link);
        $this->assertFalse($notification->is_read);
        $this->assertEquals('/images/test.jpg', $notification->img_src);
    }

    /** @test */
    public function it_casts_is_read_to_boolean()
    {
        $user = $this->createParticipant();

        $notification = NotifcationsUser::create([
            'user_id' => $user->id,
            'type' => 'event',
            'icon_type' => 'info',
            'html' => '<p>Test</p>',
            'is_read' => 1,
        ]);

        $this->assertIsBool($notification->is_read);
        $this->assertTrue($notification->is_read);
    }

    /** @test */
    public function it_casts_timestamps_to_datetime()
    {
        $user = $this->createParticipant();

        $notification = NotifcationsUser::create([
            'user_id' => $user->id,
            'type' => 'event',
            'icon_type' => 'info',
            'html' => '<p>Test</p>',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $notification->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $notification->updated_at);
    }

    /** @test */
    public function it_uses_correct_table_name()
    {
        $notification = new NotifcationsUser();

        $this->assertEquals('notifications2', $notification->getTable());
    }

    /** @test */
    public function it_can_mark_as_read()
    {
        $user = $this->createParticipant();

        // Create counter first
        NotificationCounter::create([
            'user_id' => $user->id,
            'event_count' => 1,
        ]);

        $notification = NotifcationsUser::create([
            'user_id' => $user->id,
            'type' => 'event',
            'icon_type' => 'info',
            'html' => '<p>Test</p>',
            'is_read' => false,
        ]);

        $notification->markAsRead();

        $this->assertTrue($notification->fresh()->is_read);
    }

    /** @test */
    public function it_can_insert_with_count()
    {
        $user1 = $this->createParticipant();
        $user2 = $this->createParticipant();

        $notifications = [
            [
                'user_id' => $user1->id,
                'type' => 'event',
                'icon_type' => 'info',
                'html' => '<p>Test 1</p>',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user2->id,
                'type' => 'team',
                'icon_type' => 'info',
                'html' => '<p>Test 2</p>',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        NotifcationsUser::insertWithCount($notifications);

        $this->assertDatabaseCount('notifications2', 2);
    }
}
