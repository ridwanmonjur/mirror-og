<?php

namespace App\Jobs;

use App\Mail\EventRescheduledMail;
use App\Models\JoinEvent;
use App\Models\NotifcationsUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class HandleEventUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $eventDetail;

    public function __construct($eventDetail)
    {
        $this->eventDetail = $eventDetail;
    }

    // Simple Strategy
    public function handle()
    {
        try {
            $joinEvent = JoinEvent::where('event_details_id', $this->eventDetail->id)
                ->where('join_status', 'confirmed')
                ->with(['members', 'members.user'])
                ->first();
            

            $this->eventDetail->load('user');

            if ($joinEvent) {
                $startTimeDate = $this->eventDetail->createCarbonDateTime($this->eventDetail->startDate, $this->eventDetail->startTime);
                $endTimeDate = $this->eventDetail->createCarbonDateTime($this->eventDetail->endDate, $this->eventDetail->startTime);

                DB::beginTransaction();
                try {
                    $partialEmail = "The event, {$this->eventDetail->eventName}</button>  starts in {$startTimeDate->diffForHumans()} at {$startTimeDate->format('g:i A')}. 
                    It ends in {$endTimeDate->diffForHumans()} at {$endTimeDate->format('g:i A')}.";

                    $memberHtml = <<<HTML
                        <span class="notification-gray">
                            <button class="btn-transparent px-0 border-0 notification-blue" data-href="/event/{$this->eventDetail->id}">
                            {$partialEmail}</button>
                            </span>
                        HTML;

                    $memberEmail = <<<HTML
                        <span class="notification-gray">
                            <a class="px-0 border-0 notification-blue" href="/event/{$this->eventDetail->id}">
                            {$partialEmail}</a>
                        </span>
                        HTML;

                    $notificationMap = [
                        'member' => [
                            'type' => 'event',
                            'link' =>  route('public.event.view', ['id' => $this->eventDetail->id]),
                            'icon_type' => 'ended',
                            'html' => $memberHtml,
                            'mail' => $memberEmail,
                        ], 
                        'organizer' => [
                            'type' => 'event',
                            'link' =>  route('public.event.view', ['id' => $this->eventDetail->id]),
                            'icon_type' => 'ended',
                            'html' => $memberHtml,
                            'mail' => $memberEmail,
                        ]
                    ];

                    $organizerEmail = new EventRescheduledMail([
                        'text' => $notificationMap['organizer']['mail'],
                        'link' =>  $notificationMap['organizer']['link'],
                    ]);

                    $participantEmail = new EventRescheduledMail([
                        'text' => $notificationMap['member']['mail'],
                        'link' =>  $notificationMap['member']['link'],
                    ]);


                    $memberNotification = $organizerNotification = [];
                    foreach ($joinEvent->members as $member) {
                        $memberNotification[] = [
                            'user_id' => $member->user->id,
                            'type' => $notificationMap['member']['type'],
                            'link' =>  $notificationMap['member']['link'],
                            'icon_type' => $notificationMap['member']['icon_type'],
                            'html' => $notificationMap['member']['html'],
                        ];

                        Mail::to($member->user->email)->send($participantEmail);
                    }

                    $organizerNotification = [
                        'user_id' => $joinEvent->eventDetails->user_id,
                        'type' => $notificationMap['organizer']['type'],
                        'link' =>  $notificationMap['organizer']['link'],
                        'icon_type' => $notificationMap['organizer']['icon_type'],
                        'html' => $notificationMap['organizer']['html'],
                    ];
                    
                    Mail::to($this->eventDetail->user->email)->send($organizerEmail);

                    NotifcationsUser::insertWithCount([
                        ...$memberNotification,
                        $organizerNotification
                    ]);
                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e->getMessage() . $e->getTraceAsString());
                }
            }
        } catch (Exception $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
        }
    }
}
