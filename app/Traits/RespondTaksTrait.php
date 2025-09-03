<?php

namespace App\Traits;

use App\Models\ActivityLogs;
use App\Models\BracketDeadline;
use App\Models\Discount;
use App\Models\EventDetail;
use App\Models\EventJoinResults;
use App\Models\EventTier;
use App\Models\JoinEvent;
use App\Models\NotifcationsUser;
use App\Models\SystemCoupon;
use App\Models\UserCoupon;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\RecordStripe;
use App\Models\StripeConnection;
use App\Models\Task;
use App\Models\TransactionHistory;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

trait RespondTaksTrait
{
    protected $stripeService;

    protected $childTaskId;

    protected function initializeTrait(StripeConnection $stripeService, $taskId)
    {
        $this->stripeService = $stripeService;
        $this->childTaskId = $taskId;
    }

    public function checkAndCancelEvent(EventDetail $event)
    {
        try {
            if ($event->join_events_count < intval($event->tier->tierTeamSlot)) {
                if ($event->status != 'FAILED') {
                    $joinEvents = JoinEvent::where('event_details_id', $event->id)
                        ->where('join_status', 'confirmed')
                        ->with(['eventDetails:id,eventName,startDate,startTime,event_tier_id,user_id', 'payments.history', 'payments.transaction'])
                        ->get();
                    $this->releaseFunds($event, $joinEvents);

                    $paidEvents = JoinEvent::where('event_details_id', $event->id)
                        ->where('payment_status', 'completed')
                        ->whereNot('join_status', 'confirmed')
                        ->with(['eventDetails:id,eventName,startDate,startTime,event_tier_id,user_id', 'payments.history', 'payments.transaction'])
                        ->get();
                    $this->releaseFunds($event, $paidEvents);

                    $systemCoupon = new SystemCoupon([
                        'code' => "ORGREFUND{$event->id}",
                        'amount' => 100,
                        'description' => "Refund for failed event id: {$event->id}",
                        'is_active' => true,
                        'is_public' => false,
                        'discount_type' => 'percent',
                        'expires_at' => Carbon::now()->addYears(1),
                        'for_type' => 'organizer',
                        'redeemable_count' => 1,
                    ]);

                    $systemCoupon->save();

                    $event->update(['status' => 'FAILED']);
                    $deadlines = BracketDeadline::where('event_details_id', $event->id)->get();
                    $deadlinesPast = $deadlines->pluck('id');
                    Task::whereIn('taskable_id', $deadlinesPast)->where('taskable_type', 'Deadline')->delete();
                    Task::where('taskable_id', $event->id)->where('taskable_type', 'EventDetail')->delete();
                    BracketDeadline::where('event_details_id', $event->id)->delete();
                    Log::info("Event successfully cancelled and cleaned up. Event ID: {$event->id}");
                }

                return true;
            }
            Log::info("Event does not need cancellation. Event ID: {$event->id}");

            return false;
        } catch (Exception $e) {
            Log::error("Error in checkAndCancelEvent for Event ID {$event->id}: ".$e->getMessage());

            return false;
        }
    }

