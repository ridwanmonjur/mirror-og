<?php

namespace App\Http\Requests\Match;

use App\Models\EventDetail;
use App\Models\JoinEvent;
use App\Models\ParticipantPayment;
use App\Models\RosterMember;
use App\Models\SystemCoupon;
use App\Models\TransactionHistory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class ShowCheckoutRequest extends FormRequest
{
    public ?EventDetail $event;
    public float $total;
    public array $prevForm;
    public float $participantPaymentSum;
    public string|int $id;

    public bool $isEarly;

    public float $amount;
    public array $fee;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teamId' => 'required|exists:teams,id',
            'teamName' => 'required|string',
            'joinEventId' => 'required',
            'memberId' => 'required',
            'amount' => 'required|numeric|min:0',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateRosterMembership($validator);
            $this->validatePaymentAmount($validator);
        });
    }

    private function validateRosterMembership($validator)
    {
        $user = $this->user();

        $prevForm = [
            'id' => $this->input('teamId'),
            'teamName' => $this->input('teamName'),
            'joinEventId' => $this->input('joinEventId'),
            'event_details_id' => $this->input('id'),
            'memberId' => $this->input('memberId')
         ];

        $this->prevForm = $prevForm;

        $this->id = $this->input('id');
        $rosterMember = RosterMember::where([
            'user_id' => $user->id,
            'join_events_id' => $this->joinEventId,
            'team_id' => $this->teamId,
        ])->exists();

        if (!$rosterMember) {
            $validator->errors()->add(
                'roster', 
                "You are a member of {$this->teamName}, but not a member of this event's roster."
            );

            return;
        }
    }

    private function validatePaymentAmount($validator)
    {
        $user = $this->user();

        $joinEvent = JoinEvent::select('id', 'event_details_id', 'payment_status')
            ->find($this->input('joinEventId'))->first();
        if (!$joinEvent) {
            $validator->errors()->add(
                'join', 
                "No team has joined with id: {$this->id}!"
            );
            return;
        }

        $event = EventDetail::with([
                'tier', 'user', 'user.organizer', 'game', 'type',
                'signup:id,event_id,signup_open,normal_signup_start_advanced_close,signup_close'

            ])
            ->where('id', $this->id)->first();
        $this->event = $event;
        if (!$event) {
            $validator->errors()->add(
                'event', 
                "Event with id: {$this->id} can't be found!"
            );
            return;
        }

        $status = $event->getRegistrationStatus();
        if ($status == config('constants.SIGNUP_STATUS.CLOSED')) {
            $validator->errors()->add('time', "Regisration time is now over!");
            return;
        }
        
        if ($status == config('constants.SIGNUP_STATUS.TOO_EARLY')) {
            $validator->errors()->add('time', "Regisration time has not yet started!");
            return;
        }

        if (!$event->tier) {
            $validator->errors()->add(
                'tier', 
                "Event with id: {$this->id} has no event tier chosen"
            );
            return;
        }

        $this->isEarly = $status == config('constants.SIGNUP_STATUS.EARLY');
        $total =  $this->isEarly ? (float) $event->tier->earlyEntryFee : (float) $event->tier->tierEntryFee;        
        $this->total = $total;
        $paymentOptionLower = config("constants.STRIPE.MINIMUM_RM");
        $paymentOptionHigher = $total - $paymentOptionLower;

        $user = $this->user();
        [$fee,] = SystemCoupon::loadCoupon($this->input('coupon_code'), $this->input('amount'), 0.0, 'participant', $user->id);
        // dd($fee);
        $this->fee = $fee;
        $this->amount = $fee['finalFee'];
        if ($this->amount < $paymentOptionLower) {
            $validator->errors()->add(
                'amount',
                "You have tried to pay RM $this->amount. If you don't pay full amount, you have to pay higher than RM $paymentOptionLower."
            );

            return;
        }

        $participantPaymentSum = DB::table('participant_payments')
            ->where('join_events_id', $this->joinEventId)
            ->sum('payment_amount');

        $soFarPaid = $participantPaymentSum + $this->amount;
        $pending = $total - $soFarPaid;
        
        if ($pending > config("constants.STRIPE.ZERO") && $soFarPaid > $paymentOptionHigher) {
            $minimum = ($total - $participantPaymentSum) - $paymentOptionLower ;
            $validator->errors()->add(
                'amount',
                "You have tried to pay RM $this->amount. If you don't pay full amount, you have to pay higher than RM $paymentOptionLower and lower than RM $minimum."
            );

            return;
        }

        if ($this->amount <=  config("constants.STRIPE.ZERO")) {
            if ($this->isEarly) {
                $history = new TransactionHistory([
                    'name' => "{$event->eventName}",
                    'type' => "Top up for Event: RM {$this->amount}",
                    'link' => route('public.event.view', ['id' => $event->id]),
                    'amount' => $this->amount,
                    'summary' => "{$event->game->gameTitle}, {$event->tier->eventTier}, {$event->type->eventType}",
                    'isPositive' => false,
                    'date' => now(),
                    'user_id' => $user->id,
                ]);
            } else {
                $history = new TransactionHistory([
                    'name' => "{$event->eventName}",
                    'type' => "Top up: RM {$this->amount}",
                    'link' => null,
                    'amount' => $this->amount,
                    'summary' => "Wallet RM {$this->amount}",
                    'isPositive' => false,
                    'date' => now(),
                    'user_id' => $user->id,
                ]);
                
            }

            $history?->save();

            ParticipantPayment::create([
                'team_members_id' => $this->input('memberId'),
                'user_id' => $user->Id,
                'join_events_id' => $this->input('joinEventId'),
                'payment_amount' => $this->amount,
                'payment_id' => null,
                'register_time' => $status,
                'history_id' => $history?->id,
                'type' => 'stripe',
            ]);

            if ($pending <= config("constants.STRIPE.ZERO")) {
                $joinEvent->payment_status = 'completed';
                $joinEvent->register_time = $status;
                $joinEvent->save();
            }
        }

        $pendingBeforePayment = $this->total - $participantPaymentSum ;
        if ($pendingBeforePayment < config("constants.STRIPE.ZERO")) {
            $validator->errors()->add(
                'amount',
                "You've already paid for this event!"
            );

            return;
        }

        $this->total = $total;
        $this->participantPaymentSum = $participantPaymentSum;
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, redirect()
            ->route('participant.register.manage', ['id' => $this->teamId])            
            ->with('errorMessage', $validator->errors()->first()));
    }

}
