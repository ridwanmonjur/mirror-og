<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\EventDetail;
use App\Models\JoinEvent;
use App\Models\ParticipantPayment;
use App\Models\PaymentTransaction;
use App\Models\RosterMember;
use App\Models\StripePayment;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\View\View;

class ParticipantCheckoutController extends Controller
{
    private $stripeClient;

    public function __construct(StripePayment $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function showCheckout(Request $request): RedirectResponse|View
    {
        session()->forget(['successMessageCoupon',  'errorMessageCoupon']);
        $id = $request->id;

        try {
            $user = $request->get('user');
            $user->stripe_customer_id = $user->organizer()->value('stripe_customer_id');
            $discount_wallet = DB::table('user_discounts')
                ->where('user_id', $user->id)->first();
            $event = EventDetail::with(['tier', 'game'])
                ->where('id', $id)
                ->first();
            $isUserSameAsAuth = true;
            $rosterMember = RosterMember::where([
                'user_id' => $user->id,
                'join_events_id' => $request->joinEventId,
                'team_id' => $request->teamId,
            ])
                ->first();

            $isRosterMember = $rosterMember !== null;

            if (!$isRosterMember) {
                return $this->showErrorParticipant(
                    "You are a member of {$request->teamName}, but not a member of this event's roster.",
                );
            }

            $participantPaymentSum = ParticipantPayment::select(['join_events_id', 'id', 'payment_amount'])
                ->where('join_events_id', $request->joinEventId)
                ->sum('payment_amount');

            $total = (float) $event->tier?->tierEntryFee ;
            $paymentOptionLower = config("constants.STRIPE.MINIMUM_RM");
            $paymentOptionHigher = $total - config("constants.STRIPE.MINIMUM_RM");

            if ($request->amount < $paymentOptionLower) {
                return back()
                    ->with(['errorMessage' => "If you don't pay full amount, you have to pay higher than RM $paymentOptionLower and lower than RM $paymentOptionHigher."]);
            }

            $pending = $total - ($participantPaymentSum + $request->amount);
            if ($pending > config("constants.STRIPE.ZERO") && $request->amount > $paymentOptionHigher) {
                return back()
                    ->with(['errorMessage' => "If you don't pay full amount, you have to pay higher than RM $paymentOptionLower and lower than RM $paymentOptionHigher."]);
            }

            if (is_null($event->tier)) {
                return $this->showErrorParticipant(
                    "Event with id: {$id} has no event tier chosen",
                );
            }
            $paymentMethods = $this->stripeClient->retrieveAllStripePaymentsByCustomer([
                'customer' => $user->stripe_customer_id,
            ]);

            return view('Participant.CheckoutEvent', [
                'teamId' => $request->teamId,
                'amount' => $request->amount,
                'teamName' => $request->teamName,
                'joinEventId' => $request->joinEventId,
                'memberId' => $request->memberId,
                'event' => $event,
                'discount_wallet' => $discount_wallet,
                'isUser' => $isUserSameAsAuth,
                'livePreview' => 1,
                'paymentMethods' => $paymentMethods,
            ]);
        }  catch (Exception $e) {
            return $this->showErrorParticipant($e->getMessage());
        }
    }

    public function discountCheckout(Request $request)
    {
        $paymentAmount = (float) $request->payment_amount;
        $discountAmount = (float) $request->discount_applied_amount ;

        DB::beginTransaction();
        try {
            $newAmount = $paymentAmount - $discountAmount ;
            $isNewAmountZero = $newAmount < 0.05;
            
            $user = $request->get('user');
            $userId = $user->id;
            $walletAmount = 0;
            $userDiscount = DB::table('user_discounts')
                ->where('user_id', $user->id)->first();

            if ($userDiscount) {
                $walletAmount = $userDiscount->amount - $discountAmount;
                DB::table('user_discounts')
                    ->where('user_id', $user->id)
                    ->update(
                        ['amount' => $walletAmount]
                    );
            } else {
                throw new Exception("Discount doesn't exist!");
            }

            if (!$isNewAmountZero) {
                $stripePaymentIntent =
                    $this->stripeClient->retrieveStripePaymentByPaymentId($request?->payment_intent_id);
                $this->stripeClient->updatePaymentIntent($stripePaymentIntent->id, [
                    'amount' => $newAmount * 100,
                ]);
            }
           
            $transaction = new PaymentTransaction([
                'payment_id' => null,
                'payment_status' => 'succeeded_applied_discount',
                'payment_amount' => $discountAmount
            ]);

            $transaction->save();

            ParticipantPayment::create([
                'team_members_id' => $request->member_id,
                'user_id' => $userId,
                'join_events_id' => $request->joinEventId,
                'payment_amount' =>  $discountAmount,
                'payment_id' => $transaction->id,
            ]);
            
            if ($isNewAmountZero) {
                $joinEvent = JoinEvent::select('id', 'event_details_id', 'payment_status')
                    ->findOrFail($request->joinEventId);
                $event = EventDetail::select(['id', 'event_tier_id'])
                    ->where('id', $joinEvent->event_details_id)
                    ->with('tier')->first();
                $total = (float) $event->tier?->tierEntryFee ;
                if (($total - $discountAmount) < 0.05) {
                    $joinEvent->payment_status = 'completed';
                    $joinEvent->save();
                }

                $message = "Discount applied successfully. You have completed this payment.";
            } else {
                $message = "Discount applied. You have RM {$newAmount} to pay.";
            }
            
            DB::commit();

            session()->flash('message', 'Discount applied successfully');

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'wallet_amount' => $walletAmount,
                    'transaction_id' => $transaction->id,
                    'new_amount' => $newAmount,
                    'is_payment_completed' => $isNewAmountZero
                ]
            ], 200);
            
        } catch (ModelNotFoundException|UnauthorizedException $e) {
            DB::rollBack();
            $stripePaymentIntent =
                $this->stripeClient->retrieveStripePaymentByPaymentId($request?->payment_intent_id);
                $this->stripeClient?->updatePaymentIntent($stripePaymentIntent->id, [
                    'amount' => $paymentAmount * 100,
                ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 401);
        } catch (Exception $e) {
            $stripePaymentIntent =
            $this->stripeClient->retrieveStripePaymentByPaymentId($request?->payment_intent_id);
            $this->stripeClient?->updatePaymentIntent($stripePaymentIntent->id, [
                'amount' => $paymentAmount * 100,
            ]);
          
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }

    public function showCheckoutTransition(Request $request)
    {
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
                // $this->stripeClient->updateStripeCustomer($paymentIntent->customer, )
                if ($paymentIntent['amount'] > 0 &&
                    (
                        $paymentIntent['amount_capturable'] === $paymentIntent['amount']
                        ||
                        $paymentIntent['amount_received'] === $paymentIntent['amount']
                    )
                ) {
                    $transaction = PaymentTransaction::createTransaction(
                        $intentId,
                        $paymentIntent['status'],
                        $paymentIntent['amount'] / 100
                    );

                    ParticipantPayment::create([
                        'team_members_id' => $paymentIntent['metadata']['memberId'],
                        'user_id' => $userId,
                        'join_events_id' => $paymentIntent['metadata']['joinEventId'],
                        'payment_amount' => (float) $paymentIntent['amount'] / 100 ,
                        'payment_id' => $transaction->id,
                    ]);

                    $joinEvent = JoinEvent::select('id', 'event_details_id', 'payment_status')->findOrFail($paymentIntent['metadata']['joinEventId']);
                    $event = EventDetail::select(['id', 'event_tier_id'])
                        ->where('id', $joinEvent->event_details_id)
                        ->with('tier')->first();
                    $total = (float) $event->tier?->tierEntryFee ;
                    $participantPaymentSum = ParticipantPayment::select(['join_events_id', 'id', 'payment_amount'])
                        ->where('join_events_id', $joinEvent->id)
                        ->sum('payment_amount');
                    if (($total - (float) $participantPaymentSum) < 0.05) {
                        $joinEvent->payment_status = 'completed';
                        $joinEvent->save();
                    }
                    
                    DB::commit();

                    return redirect()
                        ->route('participant.register.manage', ['id' => $paymentIntent['metadata']['teamId']])
                        ->with('errorCheckout', 'Your payment has failed unfortunately!');
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
