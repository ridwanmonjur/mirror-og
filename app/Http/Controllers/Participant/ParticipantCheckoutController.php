<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Match\ShowCheckoutRequest;
use App\Models\EventDetail;
use App\Models\JoinEvent;
use App\Models\Wallet;
use App\Models\ParticipantPayment;
use App\Models\RecordStripe;
use App\Models\StripeConnection;
use App\Models\SystemCoupon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\View\View;
use App\Http\Requests\Match\WalletCheckoutRequest;
use App\Models\TransactionHistory;

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
            $user = $request->attributes->get('user');
            $prevForm = $request->prevForm;
            if ($request->paymentDone) {
                $message = '100% discount achieved through coupon.';
                session()->flash('successMessage', $message);

                return redirect()
                    ->route('participant.register.manage', ['id' => $prevForm['id'], 'scroll' => $request->joinEventId])
                    ->with('successMessage', $message)
                    ->with('scroll', $request->joinEventId);

            }

            $fee = $request->fee;
            $user = $request->attributes->get('user');
            $user->stripe_customer_id = $user->organizer()->value('stripe_customer_id');

            $user_wallet = Wallet::retrieveOrCreateCache($user->id);

            $paymentMethods = $this->stripeClient->retrieveAllStripePaymentsByCustomer([
                'customer' => $user->stripe_customer_id,
            ]);

            $remaining_after_participant_payment = $request->total - $request->participantPaymentSum;
            $has_wallet_balance = ! is_null($user_wallet) && $user_wallet->usable_balance > 0;
            $can_pay_full_amount = $has_wallet_balance && $user_wallet->usable_balance >= $remaining_after_participant_payment;
            $wallet_shortfall = $has_wallet_balance ? max(0, $remaining_after_participant_payment - $user_wallet->usable_balance) : $remaining_after_participant_payment;

            return view('Participant.CheckoutEvent', [
                'teamId' => $request->teamId,
                'amount' => number_format($request->amount, 2),
                'teamName' => $request->teamName,
                'joinEventId' => $request->joinEventId,
                'memberId' => $request->memberId,
                'event' => $request->event,
                'user_wallet' => $user_wallet,
                'isUser' => true,
                'fee'=> $fee,
                'couponStatus' => $request->couponStatus,
                'prevForm' => $prevForm,
                'livePreview' => 1,
                'paymentMethods' => $paymentMethods,
                'has_wallet_balance' => $has_wallet_balance,
                'can_pay_full_amount' => $can_pay_full_amount,
                'wallet_shortfall' => $wallet_shortfall,
                'remaining_amount' => $remaining_after_participant_payment,
                'paymentLowerMin' => config('constants.STRIPE.MINIMUM_RM'),
            ]);
        } catch (Exception $e) {
            session()->flash('errorMessage', $e->getMessage());

            return back()->with('errorMessage', $e->getMessage())->with('scroll', $request->joinEventId);
        }
    }

    public function walletCheckout(WalletCheckoutRequest $request)
    {
        $user = $request->attributes->get('user');
        DB::beginTransaction();
        try {

            $userWallet = $request->attributes->get('user_wallet');
            $newAmount = $request->attributes->get('stripe_after_wallet');
            $isCompletePayment = $request->attributes->get('complete_payment');
            $regStatus = $request->getStatus();
            $transaction = null;
            $isNormalReg = $regStatus == config('constants.SIGNUP_STATUS.NORMAL');
            $event = $request->event;
            $pending_total_after_wallet = $request->attributes->get('pending_total_after_wallet');

            $walletAmount = $userWallet->usable_balance - $request->wallet_to_decrement;
            $currentAmount = $userWallet->current_balance - $request->wallet_to_decrement;
            $userWallet = Wallet::retrieveOrCreateCache($user->id);
            $userWallet->update(['usable_balance' => $walletAmount,
                'current_balance' => $currentAmount,
            ]);

            if ($isNormalReg) {
                $transaction = new TransactionHistory([
                    'name' => "{$event->eventName}",
                    'type' => 'Event Entry Fee Hold',
                    'link' => route('public.event.view', ['id' => $event->id]),
                    'amount' => $request->wallet_to_decrement,
                    'summary' => "User Wallet RM {$request->wallet_to_decrement}",
                    'isPositive' => false,
                    'date' => now(),
                    'user_id' => $user->id,
                ]);

                $transaction->save();
            } else {
                $transaction = new TransactionHistory([
                    'name' => "{$event->eventName}",
                    'type' => 'Event Entry Fee',
                    'link' => route('public.event.view', ['id' => $event->id]),
                    'amount' => $request->wallet_to_decrement,
                    'summary' => "{$event->game->gameTitle}, {$event->tier->eventTier}, {$event->type->eventType}",
                    'isPositive' => false,
                    'date' => now(),
                    'user_id' => $user->id,
                ]);

                $transaction->save();
            }

            ParticipantPayment::create([
                'team_members_id' => $request->member_id,
                'user_id' => $user->id,
                'join_events_id' => $request->joinEventId,
                'payment_amount' => $request->wallet_to_decrement,
                'register_time' => $regStatus,
                'history_id' => $transaction?->id,
                'type' => 'wallet',
            ]);

            ['coupon' => $coupon] = $request->couponDetails;
            $coupon?->validateAndIncrementCoupon();

            if ($isCompletePayment) {
                if ($pending_total_after_wallet <= config('constants.STRIPE.ZERO')) {
                    $joinEvent = $request->joinEvent;
                    $joinEvent->completePayment($regStatus);
                }
            }

            DB::commit();

            $message = $isCompletePayment ? 'Wallet payment applied successfully.' : "Partial payment via wallet applied. You have RM {$newAmount} to pay.";

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
                $paymentMethodId = $paymentIntent['payment_method'];
                $paymentMethod = $this->stripeClient->retrievePaymentMethod($paymentMethodId);

                $paymentDone = (float) $paymentIntent['amount'] / 100;

                if ($paymentIntent['amount'] > 0 && ($paymentIntent['amount_capturable'] === $paymentIntent['amount'] || $paymentIntent['amount_received'] === $paymentIntent['amount'])) {
                    $joinEvent = JoinEvent::select('id', 'event_details_id', 'payment_status')->findOrFail($paymentIntent['metadata']['joinEventId']);

                    $participantPaymentSum = ParticipantPayment::select(['join_events_id', 'id', 'payment_amount'])
                        ->where('join_events_id', $joinEvent->id)
                        ->sum('payment_amount');

                    $couponCode = $paymentIntent['metadata']['coupon_code'] ?? null;
                    if ($couponCode) {
                        [$fee, , ,$coupon] = SystemCoupon::loadCoupon($couponCode, $paymentIntent['metadata']['totalFee'], 0.0, 'participant', $user->id);

                        $coupon?->validateAndIncrementCoupon($couponCode, $user->id);
                    }

                    $transaction = RecordStripe::createTransaction($paymentIntent, $paymentMethod, $user->id, $request->query('saveDefault'), $request->query('savePayment'));

                    $event = EventDetail::select(['id', 'eventName', 'event_tier_id', 'event_type_id', 'event_category_id'])
                        ->where('id', $joinEvent->event_details_id)
                        ->with(['tier', 'type', 'game', 'signup:id,event_id,signup_open,normal_signup_start_advanced_close,signup_close'])
                        ->first();

                    $regStatus = $event->getRegistrationStatus();
                    $total = $regStatus == config('constants.SIGNUP_STATUS.EARLY') ? (float) $event->tier->earlyEntryFee : (float) $event->tier->tierEntryFee;

                    $history = null;
                    $isNormalReg = $regStatus == config('constants.SIGNUP_STATUS.NORMAL');

                    if ($isNormalReg) {
                        $history = new TransactionHistory([
                            'name' => "{$event->eventName}",
                            'type' => "Top up: RM {$paymentDone}",
                            'link' => null,
                            'amount' => $paymentDone,
                            'summary' => "Wallet RM $paymentDone",
                            'isPositive' => false,
                            'date' => now(),
                            'user_id' => $user->id,
                        ]);
                    } else {
                        $history = new TransactionHistory([
                            'name' => "{$event->eventName}",
                            'type' => "Top up for Event: RM $paymentDone",
                            'link' => route('public.event.view', ['id' => $event->id]),
                            'amount' => $paymentDone,
                            'summary' => "{$event->game->gameTitle}, {$event->tier->eventTier}, {$event->type->eventType}",
                            'isPositive' => false,
                            'date' => now(),
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

                    if ($total - ($participantPaymentSum + $paymentDone) < config('constants.STRIPE.ZERO')) {
                        $joinEvent->payment_status = 'completed';
                        $joinEvent->register_time = $regStatus;
                        $joinEvent->save();
                    }
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
                        'processing',
                    ];

                    if (in_array($paymentIntent['status'], $cancelableStatuses)) {
                        $this->stripeClient->cancelPaymentIntent($intentId, [
                            'cancellation_reason' => 'abandoned',
                        ]);
                    }
                } catch (Exception $e) {
                    return $this->showErrorParticipant($e->getMessage());
                }
            }

            DB::rollBack();

            return $this->showErrorParticipant('Your payment has failed unfortunately!');
        } catch (ModelNotFoundException|UnauthorizedException $e) {
            DB::rollBack();

            return $this->showErrorParticipant($e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();

            return $this->showErrorParticipant($e->getMessage());
        }
    }
}
