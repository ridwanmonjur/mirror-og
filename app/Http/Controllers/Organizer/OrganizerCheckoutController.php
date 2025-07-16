<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RedeemCouponRequest;
use App\Models\SystemCoupon;
use App\Models\UserCoupon;
use App\Models\EventDetail;
use App\Models\OrganizerPayment;
use App\Models\RecordStripe;
use App\Models\StripeConnection;
use App\Models\TransactionHistory;
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

    public function showCheckout(RedeemCouponRequest $request, $id): View
    {
        DB::beginTransaction();
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
            
            $prevForm = [
                $request->coupon_code
            ];

            [$fee, $isCouponApplied, $error, $coupon] = SystemCoupon::loadCoupon($request->coupon_code, $event->tier?->tierPrizePool, 'organizer');

            if ($fee['finalFee'] < config('constants.STRIPE.ZER0')) {
                $paymentId = SystemCoupon::orgFullCheckout($event, $user, $fee['totalFee']);
                $event->payment_transaction_id = $paymentId;
                $event->save();

                $coupon->validateAndIncrementCoupon();
                DB::commit();
                return view('Organizer.CheckoutEventSuccess', [
                    'event' => $event,
                    'isUser' => true,
                ]);
            }

            if ($isCouponApplied) {
                DB::commit();
                $coupon->validateAndIncrementCoupon();
                session()->flash('successMessageCoupon', "Applying your coupon named: {$request->coupon_code}! Note, the minimum stripe payment must be RM 5.0");
            } elseif (!is_null($error)) {
                throw new Exception($error);
            }

            DB::rollBack();

            return view('Organizer.CheckoutEvent', [
                'event' => $event,
                'isUser' => $isUserSameAsAuth,
                'livePreview' => 1,
                'fee' => $fee,
                'prevForm' => $prevForm,
                'paymentMethods' => $paymentMethods,
            ]);
        } catch (ModelNotFoundException | UnauthorizedException $e) {
            DB::rollBack();
            return $this->showErrorOrganizer($e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
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
                $paymentMethodId = $paymentIntent['payment_method'];
                $paymentMethod = $this->stripeClient->retrievePaymentMethod($paymentMethodId);


                if ($paymentIntent->status === 'requires_capture') {
                    $paymentIntent->capture();
                }

                if ($paymentIntent['amount'] > 0 && $paymentIntent['amount_received'] === $paymentIntent['amount'] && $paymentIntent['metadata']['eventId'] === $id) {
                    $event = EventDetail::findEventWithRelationsAndThrowError($userId, $id, null, 'joinEvents');
                    $prizeFinal = SystemCoupon::getIncrementedFee($event->tier?->tierPrizePool);
                    
                    $couponCode = $paymentIntent['metadata']['coupon_code'] ?? null;
                    if ($couponCode) {
                        $this->validateAndIncrementCoupon($couponCode, $user->id);
                    }
                    
                    $transaction = RecordStripe::createTransaction($paymentIntent, $paymentMethod, $user->id, $request->query('saveDefault'), $request->query('savePayment'));
                    $historyId = TransactionHistory::insertGetId([
                        'name' => "$event->eventName",
                        'type' => 'Entry Fee',
                        'isPositive' => false,
                        'link' => route('public.event.view', ['id'=> $event->id]),
                        'amount' => $transaction->payment_amount,
                        'summary' => 'Entry Fee',
                        'date'=> DB::raw('NOW()'),
                        'user_id' => $user->id
                    ]);
    
                    $paymentId = OrganizerPayment::insertGetId([
                        'payment_amount' => $transaction->payment_amount,
                        'discount_amount' => $prizeFinal - $transaction->payment_amount,
                        'user_id' => $user->id,
                        'history_id' => $historyId,
                        'payment_id' => $transaction->id,
                    ]);
    
                    $event->payment_transaction_id = $paymentId;
                    $event->save();

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

    private function validateAndIncrementCoupon($couponCode, $userId)
    {
        if (!$couponCode) {
            return;
        }

        $systemCoupon = SystemCoupon::where('code', $couponCode)
            ->where('is_active', true)
            ->first();

        if (!$systemCoupon) {
            throw new Exception('Invalid coupon code.');
        }

        if (!$systemCoupon->is_public) {
            $userCoupon = UserCoupon::where('user_id', $userId)
                ->where('coupon_id', $systemCoupon->id)
                ->first();

            if (!$userCoupon) {
                throw new Exception('You do not have access to this coupon.');
            }
        } else {
            $userCoupon = UserCoupon::firstOrCreate([
                'user_id' => $userId,
                'coupon_id' => $systemCoupon->id
            ], [
                'redeemable_count' => 0
            ]);
        }

        $userCoupon = UserCoupon::where('user_id', $userId)
            ->where('coupon_id', $systemCoupon->id)
            ->first();

        if ($userCoupon->redeemable_count >= $systemCoupon->redeem_count) {
            throw new Exception('You have exceeded the maximum number of redemptions for this coupon.');
        }

        $userCoupon->increment('redeemable_count');
    }
}
