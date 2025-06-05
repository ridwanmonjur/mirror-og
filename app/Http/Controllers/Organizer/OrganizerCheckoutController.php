<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Jobs\CreateUpdateEventTask;
use App\Models\EventCreateCoupon;
use App\Models\EventDetail;
use App\Models\RecordStripe;
use App\Models\StripeConnection;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\View\View;

class OrganizerCheckoutController extends Controller
{
    private $stripeClient;

    public function __construct(StripeConnection $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function showCheckout(Request $request, $id): View
    {
        session()->forget(['successMessageCoupon', 'errorMessageCoupon']);

        try {
            $user = $request->get('user');
            $user->stripe_customer_id = $user->organizer()->value('stripe_customer_id');
            $userId = $user->id;
            $event = EventDetail::findEventWithRelationsAndThrowError($userId, $id, null, ['tier', 'signup:id,event_id,signup_open,normal_signup_start_advanced_close,signup_close']);
            $isUserSameAsAuth = true;
            $fee = null;

            if (!is_null($event->payment_transaction_id)) {
                return view('Organizer.CheckoutEventSuccess', [
                    'event' => $event,
                    'isUser' => $isUserSameAsAuth,
                ]);
            }
            if (is_null($event->tier)) {
                return $this->showErrorOrganizer("Event with id: {$id} has no event tier chosen", ['edit' => true, 'id' => $id]);
            }

            $paymentMethods = $this->stripeClient->retrieveAllStripePaymentsByCustomer([
                'customer' => $user->stripe_customer_id,
            ]);
            [$fee, $isEventCreateCouponApplied, $error] = array_values(EventCreateCoupon::createEventCreateCouponFeeObject($request->coupon, $event->tier?->tierPrizePool));

            if ($isEventCreateCouponApplied) {
                session()->flash('successMessageCoupon', "Applying your coupon named: {$request->coupon}!");
            } elseif (!is_null($error)) {
                session()->flash('errorMessageCoupon', $error);
            }

            return view('Organizer.CheckoutEvent', [
                'event' => $event,
                'isUser' => $isUserSameAsAuth,
                'livePreview' => 1,
                'fee' => $fee,
                'paymentMethods' => $paymentMethods,
            ]);
        } catch (ModelNotFoundException | UnauthorizedException $e) {
            return $this->showErrorOrganizer($e->getMessage());
        } catch (Exception $e) {
            return $this->showErrorOrganizer($e->getMessage());
        }
    }

    public function showCheckoutTransition(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = $request->get('user');
            $userId = $user->id;
            $status = $request->get('redirect_status');

            if ($status === 'succeeded' && $request->has('payment_intent_client_secret')) {
                $intentId = $request->get('payment_intent');
                $paymentIntent = $this->stripeClient->retrieveStripePaymentByPaymentId($intentId);
                if ($paymentIntent->status === 'requires_capture') {
                    $paymentIntent->capture();
                }

                if ($paymentIntent['amount'] > 0 && $paymentIntent['amount_received'] === $paymentIntent['amount'] && $paymentIntent['metadata']['eventId'] === $id) {
                    $transaction = RecordStripe::createTransaction($intentId, $paymentIntent['status'], $paymentIntent['amount'] / 100);

                    $event = EventDetail::findEventWithRelationsAndThrowError($userId, $id, null, 'joinEvents');

                    $event->payment_transaction_id = $transaction->id;
                    if ($event->status !== 'DRAFT') {
                        $event->status = 'NOT PENDING';
                        $event->status = $event->isCompleteEvent() ? $event->statusResolved() : 'PENDING';
                    }

                    $event->save();
                    try {
                        $event->createStatusUpdateTask();
                        $event->createDeadlinesTask();
                    } catch (Exception $e) {
                        throw new Exception('Failed to queue event task creation: ' . $e->getMessage());
                    }

                    DB::commit();
                    return view('Organizer.CheckoutEventSuccess', [
                        'event' => $event,
                        'isUser' => true,
                    ]);
                }
            }
            DB::rollback();
            return redirect()
                ->route('organizer.checkout.view', ['id' => $id])
                ->with('errorCheckout', 'Your payment has failed unfortunately!');
        } catch (ModelNotFoundException | UnauthorizedException $e) {
            DB::rollback();
            return $this->showErrorOrganizer($e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return redirect()
                ->route('organizer.checkout.view', ['id' => $id])
                ->with('errorCheckout', $e->getMessage());
        }
    }
}
