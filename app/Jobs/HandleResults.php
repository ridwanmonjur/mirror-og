<?php

namespace App\Jobs;

use App\Models\ActivityLogs;
use App\Models\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


trait FindActivityNotificationTrait
{
    /**
        * @return ActivityLogs
    */
    public function findActivityLog($parameters): ActivityLogs
    
    {
        return ActivityLogs::where([
            'subject_type' => $parameters['subject_type'],
            'object_type' => $parameters['object_type'],
            'subject_id' => $parameters['subject_id'],
            'object_id' => $parameters['object_id'],
            'action' => $parameters['action']
        ]);
    }

    /**
        * @return Notifications
    */
    public function findNotifications($parameters): Notifications
    {
        return Notifications::where([
            'subject_type' => $parameters['subject_type'],
            'object_type' => $parameters['object_type'],
            'subject_id' => $parameters['subject_id'],
            'object_id' => $parameters['object_id'],
            'action' => $parameters['action']
        ]);
    }

    public function createActivityLogs($parameters) {
        ActivityLogs::create([
            'subject_type' => $parameters['subject_type'],
            'object_type' => $parameters['object_type'],
            'subject_id' => $parameters['subject_id'],
            'object_id' => $parameters['object_id'],
            'action' => $parameters['action'],
            'log' => $parameters['log']
        ]);
    }

    public function createNotifications($parameters) {
        Notifications::create([
            'notifiable_type' => $parameters['notifiable_type'],
            'notifiable_id' => $parameters['notifiable_id'],
            'type' => Notifications::class,
            'object_type' => $parameters['object_type'],
            'object_id' => $parameters['object_id'],
            'action' => $parameters['action'],
            'data' => $parameters['data']
        ]);
    }
}

class ChangePositionStrategy
{
    use FindActivityNotificationTrait;

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
        $emoji = [1 => '🥇', 2 => '🥈'];
        $foundLogs = $this->findActivityLog($parameters);
        $foundNotifications = $this->findNotifications($parameters)->get();
        $notificationLog = '<span class="notification-gray"> You achieved ' 
            . $emoji[$parameters['position']] ?? ''
            . $parameters['position'] . bladeOrdinalPrefix($parameters['position'])
            . ' position the team, <span class="notification-black">' . $parameters['teamName'] 
            . '</span>. </span>';

        $activityLog = '<span class="notification-gray"> You achieved ' . $emoji[$parameters['position']] ?? ''
            . $parameters['position']
            . ' position the team, <span class="notification-black">' . $parameters['teamName'] 
            . '</span>. </span>';
        
        if (!$foundLogs) {
            $parameters['log'] = $activityLog;
            $this->createActivityLogs($parameters);
        } else {
            $foundLogs->update([
                'log' => $parameters['log']
            ]);
        }

        $parameters['data'] = [
            'subject' => 'Position updated',
            'data' => $notificationLog,
            'links' => [
                'name' => 'Results page public?',
                'url' => ''
            ]
        ];

        if (!$foundNotifications) {
            $this->createNotifications($parameters);
        } else {
            $foundNotifications->update([
                'data' => $parameters['data']
            ]);
        }
    }
}

class AddAwardStrategy
{
    use FindActivityNotificationTrait;

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
        $notificationLog = '<span class="notification-gray"> You achieved ' 
            . $parameters['award']
            . ' in the team, <span class="notification-black">' . $parameters['teamName'] 
            . '</span>. </span>';

        $activityLog = '<span class="notification-gray"> You achieved '
            . $parameters['award']
            . ' in the team, <span class="notification-black">' . $parameters['teamName'] 
            . '</span>. </span>';
    
        $parameters['log'] = $activityLog;
        $this->createActivityLogs($parameters);
        
        $parameters['data'] = [
            'subject' => 'Position updated',
            'data' => $notificationLog,
            'links' => [
                'name' => 'Results page public?',
                'url' => ''
            ]
        ];

        $this->createNotifications($parameters);
    }
}

class AddAchievementStrategy
{
    use FindActivityNotificationTrait;

    public function handle($parameters)
    {
        // 'subject_type' => User::class,
        // 'object_type' => Achievements::class,
        // 'subject_id' => $user->id,
        // 'object_id' => $rowId,
        // 'action' => 'Position',
        // 'teamName' => $request->teamName
        $notificationLog = '<span class="notification-gray"> You achieved ' 
            . $parameters['award']
            . ' in the team, <span class="notification-black">' . $parameters['teamName'] 
            . '</span>. </span>';

        $activityLog = '<span class="notification-gray"> You achieved '
            . $parameters['award']
            . ' in the team, <span class="notification-black">' . $parameters['teamName'] 
            . '</span>. </span>';

        $parameters['log'] = $activityLog;
        $this->createActivityLogs($parameters);
        
        $parameters['data'] = [
            'subject' => 'Position updated',
            'data' => $notificationLog,
            'links' => [
                'name' => 'Results page public?',
                'url' => ''
            ]
        ];

        $this->createNotifications($parameters);
    }
}


class DeleteAwardStrategy
{
    use FindActivityNotificationTrait;

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
        $this->findActivityLog($parameters)->delete();
    }
}


class DeleteAchievementStrategy
{
    use FindActivityNotificationTrait;

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
        $this->findNotifications($parameters);
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
