<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\EventDetail;
use App\Models\Friend;
use App\Models\OrganizerFollow;
use App\Models\ParticipantFollow;
use App\Models\JoinEvent;
use App\Models\NotifcationsUser;

class ResetAndSeedNotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing notifications and counters
        $this->command->info('Deleting all existing notifications and counters...');
        $deletedNotifications = DB::table('notifications2')->delete();
        $deletedCounters = DB::table('notification_counters')->delete();
        $this->command->info("Deleted {$deletedNotifications} notifications and {$deletedCounters} counters.");

        // Get data for seeding
        $friends = Friend::where('status', 'accepted')->limit(50)->get();
        $organizerFollows = OrganizerFollow::limit(50)->get();
        $participantFollows = ParticipantFollow::limit(50)->get();
        $joinEvents = JoinEvent::with(['eventDetails', 'user'])->limit(50)->get();

        if ($friends->isEmpty() && $organizerFollows->isEmpty() && $participantFollows->isEmpty() && $joinEvents->isEmpty()) {
            $this->command->error('Not enough data found to seed notifications.');
            return;
        }

        $this->command->info('Creating new notifications...');

        $startDate = Carbon::create(2024, 9, 1);
        $endDate = Carbon::create(2025, 5, 31);

        $notifications = [];
        $notificationCount = 0;

        // Create notifications for friendships
        foreach ($friends as $friend) {
            $randomDate = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );

            // Notification for user1
            $notifications[] = [
                'user_id' => $friend->user1_id,
                'type' => 'social',
                'icon_type' => 'friend',
                'img_src' => null,
                'html' => $this->generateFriendshipNotificationHtml($friend->user2),
                'link' => "/view/participant/{$friend->user2_id}",
                'is_read' => rand(0, 1) ? true : false,
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ];
            $notificationCount++;

            // Notification for user2
            $notifications[] = [
                'user_id' => $friend->user2_id,
                'type' => 'social',
                'icon_type' => 'friend',
                'img_src' => null,
                'html' => $this->generateFriendshipNotificationHtml($friend->user1),
                'link' => "/view/participant/{$friend->user1_id}",
                'is_read' => rand(0, 1) ? true : false,
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ];
            $notificationCount++;

            // Insert in batches of 100
            if (count($notifications) >= 100) {
                NotifcationsUser::insertWithCount($notifications);
                $notifications = [];
            }
        }

        // Create notifications for organizer follows
        foreach ($organizerFollows as $follow) {
            $randomDate = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );

            $notifications[] = [
                'user_id' => $follow->organizer_user_id,
                'type' => 'social',
                'icon_type' => 'follow',
                'img_src' => null,
                'html' => $this->generateFollowNotificationHtml($follow->participantUser, 'organizer'),
                'link' => "/view/participant/{$follow->participant_user_id}",
                'is_read' => rand(0, 1) ? true : false,
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ];
            $notificationCount++;

            // Insert in batches of 100
            if (count($notifications) >= 100) {
                NotifcationsUser::insertWithCount($notifications);
                $notifications = [];
            }
        }

        // Create notifications for participant follows
        foreach ($participantFollows as $follow) {
            $randomDate = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );

            $notifications[] = [
                'user_id' => $follow->participant_followee,
                'type' => 'social',
                'icon_type' => 'follow',
                'img_src' => null,
                'html' => $this->generateFollowNotificationHtml($follow->followerUser, 'participant'),
                'link' => "/view/participant/{$follow->participant_follower}",
                'is_read' => rand(0, 1) ? true : false,
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ];
            $notificationCount++;

            // Insert in batches of 100
            if (count($notifications) >= 100) {
                NotifcationsUser::insertWithCount($notifications);
                $notifications = [];
            }
        }

        // Create notifications for event signups
        foreach ($joinEvents as $joinEvent) {
            if (!$joinEvent->eventDetails || !$joinEvent->user) {
                continue;
            }

            $randomDate = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );

            // Notification for the organizer about a new signup
            $notifications[] = [
                'user_id' => $joinEvent->eventDetails->user_id,
                'type' => 'event',
                'icon_type' => 'event_signup',
                'img_src' => null,
                'html' => $this->generateEventSignupNotificationHtml($joinEvent->user, $joinEvent->eventDetails, 'organizer'),
                'link' => "/event/{$joinEvent->event_details_id}",
                'is_read' => rand(0, 1) ? true : false,
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ];
            $notificationCount++;

            // Notification for the participant about their signup
            $notifications[] = [
                'user_id' => $joinEvent->joiner_id,
                'type' => 'event',
                'icon_type' => 'event_signup',
                'img_src' => null,
                'html' => $this->generateEventSignupNotificationHtml($joinEvent->user, $joinEvent->eventDetails, 'participant'),
                'link' => "/event/{$joinEvent->event_details_id}",
                'is_read' => rand(0, 1) ? true : false,
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ];
            $notificationCount++;

            // Insert in batches of 100
            if (count($notifications) >= 100) {
                NotifcationsUser::insertWithCount($notifications);
                $notifications = [];
            }
        }

        // Insert remaining notifications
        if (!empty($notifications)) {
            NotifcationsUser::insertWithCount($notifications);
        }

        $this->command->info("Successfully created {$notificationCount} new notifications.");
    }

    /**
     * Generate HTML for friendship notification
     */
    private function generateFriendshipNotificationHtml($friend): string
    {
        $friendName = $friend->name ?? 'Unknown User';
        $friendId = $friend->id ?? 0;

        return <<<HTML
            <span class="notification-gray">
                You are now friends with
                <a class="px-0 border-0" href="/view/participant/{$friendId}" alt="Friend link">
                    <span class="notification-blue">{$friendName}</span>
                </a>
            </span>
        HTML;
    }

    /**
     * Generate HTML for follow notification
     */
    private function generateFollowNotificationHtml($follower, $targetRole): string
    {
        $followerName = $follower->name ?? 'Unknown User';
        $followerId = $follower->id ?? 0;
        $followerRole = $follower->role === 'ORGANIZER' ? 'organizer' : 'participant';

        return <<<HTML
            <span class="notification-gray">
                <a class="px-0 border-0" href="/view/{$followerRole}/{$followerId}" alt="Follower link">
                    <span class="notification-blue">{$followerName}</span>
                </a>
                started following you
            </span>
        HTML;
    }

    /**
     * Generate HTML for event signup notification
     */
    private function generateEventSignupNotificationHtml($user, $event, $recipientType): string
    {
        $userName = $user->name ?? 'Unknown User';
        $userId = $user->id ?? 0;
        $eventName = $event->eventName ?? 'Unknown Event';
        $eventId = $event->id ?? 0;
        $eventTier = $event->tier->eventTier ?? 'Free';

        if ($recipientType === 'organizer') {
            return <<<HTML
                <span class="notification-gray">
                    <a class="px-0 border-0" href="/view/participant/{$userId}" alt="User link">
                        <span class="notification-blue">{$userName}</span>
                    </a>
                    signed up for your event
                    <button class="btn-transparent px-0 border-0 Color-{$eventTier}" data-href="/event/{$eventId}">
                        {$eventName}
                    </button>
                </span>
            HTML;
        } else {
            $organizerName = $event->user->name ?? 'Unknown Organizer';
            $organizerId = $event->user->id ?? 0;

            return <<<HTML
                <span class="notification-gray">
                    You signed up for
                    <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/organizer/{$organizerId}">
                        {$organizerName}
                    </button>'s event
                    <button class="btn-transparent px-0 border-0 Color-{$eventTier}" data-href="/event/{$eventId}">
                        {$eventName}
                    </button>
                </span>
            HTML;
        }
    }
}
