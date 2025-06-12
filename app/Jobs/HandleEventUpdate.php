<?php

namespace App\Jobs;

use App\Mail\EventRescheduledMail;
use App\Models\JoinEvent;
use App\Models\NotifcationsUser;
use Carbon\Carbon;
use DateTimeZone;
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
            Log::info($this->eventDetail);
            $joinEvent = JoinEvent::where('event_details_id', $this->eventDetail->id)
                ->where('join_status', 'confirmed')
                ->with(['roster', 'roster.user'])
                ->first();
            Log::info($joinEvent);
            
            $memberMail = [];
            $this->eventDetail->load(['user']);

            $malaysiaTimezone = new DateTimeZone('Asia/Kuala_Lumpur');
            $startTimeDate = $this->eventDetail->getDateTz($this->eventDetail->startDate, $this->eventDetail->startTime);
            $endTimeDate = $this->eventDetail->getDateTz($this->eventDetail->endDate, $this->eventDetail->startTime);
            $startTimeDate->setTimezone($malaysiaTimezone);
            $endTimeDate->setTimezone($malaysiaTimezone);
            $partialHtml = <<<HTML
                The event, <button class="btn-transparent px-0 border-0 Color-{$this->eventDetail->tier->eventTier}" data-href="/event/{$this->eventDetail->id}">{$this->eventDetail->eventName}</button> has
                been RESCHEDULED. It starts on {$startTimeDate->format('l, F j, Y')} MYT and ends on {$endTimeDate->format('l, F j, Y')} MYT.
                HTML;

            $partialEmail = <<<HTML
                The date and time for your event, <span class="px-0 border-0 notification-blue">{$this->eventDetail->eventName}</span> has been updated to the following:<br><br>
                <b>Start: {$startTimeDate->format('l, jS F Y')}</b><br> <b>End: {$endTimeDate->format('l, jS F Y')}</b><br><br>
                All registered teams have been notified of this change.
                HTML;

            $memberHtml = <<<HTML
                <span class="notification-gray">
                    {$partialHtml}
                </span>
                HTML;

            $memberEmail = <<<HTML
                <span class="notification-gray">
                    {$partialEmail}
                </span>
                HTML;


            if ($joinEvent) {
                
                DB::beginTransaction();
                try {
                  
                    $notificationMap = [
                        'member' => [
                            'type' => 'event',
                            'link' =>  route('public.event.view', ['id' => $this->eventDetail->id]),
                            'icon_type' => 'schedule',
                            'html' => $memberHtml,
                            'mail' => $memberEmail,
                            'created_at' => DB::raw('NOW()')
                        ], 
                        'organizer' => [
                            'type' => 'event',
                            'link' =>  route('public.event.view', ['id' => $this->eventDetail->id]),
                            'icon_type' => 'schedule',
                            'html' => $memberHtml,
                            'mail' => $memberEmail,
                            'created_at' => DB::raw('NOW()')
                        ]
                    ];

                    $participantEmail = new EventRescheduledMail([
                        'text' => $notificationMap['member']['mail'],
                        'link' =>  $notificationMap['member']['link'],
                    ]);


                    $memberNotification =  [];
                    foreach ($joinEvent->roster as $member) {
                        $memberNotification[] = [
                            'user_id' => $member->user->id,
                            'type' => $notificationMap['member']['type'],
                            'link' =>  $notificationMap['member']['link'],
                            'icon_type' => $notificationMap['member']['icon_type'],
                            'html' => $notificationMap['member']['html'],
                            'created_at' => DB::raw('NOW()')
                        ];

                        if ($member->user->email) {
                            $memberMail[] = $member->user->email;
                        } 
                    }

                    Mail::to($memberMail)->send($participantEmail);

                    
                    NotifcationsUser::insertWithCount($memberNotification);

                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error($e->getMessage() . $e->getTraceAsString());
                }
            } else {
                $notificationMap = [
                    'organizer' => [
                        'type' => 'event',
                        'link' =>  route('public.event.view', ['id' => $this->eventDetail->id]),
                        'icon_type' => 'schedule',
                        'html' => $memberHtml,
                        'mail' => $memberEmail,
                        'created_at' => DB::raw('NOW()')
                    ]
                ];

                $organizerEmail = new EventRescheduledMail([
                    'text' => $notificationMap['organizer']['mail'],
                    'link' =>  $notificationMap['organizer']['link'],
                ]);

                if ($this->eventDetail->user->email) Mail::to($this->eventDetail->user->email)->send($organizerEmail);
                
                $organizerNotification = [
                    'user_id' => $joinEvent->eventDetails->user_id,
                    'type' => $notificationMap['organizer']['type'],
                    'link' =>  $notificationMap['organizer']['link'],
                    'icon_type' => $notificationMap['organizer']['icon_type'],
                    'html' => $notificationMap['organizer']['html'],
                    'created_at' => DB::raw('NOW()')
                ];
                

                NotifcationsUser::insertWithCount([
                    $organizerNotification
                ]);
                
            }
        } catch (Exception $e) {
            Log::error($e->getMessage() . $e->getTraceAsString());
        }
    }
}
