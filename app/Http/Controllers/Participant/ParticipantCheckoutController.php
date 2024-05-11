<?php

namespace App\Http\Controllers\Participant;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\EventDetail;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use App\Models\Discount;
use App\Models\ParticipantPayment;
use Illuminate\View\View;
use Exception;
use App\Models\PaymentTransaction;
use App\Models\StripePayment;
use App\Models\TeamMember;

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
              
            if (is_null($event->tier)) {
                return $this->showErrorOrganizer(
                    "Event with id: $id has no event tier chosen",
                    ['edit' => true, 'id' => $id] 
                );
            } else {
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
                    'paymentMethods' => $paymentMethods 
                ]);
            }
        }  catch (ModelNotFoundException | UnauthorizedException $e) {
            return $this->showErrorOrganizer($e->getMessage());
        } catch (Exception $e) {
            return $this->showErrorOrganizer(
                "Event not retrieved with id: $id"
            );
        }     
    }

    public function showCheckoutTransition(Request $request) {
        try {
            $user = $request->get('user');
            $userId = $user->id;
            $status = $request->get('redirect_status');
            if ($status == "succeeded" && $request->has('payment_intent_client_secret')) {
                $intentId = $request->get('payment_intent');
                $paymentIntent = $this->stripeClient->retrieveStripePaymentByPaymentId($intentId);
                if ($paymentIntent["amount"] > 0 && 
                    $paymentIntent["amount_received"] == $paymentIntent["amount"] 
                ) {
                    $transaction = PaymentTransaction::createTransaction(
                        $intentId, "SUCCESS", $paymentIntent["amount"]/100
                    );

                    ParticipantPayment::create([
                        'team_members_id' => $paymentIntent["metadata"]["memberId"],
                        'user_id' => $userId,
                        'join_events_id' => $paymentIntent["metadata"]["joinEventId"],
                        'payment_amount' => $paymentIntent["amount"],
                        'payment_id' => $transaction->id
                    ]);
    
                    return redirect()
                        ->route('participant.register.manage', ['id' => $paymentIntent["metadata"]["teamId"]] )
                        ->with('errorCheckout', 'Your payment has failed unfortunately!');
                }
            }

            // return redirect()
            //     ->route('participant.checkout.action', ['id' => $id] )
            //     ->with('errorCheckout', 'Your payment has failed unfortunately!');

        } catch (ModelNotFoundException | UnauthorizedException $e) {
            return $this->showErrorOrganizer($e->getMessage());
        } catch (Exception $e) {
            // return redirect()
            //     ->route('organizer.checkout.view', ['id' => $id] )
            //     ->with('errorCheckout', $e->getMessage());
        }
    }
}
