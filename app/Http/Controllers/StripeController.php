<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


namespace App\Http\Controllers;

use App\Models\StripePayment;
use Exception;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    private $stripeClient;
    
    public function __construct(StripePayment $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function stripeCardIntentCreate(Request $request){
        try {
            $customer = null;
            if (empty($request->stripeCustomerId)) {
                $customer = $this->stripeClient->createStripeCustomer($request->name, $request->email);
            } else {
                $customer = $this->stripeClient->retrieveStripeCustomer($request->stripeCustomerId);
            }

            $paymentIntent = $this->stripeClient->createPaymentIntent([
                'customer'=> $customer->id,
                'amount' =>  $request->paymentAmount,
                'payment_method_types' => ['card'],
                'metadata' => $request->metadata
            ]);

            $responseData = [
                'status' => 'success',
                'message' => 'Payment intent creation successful',
                'data' => [
                    'client_secret' => $paymentIntent->client_secret,
                ],
            ];
        
            return response()->json($responseData);
        } catch (Exception $e) {
            return response()->json([
                'status'=> 'error',
                'message'=> $e->getMessage(),
                'data'=> null
            ]);
        }
    }


   
}
