<?php

namespace App\Listeners;

use App\Events\JoinEventSignuped;
use App\Mail\EventSignupMail;
use App\Models\ActivityLogs;
use App\Models\NotifcationsUser;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class JoinEventSignupListener implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 3; 

    /**
     * 
     * Create the event listener.
     */

    /**
     * Handle the event.
     */
    public function handle(JoinEventSignuped $event2): void
    {
        $memberNotification = []; $memberEmail = [];
        foreach ($event2->selectTeam->members as $member) {
            $html = <<<HTML
                <span class="notification-gray">
                    You have signed up for  
                    <a href="/view/organizer/{$event2->event->user->id}">
                        <span class="notification-blue">{$event2->event->user->name}</span>
                    </a>'s event,
                    <a href="/event/{$event2->event->id}">
                        <span class="notification-blue">{$event2->event->eventName}</span>
                    </a>
                    with your team, 
                    <a href="/view/team/{$event2->selectTeam->id}">
                        <span class="notification-blue">{$event2->selectTeam->teamName}</span></a>. 
                    Please complete and confirm your registration for this event.
                </span>
            HTML;

            $memberNotification[] = [
                'user_id' => $member->user->id,
                'type' => 'teams',
                'link' =>  route('participant.register.manage', ['id' => $event2->selectTeam->id]),
                'icon_type' => 'signup',
                'html' => $html,
            ];

            Mail::to($member->user->email)->send(new EventSignupMail([
                'team' => $event2->selectTeam,
                'text' => $html,
                'link' =>  route('participant.register.manage', ['id' => $event2->selectTeam->id]),
            ]));
        }
            
        NotifcationsUser::insertWithCount($memberNotification);
    }

    public function failed($error): void
    {
        Log::error($error->message());
    }
}
