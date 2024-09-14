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

    public function showCheckout(Request $request): View
    {
        session()->forget(['successMessageCoupon',  'errorMessageCoupon']);
        $id = $request->id;
        // dd($id);
        try {
            $user = $request->get('user');
            $user->stripe_customer_id = $user->organizer()->value('stripe_customer_id');
            $event = EventDetail::findOrFail($id);
            $isUserSameAsAuth = true;
            $isRosterMember = RosterMember::where([
                'user_id' => $user->id,
                'join_events_id' => $request->joinEventId,
                'team_id' => $request->teamId,
            ])->exists();

            if (!$isRosterMember) {
                return $this->showErrorParticipant(
                    "You are a member of {$request->teamName}, but not a member of this event's roster.",
                );
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
                'mappingEventState' => EventDetail::mappingEventStateResolve(),
                'isUser' => $isUserSameAsAuth,
                'livePreview' => 1,
                'paymentMethods' => $paymentMethods,
            ]);
        }  catch (Exception $e) {
            return $this->showErrorParticipant($e->getMessage());
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
                if ($paymentIntent['amount'] > 0 &&
                    (
                        $paymentIntent['amount_capturable'] === $paymentIntent['amount']
                        ||
                        $paymentIntent['amount_received'] === $paymentIntent['amount']
                    )
                ) {
                    $transaction = PaymentTransaction::createTransaction(
                        $intentId,
                        'SUCCESS',
                        $paymentIntent['amount'] / 100
                    );

                    ParticipantPayment::create([
                        'team_members_id' => $paymentIntent['metadata']['memberId'],
                        'user_id' => $userId,
                        'join_events_id' => $paymentIntent['metadata']['joinEventId'],
                        'payment_amount' => (float) $paymentIntent['amount'] / 100,
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
                    if ($total !== 0.00 && $total === (float) $participantPaymentSum) {
                        $joinEvent->payment_status = 'completed';
                        $joinEvent->save();
                    }
                    

                    DB::commit();

                    return redirect()
                        ->route('participant.register.manage', ['id' => $paymentIntent['metadata']['teamId']])
                        ->with('errorCheckout', 'Your payment has failed unfortunately!');
                }
            }

            // return redirect()
            //     ->route('participant.checkout.action', ['id' => $id] )
            //     ->with('errorCheckout', 'Your payment has failed unfortunately!');
        } catch (ModelNotFoundException|UnauthorizedException $e) {
            // ERROR
            // TRASACTION ERROR OCCURRED
            DB::rollBack();

            return $this->showErrorParticipant($e->getMessage());
        } catch (Exception $e) {
            // ERROR
            // TRANSACTION ERROR OCCURRED
            DB::rollBack();

            return $this->showErrorParticipant($e->getMessage());

            // return redirect()
            //     ->route('organizer.checkout.view', ['id' => $id] )
            //     ->with('errorCheckout', $e->getMessage());
        }
    }
}
