<?php


namespace App\Http\Controllers;

use App\Models\StripePayment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

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
            $customer = null;
            $user = $request->attributes->get('user');
            $isEmptyStripeCustomerId = empty($request->stripe_customer_id);
            if ($isEmptyStripeCustomerId) {
                $customer = $this->stripeClient->createStripeCustomer($request->name, $request->email);
            } else {
                $customer = $this->stripeClient->retrieveStripeCustomer($request->stripe_customer_id);
            }

            $routeName = $request->route()->getName();
            if ($routeName === "stripe.stripeCardIntentCreateIntentWithHold") {
                $paymentIntent = $this->stripeClient->createPaymentIntent([
                    'customer' => $customer->id,
                    'amount' => $request->paymentAmount * 100,
                    'capture_method' => 'manual',
                    // just 2 lines of code
                    // 'payment_method_options' => [
                    //     'card' => ['request_extended_authorization' => 'if_available'],
                    // ],
                    'metadata' => $request->metadata,
                ]);
            } else {
                $paymentIntent = $this->stripeClient->createPaymentIntent([
                    'customer' => $customer->id,
                    'amount' => $request->paymentAmount * 100,
                    'payment_method_types' => ['card'],
                    'metadata' => $request->metadata,
                ]);
            }

            

            $responseData = [
                'status' => 'success',
                'message' => 'Payment intent creation successful',
                'data' => [
                    'client_secret' => $paymentIntent->client_secret,
                ],
            ];
            if ($isEmptyStripeCustomerId) { 
                $user->stripe_customer_id = $customer->id;
                $user->save();
            }

            return response()->json($responseData);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'retrieved' => $isEmptyStripeCustomerId,
                'message' => $e->getMessage(),
                'data' => null,
            ]);
        }
    }
}
