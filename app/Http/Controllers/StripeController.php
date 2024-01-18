<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


namespace App\Http\Controllers;

use Exception;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use Stripe\Exception\CardException;

class StripeController extends Controller
{
    private $stripeClient;
    
    public function __construct() {
        $this->stripeClient = new StripeClient(env('STRIPE_SECRET'))s;
    }


    public function createIntent(Request $request){
        $this->stripeClient->paymentIntents->create([
            'amount' => $request->paymentAmount,
            'currency' => 'myr',
            'automatic_payment_methods' => ['enabled' => true],
        ]);

    }
    public function organizerTeamPay(Request $request)
    {
        try {
            $this->stripeClient = new StripeClient(env('STRIPE_SECRET'));

            $stripeClient->paymentIntents->create([
                'amount' => 99 * 100,
                'currency' => 'myr',
                'payment_method' => $request->payment_method,
                'description' => 'Demo payment with stripe',
                'confirm' => true,
                'receipt_email' => $request->email,
                'payment_method_types' => ['card'],
                'automatic_payment_methods' => ['enabled' => false,],
            ]);
            // $customer = $stripeClient->customers->create([
            //     'name' => $request->name,
            //     'email' => $request->email,
            //     'description' => 'My first customer',
            //      add this????????????????
            //      "card" : charge.stripe_card_token
            // ]);
            // $invoice= $stripeClient->invoices->create([
            //     'customer' => $customer->id,
            //     'collection_method' => 'send_invoice',
            //     'days_until_due' => 0,
            // ]);
            // $stripe->invoices->finalizeInvoice($invoice->id, []);
            $responseData = [
                'status' => 'success',
                'message' => 'Payment successful',
                'data' => [
                    'name' => $request->name,
                    'email' => $request->email,
                    // Add other data as needed
                ],
            ];
        
            return response()->json($responseData);
        } catch (CardException $th) {
            throw new Exception("There was a problem processing your payment", 1);
        }

        return back()->withSuccess('Payment done.');
    }
}
