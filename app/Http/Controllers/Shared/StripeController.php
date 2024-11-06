<?php


namespace App\Http\Controllers;

use App\Models\PaymentIntent;
use App\Models\StripePayment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StripeController extends Controller
{
    private $stripeClient;

    public function __construct(StripePayment $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function stripeCardIntentCreate(Request $request)
    {
        try {
            DB::beginTransaction();
            $paymentIntentStatus = 'created';
            $customerStatus = 'created';
            $willCreateNewPaymentIntent = true;
            $isParticipant = $request->role === "PARTICIPANT";
            $isManualCaptureMethod = false;
            if ($isParticipant) {
                $eventType = $request['metadata']['eventType'];
                $isManualCaptureMethod = isset($request['metadata'])
                    && isset($request['metadata']['eventType'])
                    && $eventType === "normal";
            }

            $user = auth()->user()?->fresh();
            $isEmptyStripeCustomerId = empty($user->stripe_customer_id);
            if ($isEmptyStripeCustomerId) {
                $customer = $this->stripeClient->createStripeCustomer($request->name, $request->email);
            } else {
                $customerStatus = 'retrieved';
                $customer = $this->stripeClient->retrieveStripeCustomer($user->stripe_customer_id);
            }

            $paymentIntentDB = PaymentIntent::where([
                'user_id' => $user->id,
                'status' => 'requires_payment_method'
            ])->first();

            $paymentIntentStripe = $paymentIntentDB?->payment_intent_id ?
                $this->stripeClient->retrieveStripePaymentByPaymentId($paymentIntentDB?->payment_intent_id)
                : null;
            
            $willCreateNewPaymentIntent = ! ($paymentIntentDB && $paymentIntentStripe 
                && $paymentIntentStripe?->status === 'requires_payment_method');

            $paymentIntentStripeBody = [
                'amount' => +$request->paymentAmount * 100,
                'metadata' => $request->metadata,
            ];

            if ($isManualCaptureMethod) {
                $paymentIntentStripeBody['capture_method'] = 'manual' ;
            } else {
                $paymentIntentStripeBody['capture_method'] = 'automatic_async' ;
            }
            
            if ($willCreateNewPaymentIntent) {
                $paymentIntentStripeBody['customer'] = $customer->id;
                $paymentIntentDB?->delete();

                $paymentIntentStripe = $this->stripeClient->createPaymentIntent($paymentIntentStripeBody);
                
                $paymentIntentDBBody = [
                    'user_id' => $user->id,
                    'customer_id' => $customer->id,
                    'payment_intent_id' => $paymentIntentStripe->id,
                    'amount' => $request->paymentAmount,
                    'status' => $paymentIntentStripe->status,
                ];
        
                $paymentIntentDB = PaymentIntent::insert($paymentIntentDBBody);
            } else {
                $paymentIntentStatus = 'retrieved';


                $this->stripeClient->updatePaymentIntent($paymentIntentStripe->id, $paymentIntentStripeBody);

                $paymentIntentDB->update([
                    'customer_id' => $customer->id,
                    'status' => $paymentIntentStripe->status,
                    'amount' => $request->paymentAmount,
                ]);
            }

            $responseData = [
                'success' => 'true',
                'message' => 'Payment intent creation successful',
                'data' => [
                    'client_secret' => $paymentIntentStripe->client_secret,
                    'customer_status' => $customerStatus,
                    'payment_status' => $paymentIntentStatus,
                    'customer'=> $customer,
                    'payment_intent' => $paymentIntentStripe
                ],
            ];

            if ($isEmptyStripeCustomerId) { 
                $user->stripe_customer_id = $customer->id;
                $user->save();
            }

            DB::commit();

            return response()->json($responseData);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => 'false',
                'retrieved' => $isEmptyStripeCustomerId,
                'message' => $e->getMessage(),
                'data' => [
                    'client_secret' => null,
                    'customer_status' => $customerStatus,
                    'payment_status' => $paymentIntentStatus,
                    'customer'=> $customer,
                    'payment_intent' => $paymentIntentStripe
                ],
            ]);
        }
    }
}
