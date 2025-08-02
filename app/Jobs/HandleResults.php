<?php

namespace App\Jobs;

use App\Mail\EventResultMail;
use App\Models\ActivityLogs;
use App\Models\EventDetail;
use App\Models\NotifcationsUser;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ChangePositionStrategy
{
    public function ordinalPrefix($number)
    {
        $number = intval($number);

        if ($number % 100 >= 11 && $number % 100 <= 13) {
            return $number.'th';
        }

        switch ($number % 10) {
            case 1:
                return $number.'st';
            case 2:
                return $number.'nd';
            case 3:
                return $number.'rd';
            default:
                return $number.'th';
        }
    }

    public function handle($parameters)
    {
        try {
            [
                'teamId' => $teamId,
                'image' => $image,
                'team' => $team,
                'eventId' => $eventId,
                'joinEvent' => $joinEvent
            ] = $parameters;

            $joinEvent->load(['roster', 'roster.user']);
            Log::info($parameters['position']);

            $event = EventDetail::where(['id' => $eventId])->select(['id', 'eventName'])->firstOrFail();

            $positionString = $this->ordinalPrefix($parameters['position']);

            $foundLogs = ActivityLogs::findActivityLog($parameters)->get();
            $notificationLog = <<<HTML
                <span>
                    <span class="notification-gray"> You achieved 
                    <span class="notification-other"><span class="notification-{$parameters['position']}">
                        {$positionString}</span></span> position with your team,
                    <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/$teamId" alt="Team Link">
                        {$parameters['teamName']}</button> in the event, <button class="btn-transparent px-0 border-0 notification-entity" data-href="/event/$event->id" alt="Event Link">
                        {$event->eventName}</button>. 
                </span>
            HTML;

            $activityLog = <<<HTML
                <span>
                    <a class="px-0 border-0 notification-blue" href="/view/team/$teamId" alt="Team View">
                        <img src="/storage/$image" 
                            width="30" height="30"
                            onerror="this.src='/assets/images/404.png';"
                            class="object-fit-cover rounded-circle me-2"
                            alt="Event View"
                        ></a>
                    <span class="notification-gray"> You achieved 
                    {$positionString} position with your team,
                    <a class="px-0 border-0" href="/view/team/$teamId" alt="Team Link">
                        <span class="notification-blue">{$parameters['teamName']}</span></a> in the event, 
                        <a class="px-0 border-0" href="/event/$event->id" alt="Event Link">
                        <span class="notification-blue">{$event->eventName}</span></a>. 
                </span>
            HTML;

            if (isset($foundLogs[0])) {
                if ($parameters['position'] > 0) {
                    foreach ($foundLogs as $foundLog) {
                        $foundLog->update([
                            'log' => $activityLog,
                        ]);
                    }
                } else {
                    foreach ($foundLogs as $foundLog) {
                        $foundLog->delete();
                    }
                }
            } else {
                if ($parameters['position'] > 0) {
                    $parameters['log'] = $activityLog;
                    ActivityLogs::createActivityLogs($parameters);
                }
            }

            $memberMailable = new EventResultMail([
                'team' => $team,
                'text' => $notificationLog,
                'link' => route('public.team.view', ['id' => $team->id]).'#Positions',
            ]);

            foreach ($joinEvent->roster as $member) {
                $memberNotification[] = [
                    'user_id' => $member->user->id,
                    'type' => 'teams',
                    'link' =>  route('public.team.view', ['id' => $team->id]).'#Positions',
                    'icon_type' => 'trophy',
                    'html' => $notificationLog,
                    'created_at' => DB::raw('NOW()'),
                ];

                if ($member->user->email) {
                    $memberMail[] = $member->user->email;
                }
            }
            Mail::to($memberMail)->send($memberMailable);
            NotifcationsUser::insertWithCount($memberNotification);

        } catch (Exception $e) {
            Log::error($e->getMessage().PHP_EOL.$e->getTraceAsString());
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

            $notificationLog = <<<HTML
                <span class="notification-gray"> You achieved 
                {$parameters['award']} position in the team,
                <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/$teamId" alt="Team Link">
                    {$parameters['teamName']}
                </button>. 
            </span>
            HTML;

            $activityLog = <<<HTML
                <span>
                    <a class=" px-0 border-0 notification-blue" href="/view/team/$teamId" alt="Team View">
                        <img src="/storage/$image" 
                            width="30" height="30"
                            onerror="this.src='/assets/images/404.png';"
                            class="object-fit-cover rounded-circle me-2"
                            alt="Event View"
                        ></a>
                    <span class="notification-gray"> You achieved 
                    {$parameters['award']} position in the team,
                    <a class=" px-0 border-0" href="/view/team/$teamId" alt="Team Link">
                        <span class="notification-blue">{$parameters['teamName']}</span></a>. 
                </span>
            HTML;

            $parameters['log'] = $activityLog;
            ActivityLogs::createActivityLogs($parameters);

            $parameters['data'] = [
                'subject' => 'Award added',
                'data' => $notificationLog,
                'links' => [[
                    'url' => route('public.event.view', ['id' => $parameters['eventId']]),
                    'name' => 'View event',
                ],
                ],
            ];

        } catch (Exception $e) {
            Log::error($e->getMessage().PHP_EOL.$e->getTraceAsString());
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

            $notificationLog = <<<HTML
                <span>
                <span class="notification-gray"> You achieved {$parameters['achievement']} in the team, 
                    <span class="notification-entity">{$parameters['teamName']}</span>.
                </span>
            HTML;

            $activityLog = <<<HTML
                <span class="notification-gray"> You achieved {$parameters['achievement']} in the team, 
                <a class=" px-0 border-0" href="/view/team/$teamId" alt="Team Link">
                    <span class="notification-blue">{$parameters['teamName']}</span></a>.
                </span>
            HTML;

            $parameters['log'] = $activityLog;
            ActivityLogs::createActivityLogs($parameters);

            $parameters['data'] = [
                'subject' => 'Achievement added',
                'data' => $notificationLog,
                'links' => [[
                    'url' => route('public.event.view', ['id' => $parameters['eventId']]),
                    'name' => 'View event',
                ],
                ],
            ];

        } catch (Exception $e) {
            Log::error($e->getMessage().PHP_EOL.$e->getTraceAsString());
        }
    }
}

class DeleteAwardStrategy
{
    public function handle($parameters)
    {
        try {
            ActivityLogs::findActivityLog($parameters)->delete();
        } catch (Exception $e) {
            Log::error($e->getMessage().PHP_EOL.$e->getTraceAsString());
        }
    }
}

class DeleteAchievementStrategy
{
    public function handle($parameters)
    {
        try {
            ActivityLogs::findActivityLog($parameters)->delete();
        } catch (Exception $e) {
            Log::error($e->getMessage().PHP_EOL.$e->getTraceAsString());
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
        $strategyClass = __NAMESPACE__.'\\'.$this->strategy.'Strategy';

        if (! class_exists($strategyClass)) {
            throw new \InvalidArgumentException("Strategy class {$strategyClass} does not exist.");
        }
        $strategy = new $strategyClass;
        $strategy->handle($this->parameters);
    }
}
