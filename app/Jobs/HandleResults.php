<?php

namespace App\Jobs;

use App\Models\ActivityLogs;
use App\Models\Notifications;
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
        // dispatch(new HandleResults('ChangePosition', [
        //     'subject_type' => User::class,
        //     'object_type' => EventJoinResults::class,
        //     'subject_id' => $user->id,
        //     'object_id' => $existingRowId,
        //     'action' => 'Position',
        //     'teamName' => $request->teamName
        // ]));
        Log::info($parameters);
        $emoji = [1 => '🥇', 2 => '🥈'];
        $activityLogX = new ActivityLogs;
        $notificationX = new Notifications;
        $foundLogs = $activityLogX->findActivityLog($parameters)->get();
        $foundNotifications = $notificationX->findNotifications($parameters)->get();
        $notificationLog = '<span class="notification-gray"> You achieved ' 
            // . isset($emoji[$parameters['position']]) ? $emoji[$parameters['position']] : ''
            . $parameters['position'] . bladeOrdinalPrefix($parameters['position'])
            . ' position the team, <span class="notification-black">' . $parameters['teamName'] 
            . '</span>. </span>';

        $activityLog = '<span class="notification-gray"> You achieved ' 
            // . $emoji[$parameters['position']] ?? ''
            . $parameters['position'] . bladeOrdinalPrefix($parameters['position'])
            . ' position the team, <span class="notification-black">' . $parameters['teamName'] 
            . '</span>. </span>';
        
        if (!$foundLogs) {
            $parameters['log'] = $activityLog;
            $activityLogX->createActivityLogs($parameters);
        } else {
            foreach ($foundLogs as $foundLog) {
                $foundLog->update([
                    'log' => $activityLog
                ]);
            }
        }

        $parameters['data'] = [
            'subject' => 'Position updated',
            'data' => $notificationLog,
            'links' => [[
                'url' => route('public.event.view' , ['id' => $parameters['eventId']] ),
                'name' => 'View event'
            ]]
        ];

        if (!$foundNotifications) {
            $notificationX->createNotifications($parameters);
        } else {
            foreach ($foundNotifications as $foundNotification) {
                $foundNotification->update([
                    'data' => $parameters['data']
                ]);
            }
        }
    }
}

class AddAwardStrategy
{
    
    public function handle($parameters)
    {
        // dispatch(new HandleResults('AddAward', [
        //     'subject_type' => User::class,
        //     'object_type' => AwardResults::class,
        //     'subject_id' => $user->id,
        //     'object_id' => $rowId,
        //     'action' => 'Position',
        //     'teamName' => $request->teamName
        // ]));
        $notificationX = new Notifications;
        $activityLogX = new ActivityLogs;
        $notificationLog = '<span class="notification-gray"> You achieved ' 
            . $parameters['award']
            . ' in the team, <span class="notification-black">' . $parameters['teamName'] 
            . '</span>. </span>';

        $activityLog = '<span class="notification-gray"> You achieved '
            . $parameters['award']
            . ' in the team, <span class="notification-black">' . $parameters['teamName'] 
            . '</span>. </span>';
    
        $parameters['log'] = $activityLog;
        $activityLogX->createActivityLogs($parameters);
        
        $parameters['data'] = [
            'subject' => 'Award added',
            'data' => $notificationLog,
            'links' => [[
                'url' => route('public.event.view' , ['id' => $parameters['eventId']] ),
                'name' => 'View event'
            ]]
        ];

        $notificationX->createNotifications($parameters);
    }
}

class AddAchievementStrategy
{
    
    public function handle($parameters)
    {
        // 'subject_type' => User::class,
        // 'object_type' => Achievements::class,
        // 'subject_id' => $user->id,
        // 'object_id' => $rowId,
        // 'action' => 'Position',
        // 'teamName' => $request->teamName
        $notificationX = new Notifications;
        $activityLogX = new ActivityLogs;
        $notificationLog = '<span class="notification-gray"> You achieved ' 
            . $parameters['achievement']
            . ' in the team, <span class="notification-black">' . $parameters['teamName'] 
            . '</span>. </span>';

        $activityLog = '<span class="notification-gray"> You achieved '
            . $parameters['achievement']
            . ' in the team, <span class="notification-black">' . $parameters['teamName'] 
            . '</span>. </span>';

        $parameters['log'] = $activityLog;
        $activityLogX->createActivityLogs($parameters);
        
        $parameters['data'] = [
            'subject' => 'Achievement added',
            'data' => $notificationLog,
            'links' => [[
                'url' => route('public.event.view' , ['id' => $parameters['eventId']] ),
                'name' => 'View event'
            ]]
        ];

        $notificationX->createNotifications($parameters);
    }
}


class DeleteAwardStrategy
{
    

    public function handle($parameters)
    {
        // dispatch(new HandleResults('DeleteAward', [
        //     'subject_type' => User::class,
        //     'object_type' => AwardResults::class,
        //     'subject_id' => $user->id,
        //     'object_id' => $row->id,
        //     'action' => 'Position',
        //     'teamName' => $request->teamName
        // ]));
        $activityLogX = new ActivityLogs;
        $activityLogX->findActivityLog($parameters)->delete();
    }
}


class DeleteAchievementStrategy
{
    public function handle($parameters)
    {
        // dispatch(new HandleResults('DeleteAchievement', [
        //     'subject_type' => User::class,
        //     'object_type' => AwardResults::class,
        //     'subject_id' => $user->id,
        //     'object_id' => $row->id,
        //     'action' => 'Position',
        //     'teamName' => $request->teamName
        // ]));
        $notificationX = new Notifications;
        $notificationX->findNotifications($parameters)->delete();
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
        $strategyClass = __NAMESPACE__ . '\\' . $this->strategy . 'Strategy';

        if (!class_exists($strategyClass)) {
            throw new \InvalidArgumentException("Strategy class {$strategyClass} does not exist.");
        }
        $strategy = new $strategyClass;
        $strategy->handle($this->parameters);
    }
}
