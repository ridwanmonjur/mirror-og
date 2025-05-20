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

class ParticipantCheckoutController extends Controller
{
    private $stripeClient;

    public function __construct(StripeConnection $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function showCheckout(ShowCheckoutRequest $request): RedirectResponse|View
    {
        try{
            session()->forget(['successMessageCoupon', 'errorMessageCoupon']);
            
            $user = $request->attributes->get('user');
            $user->stripe_customer_id = $user->organizer()->value('stripe_customer_id');
            
            $discount_wallet = DB::table('user_discounts')
                ->where('user_id', $user->id)
                ->first();

                
            $paymentMethods = $this->stripeClient->retrieveAllStripePaymentsByCustomer([
                'customer' => $user->stripe_customer_id,
            ]);
            
            $discountStatusEnums = config("constants.DISCOUNT_STATUS");
            $discountStatus = (is_null($discount_wallet) || $discount_wallet?->amount < 0) ? $discountStatusEnums['ABSENT']:  $discountStatusEnums['COMPLETE'];
            $payment_amount_min = $pendingAfterDiscount = 0.0;
            if ($discountStatus != $discountStatusEnums['ABSENT']) {
                $payment_amount_min = $request->amount;
                if ($discount_wallet->amount < $request->amount) {
                    $payment_amount_min = min(
                        $discount_wallet->amount,
                        ($request->total - $request->participantPaymentSum) - config("constants.STRIPE.MINIMUM_RM")
                    );

                    $discountStatus = $discountStatusEnums['PARTIAL'];
                    $pendingAfterDiscount = $request->total - ($request->participantPaymentSum + $payment_amount_min); 
                    if ($pendingAfterDiscount < config("constants.STRIPE.MINIMUM_RM") ) {
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
                'discount_wallet' => $discount_wallet,
                'isUser' => true,
                'livePreview' => 1,
                'paymentMethods' => $paymentMethods,
                'payment_amount_min' => $payment_amount_min,
                'discountStatusEnums' => $discountStatusEnums,
                'discountStatus' => $discountStatus,
                'paymentLowerMin' => config("constants.STRIPE.MINIMUM_RM")
            ]);
        } catch (Exception $e) {
            return back()->with('errorMessage', $e->getMessage())
                ->with('scroll', $request->joinEventId) ;
        }
    }

    public function discountCheckout(DiscountCheckoutRequest $request)
    {
        $user = $request->attributes->get('user');
        DB::beginTransaction();
        try {
            $userDiscount = $request->attributes->get('user_discount');
            $newAmount = $request->attributes->get('new_amount_after_discount');
            $isCompletePayment = $request->attributes->get('complete_payment');
            $pending_total_after_discount = $request->attributes->get('pending_total_after_discount');
            $walletAmount = $userDiscount->amount - $request->discount_applied_amount;

            DB::table('user_discounts')
                ->where('user_id', $user->id)
                ->update(['amount' => $walletAmount]);

            if (!$isCompletePayment) {
                if ($request->payment_intent_id) {
                    $stripePaymentIntent = $this->stripeClient->retrieveStripePaymentByPaymentId($request->payment_intent_id);
                    $this->stripeClient->updatePaymentIntent($stripePaymentIntent->id, [
                        'amount' => $newAmount * 100,
                    ]);
                }
            }

            $transaction = new RecordStripe([
                'payment_id' => null,
                'payment_status' => 'succeeded_applied_discount',
                'payment_amount' => $request->discount_applied_amount
            ]);
            $transaction->save();

            ParticipantPayment::create([
                'team_members_id' => $request->member_id,
                'user_id' => $user->id,
                'join_events_id' => $request->joinEventId,
                'payment_amount' => $request->discount_applied_amount,
                'payment_id' => $transaction->id,
            ]);

            if ($isCompletePayment) {
                if ($pending_total_after_discount < 0.1) {
                    $joinEvent = $request->joinEvent;
                    $joinEvent->payment_status = 'completed';
                    $joinEvent->save();
                }
            }

            DB::commit();

            $message = $isCompletePayment 
                ? "Discount applied successfully. You have completed this payment."
                : "Discount applied. You have RM {$newAmount} to pay.";

            return redirect()
                ->route('participant.register.manage', ['id' => $request->teamId])
                ->with('successMessage', $message)
                ->with('scroll', $request->joinEventId) ;

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
                ->with('scroll', $request->joinEventId) ;
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
            
            if (
                ($status === 'succeeded' || $status === "requires_capture") 
                && $request->has('payment_intent_client_secret')
            ) {
                $intentId = $request->get('payment_intent');
                $paymentIntent = $this->stripeClient->retrieveStripePaymentByPaymentId($intentId);
                $paymentDone = (float) $paymentIntent['amount'] / 100;

                if ($paymentIntent['amount'] > 0 &&
                    (
                        $paymentIntent['amount_capturable'] === $paymentIntent['amount']
                        ||
                        $paymentIntent['amount_received'] === $paymentIntent['amount']
                    )
                ) {
                    $joinEvent = JoinEvent::select('id', 'event_details_id', 'payment_status')->findOrFail($paymentIntent['metadata']['joinEventId']);

                    $participantPaymentSum = ParticipantPayment::select(['join_events_id', 'id', 'payment_amount'])
                        ->where('join_events_id', $joinEvent->id)
                        ->sum('payment_amount');

                    $transaction = RecordStripe::createTransaction(
                        $intentId,
                        $paymentIntent['status'],
                        $paymentDone
                    );

                    ParticipantPayment::create([
                        'team_members_id' => $paymentIntent['metadata']['memberId'],
                        'user_id' => $userId,
                        'join_events_id' => $paymentIntent['metadata']['joinEventId'],
                        'payment_amount' => $paymentDone,
                        'payment_id' => $transaction->id,
                    ]);


                    $event = EventDetail::select(['id', 'event_tier_id'])
                        ->where('id', $joinEvent->event_details_id)
                        ->with('tier')->first();
                    $total = (float) $event->tier?->tierEntryFee ;
                 
                    if (($total - ($participantPaymentSum + $paymentDone)) < 0.1) {
                        $joinEvent->payment_status = 'completed';
                        $joinEvent->save();
                    }
                    
                    DB::commit();

                    return redirect()
                        ->route('participant.register.manage', ['id' => $paymentIntent['metadata']['teamId']])
                        ->with('successMessage', 'Your payment has succeeded!')
                        ->with('scroll', $paymentIntent['metadata']['joinEventId']) ;
                }
            }
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