    public function releaseFunds(EventDetail $event, Collection $joinEvents)
    {
        try {
            foreach ($joinEvents as $join) {

                DB::beginTransaction();
                try {
                    foreach ($join->payments as $participantPay) {

                        try {
                            if ($join->register_time == config('constants.SIGNUP_STATUS.NORMAL')) {
                                if ($participantPay->transaction && $participantPay->type == 'stripe') {
                                    $paymentIntent = $this->stripeService->retrieveStripePaymentByPaymentId($participantPay->transaction->payment_id);

                                    if ($paymentIntent->status == 'requires_capture') {
                                        $paymentIntent->cancel();
                                    }

                                    RecordStripe::where([
                                        'id' => $participantPay->payment_id,
                                    ])->update(['payment_status' => 'canceled']);
                                } elseif ($participantPay->type == 'wallet') {
                                    $wallet = Wallet::retrieveOrCreateCache($participantPay->user_id);

                                   
                                    if ($wallet) {
                                        $wallet->update([
                                            'usable_balance' => $wallet->usable_balance + $participantPay->payment_amount,
                                            'current_balance' => $wallet->current_balance + $participantPay->payment_amount, // Note: probably want current_balance here, not usable_balance
                                        ]);
                                    }
                                }

                                if ($participantPay->history) {
                                    TransactionHistory::where('id', $participantPay->history->id)->delete();
                                    $participantPay->history_id = null;
                                    $participantPay->save();
                                }
                            } else {
                                if ($participantPay->type == 'wallet') {
                                    $wallet = Wallet::retrieveOrCreateCache($participantPay->user_id);
                                    Log::info($wallet);

                                   

                                    Log::info($wallet);
                                    if ($wallet) {

                                        $wallet->update([
                                            'usable_balance' => $wallet->usable_balance + $participantPay->payment_amount,
                                            'current_balance' => $wallet->current_balance + $participantPay->payment_amount, // Note: probably want current_balance here, not usable_balance
                                        ]);
                                    }


                                    Log::info($participantPay);

                                    if ($participantPay->history) {
                                        TransactionHistory::where('id', $participantPay->history->id)->delete();
                                        $participantPay->history_id = null;
                                        $participantPay->save();
                                    }
                                } else {
                                    Log::info('entered here');
                                    // Check if a coupon with the same code already exists
                                    $existingCoupon = SystemCoupon::where('for_type', 'participant')
                                        ->where('code', "REFUNDRM{$participantPay->payment_amount}EVT{$event->id}")
                                        ->first();

                                    if (! $existingCoupon) {
                                        $systemCoupon = new SystemCoupon([
                                            'code' => "REFUNDRM{$participantPay->payment_amount}EVT{$event->id}",
                                            'amount' => $participantPay->payment_amount,
                                            'description' => 'Refund from organizers',
                                            'is_active' => true,
                                            'is_public' => false,
                                            'expires_at' => Carbon::now()->addYears(1),
                                            'for_type' => 'participant',
                                            'redeemable_count' => 1,
                                        ]);

                                        $systemCoupon->save();
                                    } else {
                                        $systemCoupon = $existingCoupon;
                                    }

                                    $existingUserCoupon = UserCoupon::where('user_id', $participantPay->user_id)->where('coupon_id', $systemCoupon->id)->first();

                                    if (! $existingUserCoupon) {
                                        $newUserCoupon = new UserCoupon([
                                            'user_id' => $participantPay->user_id,
                                            'coupon_id' => $systemCoupon->id,
                                            'redeemed_at' => null,
                                            'redeemable_count' => 0,
                                        ]);

                                        $newUserCoupon->save();

                                    } else {
                                        $existingUserCoupon->increment('redeemable_count');
                                    }
                                }
                            }
                        } catch (Exception $e) {

                            Log::error("Error in releaseFunds for JOIN ID {$join->id}: && PAY ID {$participantPay->id}");
                            $this->logError($this->childTaskId, $e);
                            throw $e;
                        }
                    }
                    DB::commit();
                } catch (Exception $e) {

                    DB::rollback();
                    Log::error("Error in releaseFunds for Join Event ID {$join->id}");
                    $this->logError($this->childTaskId, $e);
                    throw $e;
                }
            }
        } catch (Exception $e) {
            $this->logError($this->childTaskId, $e);

            return [
                'released_payments' => 0,
                'created_discounts' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function capturePayments($event, $joinEvents)
    {
        $joins = $joinEvents->where('register_time', config('constants.SIGNUP_STATUS.NORMAL'));
        Log::info('<<< PaymentData : '.$joinEvents);
        Log::info('<<< startedEvents : '.$event);

        foreach ($joins as $join) {

            DB::beginTransaction();
            try {
                foreach ($join->payments as $participantPay) {

                    try {
                        if ($participantPay->payment_id && $participantPay->type == 'stripe') {
                            // stripe capture hold
                            $paymentIntent = $this->stripeService->retrieveStripePaymentByPaymentId($participantPay->transaction->payment_id);

                            if ($paymentIntent->status === 'requires_capture') {
                                $paymentIntent->capture();
                            }

                            RecordStripe::where([
                                'id' => $participantPay->payment_id,
                            ])->update(['payment_status' => 'succeeded']);
                        }

                        if (isset($participantPay->history)) {
                            TransactionHistory::where([
                                'id' => $participantPay->history->id,
                            ])->update([
                                'name' => "{$join->eventDetails->eventName}: Entry Fee",
                                'type' => 'Event Entry Fee',
                                'isPositive' => false,
                                'link' => route('public.event.view', ['id' => $join->eventDetails->id]),
                            ]);
                        }

                        Log::error("Payment processed successfully for participant payment ID: {$participantPay->id}");
                    } catch (Exception $e) {
                        Log::error("Error processing participant payment ID {$participantPay->id}");
                        $this->logError($this->childTaskId, $e);
                        throw $e;
                    }
                }
                DB::commit();
            } catch (Exception $e) {
                Log::error("Error processing join ID {$join->id} ");
                $this->logError($this->childTaskId, $e);
                DB::rollBack();
                throw $e;
            }
        }

        Log::info('Payment capture process completed');
    }

    public function getLiveNotifications($joinList)
    {
        $playerNotif = [];
        $orgNotif = [];
        foreach ($joinList as $join) {

            try {
                $memberHtml = <<<HTML
                <span class="notification-gray">
                    <button class="btn-transparent px-0 border-0  Color-{$join->eventDetails->tier->eventTier}" data-href="/event/{$join->eventDetails->id}">
                    {$join->eventDetails->eventName}</button> is now live.
                    </span>
                HTML;
                $memberEmail = <<<HTML
                <span class="notification-gray">
                    <span class="btn-transparent px-0 border-0 " >
                    <span class="notification-blue">{$join->eventDetails->eventName}</span></span> is now live.
                    </span>
                HTML;
                $playerNotif[$join->id] = [
                    'type' => 'event',
                    'link' => route('public.event.view', ['id' => $join->eventDetails->id]),
                    'icon_type' => 'live',
                    'html' => $memberHtml,
                    'mail' => $memberEmail,
                    'mailClass' => 'EventLiveMail',
                    'subject' => 'An event you joined has gone live.',
                    'created_at' => DB::raw('NOW()'),
                ];

                if (! isset($orgNotif[$join->eventDetails->user_id])) {
                    $orgNotif[$join->eventDetails->user_id] = [
                        'type' => 'event',
                        'link' => route('public.event.view', ['id' => $join->eventDetails->id]),
                        'icon_type' => 'live',
                        'html' => $memberHtml,
                        'user_id' => $join->eventDetails->user_id,
                        'user' => $join->eventDetails->user,
                        'mail' => $memberEmail,
                        'mailClass' => 'EventLiveMail',
                        'subject' => 'Your event has gone live.',
                        'created_at' => DB::raw('NOW()'),
                    ];
                }
            } catch (Exception $e) {
                Log::error("Error processing function getLiveNotifications join ID {$join->id} ");
                $this->logError($this->childTaskId, $e);
            }
        }

        return [$playerNotif, $orgNotif];
    }

    public function getStartedNotifications($joinList)
    {
        $playerNotif = [];
        $orgNotif = [];
        foreach ($joinList as $join) {

            try {
                $memberHtml = <<<HTML
                <span class="notification-gray">
                    <button class="btn-transparent px-0 border-0 Color-{$join->eventDetails->tier->eventTier}" data-href="/event/{$join->eventDetails->id}">
                    {$join->eventDetails->eventName}</button> has started and is now underway.
                    </span>
                HTML;

                $memberEmail = <<<HTML
                <span class="notification-gray">
                    <a class="btn-transparent px-0 border-0" href="/event/{$join->eventDetails->id}">
                    <span class="notification-blue">{$join->eventDetails->eventName}</span></a> has started and is now underway.
                    </span>
                HTML;

                $playerNotif[$join->id] = [
                    'type' => 'event',
                    'link' => route('public.event.view', ['id' => $join->eventDetails->id]),
                    'icon_type' => 'started',
                    'html' => $memberHtml,
                    'mail' => $memberEmail,
                    'mailClass' => 'EventStartMail',
                    'subject' => 'An event you joined has started.',
                    'created_at' => DB::raw('NOW()'),
                ];

                if (! isset($orgNotif[$join->eventDetails->user_id])) {
                    $orgNotif[$join->eventDetails->user_id] = [
                        'type' => 'event',
                        'link' => route('public.event.view', ['id' => $join->eventDetails->id]),
                        'icon_type' => 'started',
                        'html' => $memberHtml,
                        'user_id' => $join->eventDetails->user_id,
                        'user' => $join->eventDetails->user,
                        'mail' => $memberEmail,
                        'mailClass' => 'EventStartMail',
                        'subject' => 'Your event has started.',
                        'created_at' => DB::raw('NOW()'),
                    ];
                }
            } catch (Exception $e) {
                Log::error("Error processing function getStartedNotifications join ID {$join->id}: ");
                $this->logError($this->childTaskId, $e);
            }
        }

        return [$playerNotif, $orgNotif];
    }

    public function handleEventTypes($notificationMap, $joinList)
    {
        try {
            [$playerNotif, $orgNotif] = $notificationMap;

            if (! empty($joinList)) {
                DB::beginTransaction(); //
                try {
                    $memberNotification = $organizerNotification = [];
                    foreach ($joinList as $join) {

                        try {
                            if (! isset($playerNotif[$join->id])) {
                                continue;
                            }

                            $memberMailClass = 'App\\Mail\\'.$playerNotif[$join->id]['mailClass'];

                            if (! class_exists($memberMailClass)) {
                                throw new \InvalidArgumentException("Strategy class {$memberMailClass} does not exist.");
                            }

                            $memberMailInvocation = new $memberMailClass([
                                'text' => $playerNotif[$join->id]['mail'],
                                'link' => $playerNotif[$join->id]['link'],
                                'subject' => $playerNotif[$join->id]['subject'],
                            ]);

                            $memberEmails = collect($join->roster)
                                ->map(function ($member) {
                                    return $member->user && $member->user->email ? $member->user->email : null;
                                })
                                ->filter()
                                ->all();

                            foreach ($join->roster as $member) {

                                try {
                                    $memberNotification[] = [
                                        'user_id' => $member->user->id,
                                        'type' => $playerNotif[$join->id]['type'],
                                        'link' => $playerNotif[$join->id]['link'],
                                        'icon_type' => $playerNotif[$join->id]['icon_type'],
                                        'html' => $playerNotif[$join->id]['html'],
                                        'created_at' => DB::raw('NOW()'),
                                    ];

                                    if ($member->user->email) {
                                        $memberEmails[] = $member->user->email;
                                    }
                                } catch (Exception $e) {
                                    $this->logError($this->childTaskId, $e);
                                    throw $e;
                                }
                            }

                            if (! empty($memberEmails)) {
                                Mail::to($memberEmails)->send($memberMailInvocation);
                            }
                        } catch (Exception $e) {
                            $this->logError($this->childTaskId, $e);
                        }
                    }

                    foreach ($orgNotif as $notification2) {

                        try {
                            $organizerNotification[] = [
                                'user_id' => $notification2['user_id'],
                                'type' => $notification2['type'],
                                'link' => $notification2['link'],
                                'icon_type' => $notification2['icon_type'],
                                'html' => $notification2['html'],
                                'created_at' => DB::raw('NOW()'),
                            ];

                            $orgMailClass = 'App\\Mail\\'.$notification2['mailClass'];

                            if (! class_exists($orgMailClass)) {
                                throw new \InvalidArgumentException("Strategy class {$orgMailClass} does not exist.");
                            }

                            $orgMailInvocation = new $orgMailClass([
                                'text' => $notification2['mail'],
                                'link' => $notification2['link'],
                                'subject' => $notification2['subject'],
                            ]);

                            $user = $notification2['user'];
                            if ($user && $user->email) {
                                Mail::to($user->email)->send($orgMailInvocation);
                            }
                        } catch (Exception $e) {
                            $this->logError($this->childTaskId, $e);
                            throw $e;
                        }
                    }

                    NotifcationsUser::insertWithCount([...$memberNotification, ...$organizerNotification]);
                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    $this->logError($this->childTaskId, $e);
                    throw $e;
                }
            }
        } catch (Exception $e) {
            $this->logError($this->childTaskId, $e);
        }
    }

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

    public function handlePrizeAndActivityLogs($logMap, $joinList, $memberIdMap, $prizeDetails)
    {
        $processedCount = 0;
        $activityDeletedCount = 0;
        $activityCreatedCount = 0;
        $transactionWalletCount = 0;
        $errorCount = 0;

        if (! empty($joinList)) {
            foreach ($joinList as $join) {

                DB::beginTransaction();
                try {
                    if (isset($memberIdMap[$join->id]) && isset($memberIdMap[$join->id]) && isset($memberIdMap[$join->id][0]) && isset($logMap[$join->id])) {
                        ActivityLogs::findActivityLog([
                            'object_type' => EventJoinResults::class,
                            'subject_id' => $memberIdMap[$join->id],
                            'object_id' => $join->id,
                            'action' => 'Position',
                        ])->delete();
                        $activityDeletedCount++;

                        ActivityLogs::createActivityLogs([...$logMap[$join->id], 'subject_id' => $memberIdMap[$join->id]]);
                        $activityCreatedCount++;
                    }

                    $transactionHistory = [];
                    $walletData = [];

                    if (isset($prizeDetails[$join->id])) {
                        foreach ($join->roster as $member) {
                            if ($member->user->id){
                                $transactionHistory[] = [
                                    'user_id' => $member->user->id,
                                    'name' => "Prize Money: RM {$prizeDetails[$join->id]}",
                                    'type' => 'Prize Money',
                                    'link' => null,
                                    'amount' => $prizeDetails[$join->id],
                                    'summary' => 'Prize Money for event',
                                    'date' => DB::raw('NOW()'),
                                ];
    
                                $walletData[] = [
                                    'user_id' => $member->user->id,
                                    'usable_balance' => $prizeDetails[$join->id],
                                    'current_balance' => $prizeDetails[$join->id],
                                ];
                            }
                          
                        }
                    }

                    if (! empty($transactionHistory)) {
                        TransactionHistory::insert($transactionHistory);
                    }

                    if (! empty($walletData)) {
                        foreach ($walletData as $data) {
                            if (empty($data['user_id'])) {
                                Log::error('Wallet data has null user_id', ['data' => $data]);
                                continue;
                            }
                            
                            $wallet = Wallet::where('user_id', $data['user_id'])->first();
                            
                            if ($wallet) {
                                $wallet->increment('usable_balance', $data['usable_balance']);
                                $wallet->increment('current_balance', $data['current_balance']);
                            } else {
                                Wallet::create([
                                    'user_id' => $data['user_id'],
                                    'usable_balance' => $data['usable_balance'],
                                    'current_balance' => $data['current_balance']
                                ]);
                            }
                        }
                    }

                    $processedCount++;

                    if (! empty($transactionHistory) || ! empty($walletData)) {
                        $transactionWalletCount++;
                    }
                    DB::commit();
                } catch (Exception $e) {
                    $errorCount++;
                    DB::rollBack();
                    Log::error('1 activity & prize execution failed', [
                        'join_id' => $join->id ?? 'unknown',
                        'error' => $e->getMessage(),
                        'processed_before_failure' => $processedCount,
                    ]);

                    $this->logError($this->childTaskId, $e);
                    throw $e;
                }
            }
        } else {
            Log::info('No joins to process for prize distribution');
        }
    }

    private function getPrizeFromPositionTIer(string|int $position, EventTier $tier)
    {
        $tierPrize = $tier->prizes()
            ->where('position', $position)
            ->first();

        return $tierPrize ? $tierPrize->prize_sum : null;
    }

    public function getEndedNotifications(Collection $joinList, EventTier $tier)
    {
        $playerNotif = [];
        $orgNotif = [];
        $memberIdList = [];
        $prize = [];
        $logs = [];
        foreach ($joinList as $join) {

            try {
                if ($join->position?->position) {
                    $position = $join->position->position;
                    $currentPrize = $this->getPrizeFromPositionTier($position, $tier);
                    if ($currentPrize) {
                        $prize[$join->id] = $currentPrize;
                    }

                    $positionString = $this->ordinalPrefix($position);
                    $memberHtml = <<<HTML
                    <span class="notification-gray">
                        <button class="btn-transparent px-0 border-0  Color-{$join->eventDetails->tier->eventTier}" data-href="/event/{$join->eventDetails->id}">
                        {$join->eventDetails->eventName}</button> has now ended. You achieved
                        <span class="notification-other"><span class="notification-{$join->position}">
                            {$positionString}</span></span> position with your team,
                        <button class="btn-transparent px-0 border-0 notification-entity"
                            data-href="/view/team/{$join->team->id}" alt="Team Link"
                        >
                            {$join->team->teamName}</button>.
                        </span>
                    HTML;

                    $activityLog = <<<HTML
                        <span>
                            <a class="px-0 border-0" href="/view/team/{$join->team->id}" alt="Team View">
                                <img src="/storage/{$join->team->teamBanner}"
                                    width="30" height="30"
                                    onerror="this.src='/assets/images/404.png';"
                                    class="object-fit-cover rounded-circle me-2"
                                    alt="Event View"
                                ></a>
                            <span class="notification-gray"> You achieved
                            {$positionString} position with your team,
                            <a class="px-0 border-0" href="/view/team/{$join->team->id}" alt="Team Link">
                                <span class="notification-blue">{$join->team->teamName}</span></a> in the event,
                                <a class="px-0 border-0" href="/event/{$join->eventDetails->id}" alt="Event Link">
                                <span class="notification-blue">{$join->eventDetails->eventName}</span></a>.
                        </span>
                    HTML;

                    $memberEmail = <<<HTML
                    <span class="notification-gray">
                        <span class="px-0 border-0 notification-blue" >
                        {$join->eventDetails->eventName}</span> has now ended.  You achieved
                        <span>{$positionString}</span> position with your team,
                        <span class="px-0 border-0 notification-blue" >{$join->team->teamName}</span>.
                    </span>
                    HTML;
                } else {
                    $memberHtml = <<<HTML
                    <span class="notification-gray">
                        <button class="btn-transparent px-0 border-0  Color-{$join->eventDetails->tier->eventTier}" data-href="/event/{$join->eventDetails->id}">
                        {$join->eventDetails->eventName}</button> has now ended.
                    </span>
                    HTML;

                    $activityLog = null;

                    $memberEmail = <<<HTML
                    <span class="notification-gray">
                        <span class="px-0 border-0 notification-blue" >
                        {$join->eventDetails->eventName}</span> has now ended.
                    </span>
                    HTML;
                }

                $orgHtml = <<<HTML
                <span class="notification-gray">
                    <button class="btn-transparent px-0 border-0  Color-{$join->eventDetails->tier->eventTier}" data-href="/event/{$join->eventDetails->id}">
                    {$join->eventDetails->eventName}</button> has now ended.
                </span>
                HTML;

                $orgEmail = <<<HTML
                <span class="notification-gray">
                    <span class="px-0 border-0 notification-blue" >
                    {$join->eventDetails->eventName}</span> has now ended.
                </span>
                HTML;

                if ($activityLog) {
                    $logs[$join->id] = [
                        'subject_type' => User::class,
                        'object_type' => EventJoinResults::class,
                        'object_id' => $join->id,
                        'action' => 'Position',
                        'log' => $activityLog,
                    ];
                }

                $playerNotif[$join->id] = [
                    'type' => 'event',
                    'link' => route('public.event.view', ['id' => $join->eventDetails->id]),
                    'icon_type' => 'ended',
                    'html' => $memberHtml,
                    'mail' => $memberEmail,
                    'mailClass' => 'EventEndMail',
                    'subject' => 'An event you joined has ended.',
                    'created_at' => DB::raw('NOW()'),
                ];

                if (! isset($orgNotif[$join->eventDetails->user_id])) {
                    $orgNotif[$join->eventDetails->user_id] = [
                        'type' => 'event',
                        'link' => route('public.event.view', ['id' => $join->eventDetails->id]),
                        'icon_type' => 'ended',
                        'html' => $orgHtml,
                        'mail' => $orgEmail,
                        'user_id' => $join->eventDetails->user_id,
                        'user' => $join->eventDetails->user,
                        'mailClass' => 'EventEndMail',
                        'subject' => 'Your event has ended.',
                        'created_at' => DB::raw('NOW()'),
                    ];
                }

                $members = $join->roster?->pluck('user_id')->toArray() ?? [];
                $memberIdList[$join->id] = $members;
            } catch (Exception $e) {
                $this->logError($this->childTaskId, $e);
            }
        }

        return [$playerNotif, $orgNotif, $logs, $memberIdList, $prize];
    }
}
