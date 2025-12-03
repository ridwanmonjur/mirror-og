<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\EventDetail;
use App\Models\ActivityLogs;

class ResetAndSeedActivityLogsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing activity logs
        $this->command->info('Deleting all existing activity logs...');
        $deletedCount = DB::table('activity_logs')->delete();
        $this->command->info("Deleted {$deletedCount} activity logs.");

        // Get users and events for seeding
        $users = User::where('role', 'PARTICIPANT')->limit(20)->get();
        $events = EventDetail::limit(10)->get();

        if ($users->isEmpty() || $events->isEmpty()) {
            $this->command->error('Not enough users or events found to seed activity logs.');
            return;
        }

        $this->command->info('Creating new activity logs...');

        $startDate = Carbon::create(2024, 9, 1);
        $endDate = Carbon::create(2025, 5, 31);

        $activityLogs = [];
        $logCount = 0;

        // Create activity logs for each user
        foreach ($users as $index => $user) {
            // Random number of activities per user (5-15)
            $activityCount = rand(5, 15);

            for ($i = 0; $i < $activityCount; $i++) {
                // Random date between Sep 2024 and May 2025
                $randomDate = Carbon::createFromTimestamp(
                    rand($startDate->timestamp, $endDate->timestamp)
                );

                // Random activity type (1: event signup, 2: liked event, 3: became friends, 4: followed)
                $activityType = rand(1, 4);

                switch ($activityType) {
                    case 1: // Signed up for event
                        $event = $events->random();
                        $log = $this->generateEventSignupLog($event, $user);
                        $action = 'event_signup';
                        $objectType = EventDetail::class;
                        $objectId = $event->id;
                        break;

                    case 2: // Liked event
                        $event = $events->random();
                        $log = $this->generateLikedEventLog($event, $user);
                        $action = 'event_like';
                        $objectType = EventDetail::class;
                        $objectId = $event->id;
                        break;

                    case 3: // Became friends
                        $friend = $users->where('id', '!=', $user->id)->random();
                        $log = $this->generateBecameFriendsLog($friend);
                        $action = 'friend';
                        $objectType = User::class;
                        $objectId = $friend->id;
                        break;

                    case 4: // Followed user
                        $followedUser = $users->where('id', '!=', $user->id)->random();
                        $log = $this->generateFollowedLog($followedUser);
                        $action = 'follow';
                        $objectType = User::class;
                        $objectId = $followedUser->id;
                        break;
                }

                $activityLogs[] = [
                    'subject_type' => User::class,
                    'subject_id' => $user->id,
                    'object_type' => $objectType,
                    'object_id' => $objectId,
                    'action' => $action,
                    'log' => $log,
                    'created_at' => $randomDate,
                    'updated_at' => $randomDate,
                ];

                $logCount++;

                // Insert in batches of 100
                if (count($activityLogs) >= 100) {
                    DB::table('activity_logs')->insert($activityLogs);
                    $activityLogs = [];
                }
            }
        }

        // Insert remaining logs
        if (!empty($activityLogs)) {
            DB::table('activity_logs')->insert($activityLogs);
        }

        $this->command->info("Successfully created {$logCount} new activity logs.");
    }

    /**
     * Generate HTML for event signup log
     */
    private function generateEventSignupLog($event, $user): string
    {
        $organizerName = $event->user->name ?? 'Unknown Organizer';
        $organizerId = $event->user->id ?? 0;
        $eventName = $event->eventName ?? 'Unknown Event';
        $eventTier = $event->tier->eventTier ?? 'Free';

        return <<<HTML
            <span class="notification-gray">
                You have signed up for
                <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/organizer/{$organizerId}">
                    {$organizerName}
                </button>'s event,
                <button class="btn-transparent px-0 border-0 Color-{$eventTier}" data-href="/event/{$event->id}">
                    {$eventName}
                </button>
            </span>
        HTML;
    }

    /**
     * Generate HTML for liked event log
     */
    private function generateLikedEventLog($event, $user): string
    {
        $eventName = $event->eventName ?? 'Unknown Event';
        $eventTier = $event->tier->eventTier ?? 'Free';

        return <<<HTML
            <span class="notification-gray">
                You liked Event
                <button class="btn-transparent px-0 border-0 Color-{$eventTier}" data-href="/event/{$event->id}">
                    {$eventName}
                </button>
            </span>
        HTML;
    }

    /**
     * Generate HTML for became friends log
     */
    private function generateBecameFriendsLog($friend): string
    {
        $friendName = $friend->name;

        return <<<HTML
            <span class="notification-gray">
                You became friends with
                <a class="px-0 border-0 " href="/view/participant/{$friend->id}" alt="Friend link">
                    <span class="notification-blue">{$friendName}</span></a>
            </span>
        HTML;
    }

    /**
     * Generate HTML for followed user log
     */
    private function generateFollowedLog($followedUser): string
    {
        $userName = $followedUser->name;
        $userRole = $followedUser->role === 'ORGANIZER' ? 'organizer' : 'participant';
        $roleName = $followedUser->role === 'ORGANIZER' ? 'organizer' : 'player';

        return <<<HTML
            <span class="notification-gray">
                You followed {$roleName}
                <a class="px-0 border-0 " href="/view/{$userRole}/{$followedUser->id}" alt="Follow link">
                    <span class="notification-blue">{$userName}</span></a>
            </span>
        HTML;
    }
}
