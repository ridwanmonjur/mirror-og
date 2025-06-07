<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Match\ShowCheckoutRequest;
use App\Models\EventDetail;
use App\Models\JoinEvent;
use App\Models\ParticipantPayment;
use App\Models\RecordStripe;
use App\Models\RosterMember;
use App\Models\StripeConnection;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\View\View;
use App\Http\Requests\Match\DiscountCheckoutRequest;
use App\Models\TransactionHistory;
use Illuminate\Support\Facades\Log;

class ParticipantCheckoutController extends Controller
{
    private $stripeClient;

    public function __construct(StripeConnection $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function showCheckout(ShowCheckoutRequest $request): RedirectResponse|View
    {
        try {
            session()->forget(['successMessageCoupon', 'errorMessageCoupon']);

            $user = $request->attributes->get('user');
            $user->stripe_customer_id = $user->organizer()->value('stripe_customer_id');

            $user_wallet = DB::table('user_wallet')->where('user_id', $user->id)->first();

            $paymentMethods = $this->stripeClient->retrieveAllStripePaymentsByCustomer([
                'customer' => $user->stripe_customer_id,
            ]);

            $discountStatusEnums = config('constants.DISCOUNT_STATUS');
            $discountStatus = is_null($user_wallet) || $user_wallet?->usable_balance < 0 ? $discountStatusEnums['ABSENT'] : $discountStatusEnums['COMPLETE'];
            $payment_amount_min = $pendingAfterDiscount = 0.0;
            if ($discountStatus != $discountStatusEnums['ABSENT']) {
                $payment_amount_min = $request->amount;
                if ($user_wallet->usable_balance < $request->amount) {
                    $payment_amount_min = min($user_wallet->usable_balance, $request->total - $request->participantPaymentSum - config('constants.STRIPE.MINIMUM_RM'));

                    $discountStatus = $discountStatusEnums['PARTIAL'];
                    $pendingAfterDiscount = $request->total - ($request->participantPaymentSum + $payment_amount_min);
                    if ($pendingAfterDiscount < config('constants.STRIPE.MINIMUM_RM')) {
                        $discountStatus = $discountStatusEnums['INVALID'];
                    }
                }
            }

            return view('Participant.CheckoutEvent', [
                'teamId' => $request->teamId,
                'amount' => $request->amount,
                'teamName' => $request->teamName,
                'joinEventId' => $request->joinEventId,
                'memberId' => $request->memberId,
                'event' => $request->event,
                'user_wallet' => $user_wallet,
                'isUser' => true,
                'livePreview' => 1,
                'paymentMethods' => $paymentMethods,
                'payment_amount_min' => $payment_amount_min,
                'discountStatusEnums' => $discountStatusEnums,
                'discountStatus' => $discountStatus,
                'paymentLowerMin' => config('constants.STRIPE.MINIMUM_RM'),
            ]);
        } catch (Exception $e) {
            return back()->with('errorMessage', $e->getMessage())->with('scroll', $request->joinEventId);
        }
    }

    public function walletCheckout(DiscountCheckoutRequest $request)
    {
        $user = $request->attributes->get('user');
        DB::beginTransaction();
        try {
            $userWallet = $request->attributes->get('user_wallet');
            $newAmount = $request->attributes->get('new_amount_after_discount');
            $isCompletePayment = $request->attributes->get('complete_payment');
            $regStatus = $request->getStatus();
            $isNormalReg = $regStatus == config('constants.SIGNUP_STATUS.NORMAL');
            $event = $request->event;

            $pending_total_after_discount = $request->attributes->get('pending_total_after_discount');
            $walletAmount = $userWallet->usable_balance - $request->discount_applied_amount;
            $currentAmount = $userWallet->current_balance - $request->discount_applied_amount;
            $transaction = null;
            DB::table('user_wallet')
                ->where('user_id', $user->id)
                ->update([
                    'usable_balance' => $walletAmount,
                    'current_balance' => $currentAmount,
                ]);

            if ($isNormalReg) {
                $transaction = new TransactionHistory([
                    'name' => "Entry Fee Hold: RM {$event->eventName}",
                    'type' => 'Event Entry Fee Hold',
                    'link' => route('public.event.view', ['id' => $event->id]),
                    'amount' => $request->discount_applied_amount,
                    'summary' => "User Wallet RM {$request->discount_applied_amount}",
                    'isPositive' => false,
                    'date' => DB::raw('NOW()'),
                    'user_id' => $user->id,
                ]);

                $transaction->save();
            } else {
                $transaction = new TransactionHistory([
                    'name' => "Entry Fee: RM {$event->eventName}",
                    'type' => 'Event Entry Fee',
                    'link' => route('public.event.view', ['id' => $event->id]),
                    'amount' => $request->discount_applied_amount,
                    'summary' => "{$event->game->gameTitle}, {$event->tier->eventTier}, {$event->type->eventType}",
                    'isPositive' => false,
                    'date' => DB::raw('NOW()'),
                    'user_id' => $user->id,
                ]);

                $transaction->save();
            }

            if (!$isCompletePayment) {
                if ($request->payment_intent_id) {
                    $stripePaymentIntent = $this->stripeClient->retrieveStripePaymentByPaymentId($request->payment_intent_id);
                    $this->stripeClient->updatePaymentIntent($stripePaymentIntent->id, [
                        'amount' => $newAmount * 100,
                    ]);
                }
            }

            ParticipantPayment::create([
                'team_members_id' => $request->member_id,
                'user_id' => $user->id,
                'join_events_id' => $request->joinEventId,
                'payment_amount' => $request->discount_applied_amount,
                'register_time' => $regStatus,
                'history_id' => $transaction?->id,
                'type' => 'wallet',
            ]);

            if ($isCompletePayment) {
                if ($pending_total_after_discount < 0.1) {
                    $joinEvent = $request->joinEvent;
                    $joinEvent->payment_status = 'completed';
                    $joinEvent->register_time = $regStatus;
                    $joinEvent->save();
                    // add column for registration
                }
            }

            DB::commit();

            $message = $isCompletePayment ? 'Discount applied successfully. You have completed this payment.' : "Discount applied. You have RM {$newAmount} to pay.";

            return redirect()
                ->route('participant.register.manage', ['id' => $request->teamId])
                ->with('successMessage', $message)
                ->with('scroll', $request->joinEventId);
        } catch (Exception $e) {
            DB::rollBack();

            if ($request->payment_intent_id) {
                $stripePaymentIntent = $this->stripeClient->retrieveStripePaymentByPaymentId($request->payment_intent_id);
                $this->stripeClient->updatePaymentIntent($stripePaymentIntent->id, [
                    'amount' => $request->amount * 100,
                ]);
            }

            return redirect()
                ->route('participant.register.manage', ['id' => $request->teamId])
                ->with('errorMessage', $e->getMessage())
                ->with('scroll', $request->joinEventId);
        }
    }

    public function showCheckoutTransition(Request $request)
    {
        $paymentIntent = [];
        DB::beginTransaction();
        try {
            $user = $request->get('user');
            $userId = $user->id;
            $status = $request->get('redirect_status');
            if (($status === 'succeeded' || $status === 'requires_capture') && $request->has('payment_intent_client_secret')) {
                $intentId = $request->get('payment_intent');
                $paymentIntent = $this->stripeClient->retrieveStripePaymentByPaymentId($intentId);
                $paymentDone = (float) $paymentIntent['amount'] / 100;

                if ($paymentIntent['amount'] > 0 && ($paymentIntent['amount_capturable'] === $paymentIntent['amount'] || $paymentIntent['amount_received'] === $paymentIntent['amount'])) {
                    $joinEvent = JoinEvent::select('id', 'event_details_id', 'payment_status')->findOrFail($paymentIntent['metadata']['joinEventId']);

                    $participantPaymentSum = ParticipantPayment::select(['join_events_id', 'id', 'payment_amount'])
                        ->where('join_events_id', $joinEvent->id)
                        ->sum('payment_amount');

                    $transaction = RecordStripe::createTransaction($intentId, $paymentIntent['status'], $paymentDone);

                    $event = EventDetail::select(['id', 'eventName', 'event_tier_id', 'event_type_id', 'event_category_id'])
                        ->where('id', $joinEvent->event_details_id)
                        ->with(['tier', 'type', 'game', 'signup:id,event_id,signup_open,normal_signup_start_advanced_close,signup_close'])
                        ->first();

                    $regStatus = $event->getRegistrationStatus();
                    $total = $regStatus == config('constants.SIGNUP_STATUS.EARLY') ? (float) (float) $event->tier->earlyEntryFee : (float) $event->tier->tierEntryFee;

                    $history = null;
                    $isNormalReg = $regStatus == config('constants.SIGNUP_STATUS.NORMAL');

                    if ($isNormalReg) {
                        $history = new TransactionHistory([
                            'name' => "Top up for event: {$event->eventName}",
                            'type' => "Top up: RM {$paymentDone}",
                            'link' => null,
                            'amount' => $paymentDone,
                            'summary' => "Wallet RM $paymentDone",
                            'isPositive' => false,
                            'date' => DB::raw('NOW()'),
                            'user_id' => $user->id,
                        ]);
                    } else {
                        $history = new TransactionHistory([
                            'name' => "Payment for {$event->eventName}",
                            'type' => "Top up for Event: RM $paymentDone",
                            'link' => route('public.event.view', ['id' => $event->id]),
                            'amount' => $paymentDone,
                            'summary' => "{$event->game->gameTitle}, {$event->tier->eventTier}, {$event->type->eventType}",
                            'isPositive' => false,
                            'date' => DB::raw('NOW()'),
                            'user_id' => $user->id,
                        ]);
                    }

                    $history?->save();

                    ParticipantPayment::create([
                        'team_members_id' => $paymentIntent['metadata']['memberId'],
                        'user_id' => $userId,
                        'join_events_id' => $paymentIntent['metadata']['joinEventId'],
                        'payment_amount' => $paymentDone,
                        'payment_id' => $transaction->id,
                        'register_time' => $regStatus,
                        'history_id' => $history?->id,
                        'type' => 'stripe',
                    ]);

                    if ($total - ($participantPaymentSum + $paymentDone) < 0.1) {
                        $joinEvent->payment_status = 'completed';
                        $joinEvent->register_time = $regStatus;
                        $joinEvent->save();
                    }

                    // try {
                    //     $customerId = $paymentIntent['customer'] ?? null;
                        
                    //     if ($customerId) {
                    //         $$this->stripeClient->createStripeInvoice($customerId);
                    //     }
                        
                    // } catch (Exception $invoiceException) {
                    //     Log::error("Failed to create invoice after payment", [
                    //         'error' => $invoiceException->getMessage(),
                    //         'payment_intent_id' => $intentId
                    //     ]);
                    // }

                    DB::commit();

                    return redirect()
                        ->route('participant.register.manage', ['id' => $paymentIntent['metadata']['teamId']])
                        ->with('successMessage', 'Your payment has succeeded!')
                        ->with('scroll', $paymentIntent['metadata']['joinEventId']);
                }
            } 

            if ($request->has('payment_intent')) {
                try {
                    $intentId = $request->get('payment_intent');
                    $paymentIntent = $this->stripeClient->retrieveStripePaymentByPaymentId($intentId);
                    $cancelableStatuses = [
                        'requires_payment_method',
                        'requires_confirmation',
                        'requires_action',
                        'processing'
                    ];

                    if (in_array($paymentIntent['status'], $cancelableStatuses)) {
                        $this->stripeClient->cancelPaymentIntent($intentId, [
                            'cancellation_reason' => 'abandoned'
                        ]);
                    }
                } catch (Exception $e) {
                }
            }
            
            DB::rollBack();

            return $this->showErrorParticipant('Your payment has failed unfortunately!');
        } catch (ModelNotFoundException | UnauthorizedException $e) {
            DB::rollBack();

            return $this->showErrorParticipant($e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();

            return $this->showErrorParticipant($e->getMessage());
        }
    }
}
