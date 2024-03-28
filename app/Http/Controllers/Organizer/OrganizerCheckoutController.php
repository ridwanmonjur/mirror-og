<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventDetail;
use App\Models\Discount;
use Illuminate\View\View;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\PaymentTransaction;
use App\Models\StripePayment;
use Illuminate\Validation\UnauthorizedException;

class OrganizerCheckoutController extends Controller
{

    private $stripeClient;
    
    public function __construct(StripePayment $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function showCheckout(Request $request, $id): View
    {
        session()->forget(['successMessageCoupon',  'errorMessageCoupon']);

        try {
            $user = $request->get('user');
            $user->stripe_customer_id = $user->organizer()->value('stripe_customer_id');            
            $userId = $user->id;
            $event = EventDetail::findEventWithRelationsAndThrowError($userId, $id, ['tier']);     
            $isUserSameAsAuth = true;
            $fee = null;
              
            if (!is_null($event->payment_transaction_id)) {
                return view('Organizer.CheckoutEventSuccess', [
                    'event' => $event,
                    'mappingEventState' => EventDetail::mappingEventStateResolve(),
                    'isUser' => $isUserSameAsAuth,
                ]);
            } else if (is_null($event->tier)) {
                return $this->show404Organizer(
                    "Event with id: $id has no event tier chosen",
                    ['edit' => true, 'id' => $id] 
                );
            } else {

                $paymentMethods = $this->stripeClient->retrieveAllStripePaymentsByCustomer([
                    'customer' => $user->stripe_customer_id,
                ]);
                [$fee, $isDiscountApplied, $error] = array_values(
                    Discount::createDiscountFeeObject($request->coupon, $event->tier->tierEntryFee)
                );
                
                if ($isDiscountApplied) {
                    session()->flash('successMessageCoupon', "Applying your coupon named: $request->coupon!");
                } else if ( !is_null($error) ) {
                    session()->flash('errorMessageCoupon', $error);
                }
                
                return view('Organizer.CheckoutEvent', [
                    'event' => $event,
                    'mappingEventState' => EventDetail::mappingEventStateResolve(),
                    'isUser' => $isUserSameAsAuth,
                    'livePreview' => 1,
                    'fee' => $fee,
                    'paymentMethods' => $paymentMethods 
                ]);
            }
        }  catch (ModelNotFoundException | UnauthorizedException $e) {
            return $this->show404Organizer($e->getMessage());
        } catch (Exception $e) {
            return $this->show404Organizer(
                "Event not retrieved with id: $id"
            );
        }     
    }

    public function showCheckoutTransition(Request $request, $id) {
        try {
            $user = $request->get('user');
            $userId = $user->id;
            $status = $request->get('redirect_status');
          

            if ($status == "succeeded" && $request->has('payment_intent_client_secret')) {
                $intentId = $request->get('payment_intent');
                $paymentIntent = $this->stripeClient->retrieveStripePaymentByPaymentId($intentId);

                if ($paymentIntent["amount"] > 0 && 
                    $paymentIntent["amount_received"] == $paymentIntent["amount"] &&
                    $paymentIntent["metadata"]["eventId"] == $id
                ) {
                    $transaction = PaymentTransaction::createTransaction(
                        $intentId, "SUCCESS"
                    );
    
                    $event = EventDetail::findEventWithRelationsAndThrowError(
                        $userId, $id, null , 'joinEvents'
                    );

                    $event->payment_transaction_id = $transaction->id;
                    // this line must be below setting the payment transaction
                    if ($event->status != "DRAFT") { 
                        $event->status = "NOT PENDING";
                        $event->status = $event->isCompleteEvent() ? $event->statusResolved() : 'PENDING' ;
                    }
                        
                    $event->save();
                    return view('Organizer.CheckoutEventSuccess', [
                        'event' => $event,
                        'mappingEventState' => EventDetail::mappingEventStateResolve(),
                        'isUser' => true,
                    ]);
                }
            }

            return redirect()
                ->route('organizer.checkout.view', ['id' => $id] )
                ->with('errorCheckout', 'Your payment has failed unfortunately!');

        } catch (ModelNotFoundException | UnauthorizedException $e) {
            return $this->show404Organizer($e->getMessage());
        } catch (Exception $e) {
            return redirect()
                ->route('organizer.checkout.view', ['id' => $id] )
                ->with('errorCheckout', $e->getMessage());
        }
    }
}
