<?php

namespace App\Jobs;

use App\Models\ActivityLogs;
use App\Models\Notifications;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ChangePositionStrategy
{
    public function handle($parameters)
    {
        try {
            [
                'teamId' => $teamId,
                'image' => $image,
            ] = $parameters;

            $emojiMap = [1 => '🥇 (1st position)', 2 => '🥈 (2nd position)'];
            $activityLogX = new ActivityLogs();
            $notificationX = new Notifications();
            $positionString = intval(bladeOrdinalPrefix($parameters['position']));
            if (isset($emojiMap[$parameters['position']])) {
                $positionString = $emojiMap[$parameters['position']];
            }

            $foundLogs = $activityLogX->findActivityLog($parameters)->get();
            $foundNotifications = $notificationX->findNotifications($parameters)->get();
            $notificationLog = <<<HTML
                <span>
                    <span class="notification-gray"> You achieved 
                    {$positionString} position in the team,
                    <a href="/event/$teamId" alt="Team Link">
                        <span class="notification-blue">{$parameters['teamName']}</span>
                    </a>. 
                </span>
            HTML;

            $activityLog = <<<HTML
                <span>
                    <a href="/event/$teamId" alt="Team View">
                        <img src="/storage/$image" 
                            width="30" height="30"
                            onerror="this.src='/assets/images/404.png';"
                            class="object-fit-cover rounded-circle me-2"
                            alt="Event View"
                        >
                    </a>
                    <span class="notification-gray"> You achieved 
                    {$positionString} position in the team,
                    <a href="/event/$teamId" alt="Team Link">
                        <span class="notification-blue">{$parameters['teamName']}</span>
                    </a>. 
                </span>
            HTML;

            if (isset($foundLogs[0])) {
                foreach ($foundLogs as $foundLog) {
                    $foundLog->update([
                        'log' => $activityLog,
                    ]);
                }
            } else {
                $parameters['log'] = $activityLog;
                $activityLogX->createActivityLogs($parameters);
            }

            $parameters['data'] = [
                'subject' => 'Position updated',
                'data' => $notificationLog,
                'links' => [[
                    'url' => route('public.event.view', ['id' => $parameters['eventId']]),
                    'name' => 'View event',
                ],
                ],
            ];

            if (isset($foundNotifications[0])) {
                foreach ($foundNotifications as $foundNotification) {
                    $foundNotification->update([
                        'data' => $parameters['data'],
                    ]);
                }
            } else {
                $notificationX->createNotifications($parameters);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}

class AddAwardStrategy
{
    public function handle($parameters)
    {
        try {
            [
                'teamId' => $teamId,
                'image' => $image,
            ] = $parameters;

            $notificationX = new Notifications();
            $activityLogX = new ActivityLogs();
            $notificationLog = <<<HTML
                <span class="notification-gray"> You achieved {$parameters['award']} in the team, 
                    <span class="notification-black">{$parameters['teamName']}</span>.
                </span>
                <span>
                <a href="/event/$teamId" alt="Team View">
                    <img src="/storage/$image" 
                        width="30" height="30"
                        onerror="this.src='/assets/images/404.png';"
                        class="object-fit-cover rounded-circle me-2"
                        alt="Event View"
                    >
                </a>
                <span class="notification-gray"> You achieved 
                {$parameters['award']} position in the team,
                <a href="/event/$teamId" alt="Team Link">
                    <span class="notification-blue">{$parameters['teamName']}</span>
                </a>. 
            </span>
            HTML;

            $activityLog = <<<HTML
                <span>
                    <a href="/event/$teamId" alt="Team View">
                        <img src="/storage/$image" 
                            width="30" height="30"
                            onerror="this.src='/assets/images/404.png';"
                            class="object-fit-cover rounded-circle me-2"
                            alt="Event View"
                        >
                    </a>
                    <span class="notification-gray"> You achieved 
                    {$parameters['award']} position in the team,
                    <a href="/event/$teamId" alt="Team Link">
                        <span class="notification-blue">{$parameters['teamName']}</span>
                    </a>. 
                </span>
            HTML;

            $parameters['log'] = $activityLog;
            $activityLogX->createActivityLogs($parameters);

            $parameters['data'] = [
                'subject' => 'Award added',
                'data' => $notificationLog,
                'links' => [[
                    'url' => route('public.event.view', ['id' => $parameters['eventId']]),
                    'name' => 'View event',
                ],
                ],
            ];

            $notificationX->createNotifications($parameters);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}

class AddAchievementStrategy
{
    public function handle($parameters)
    {
        try {
            [
                'teamId' => $teamId,
                'image' => $image,
            ] = $parameters;
            
            $notificationX = new Notifications();
            $activityLogX = new ActivityLogs();
            $notificationLog = <<<HTML
                <span>
                    <a href="/event/$teamId" alt="Team View">
                        <img src="/storage/$image" 
                            width="30" height="30"
                            onerror="this.src='/assets/images/404.png';"
                            class="object-fit-cover rounded-circle me-2"
                            alt="Event View"
                        >
                    </a>
                    <span class="notification-gray"> You achieved 
                    {$parameters['award']} position in the team,
                    <a href="/event/$teamId" alt="Team Link">
                        <span class="notification-blue">{$parameters['teamName']}</span>
                    </a>. 
                </span>
                <span class="notification-gray"> You achieved {$parameters['achievement']} in the team, 
                    <span class="notification-black">{$parameters['teamName']}</span>.
                </span>
            HTML;

            $activityLog = <<<HTML
                <span>
                    <a href="/event/$teamId" alt="Team View">
                        <img src="/storage/$image"
                            width="30" height="30" 
                            onerror="this.src='/assets/images/404.png';"
                            class="object-fit-cover rounded-circle me-2"
                            alt="Event View"
                        >
                    </a>
                    <span class="notification-gray"> You achieved 
                    {$parameters['award']} position in the team,
                    <a href="/event/$teamId" alt="Team Link">
                        <span class="notification-blue">{$parameters['teamName']}</span>
                    </a>. 
                </span>
                <span class="notification-gray"> You achieved {$parameters['achievement']} in the team, 
                    <span class="notification-black">{$parameters['teamName']}</span>.
                </span>
            HTML;

            $parameters['log'] = $activityLog;
            $activityLogX->createActivityLogs($parameters);

            $parameters['data'] = [
                'subject' => 'Achievement added',
                'data' => $notificationLog,
                'links' => [[
                    'url' => route('public.event.view', ['id' => $parameters['eventId']]),
                    'name' => 'View event',
                ],
                ],
            ];

            $notificationX->createNotifications($parameters);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}

class DeleteAwardStrategy
{
    public function handle($parameters)
    {
        try {
            $activityLogX = new ActivityLogs();
            $activityLogX->findActivityLog($parameters)->delete();
            $notificationX = new Notifications();
            $notificationX->findNotifications($parameters)->delete();
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}

class DeleteAchievementStrategy
{
    public function handle($parameters)
    {
        try {
            $activityLogX = new ActivityLogs();
            $activityLogX->findActivityLog($parameters)->delete();
            $notificationX = new Notifications();
            $notificationX->findNotifications($parameters)->delete();
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}

class HandleResults implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $strategy;

    protected $parameters;

    public function __construct($strategy, $parameters)
    {
        $this->strategy = $strategy;
        $this->parameters = $parameters;
    }

    // Simple Strategy
    public function handle()
    {
        Log::info($this->parameters);
        $strategyClass = __NAMESPACE__.'\\'.$this->strategy.'Strategy';

        if (! class_exists($strategyClass)) {
            throw new \InvalidArgumentException("Strategy class {$strategyClass} does not exist.");
        }
        $strategy = new $strategyClass();
        $strategy->handle($this->parameters);
    }
}
