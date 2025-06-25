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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
        $memberNotification = []; 
        $memberMail = [];
     
        foreach ($event2->selectTeam->members as $member) {

            $notifHtml = <<<HTML
                <span class="notification-gray">
                    You have signed up for  
                    <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/organizer/{$event2->event->user->id}">
                        {$event2->event->user->name}
                    </button>'s event,
                    <button class="btn-transparent px-0 border-0 Color-{$event2->event->tier->eventTier}" data-href="/event/{$event2->event->id}">
                        {$event2->event->eventName}
                    </button>
                    with your team, 
                    <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$event2->selectTeam->id}">
                        {$event2->selectTeam->teamName}</button>. 
                    Please complete and confirm your registration for this event.
                </span>
            HTML;

            $notifEmailPart = <<<HTML
            <p><b>Hi, {$member->user->name}.</b><br><br></p>
            HTML;

            $notifEmail = $notifEmailPart . $notifHtml;


            $memberNotification[] = [
                'user_id' => $member->user->id,
                'type' => 'teams',
                'link' =>  route('participant.register.manage', [
                        'id' => $event2->selectTeam->id,
                        'scroll' => $event2->join_id
                    ]
                ),
                'icon_type' => 'signup',
                'html' => $notifHtml,
                'created_at' => DB::raw('NOW()')
            ];

            if ($member->user->email) {
                $memberMail[] = $member->user->email;
            }
        }

        Mail::to($memberMail)->send(new EventSignupMail([
            'team' => $event2->selectTeam,
            'text' => $notifEmail,
            'link' =>  route('participant.register.manage', [
                'id' => $event2->selectTeam->id,
                'scroll' => $event2->join_id
            ]),
        ]));

        NotifcationsUser::insertWithCount($memberNotification);
    }

    public function failed($event, $exception): void
    {
        Log::error('JoinEventSignupListener failed', [
            'exception' => $exception->getMessage(),
            'team_member_id' => $this->teamMember->id ?? null,
            'stack_trace' => $exception->getTraceAsString()
        ]);
    }
}
