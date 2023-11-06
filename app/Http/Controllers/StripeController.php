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
    public function organizerTeamPay(Request $request)
    {
        try {
            $stripe = new StripeClient(env('STRIPE_SECRET'));

            $stripe->paymentIntents->create([
                'amount' => 99 * 100,
                'currency' => 'myr',
                'payment_method' => $request->payment_method,
                'description' => 'Demo payment with stripe',
                'confirm' => true,
                'receipt_email' => $request->email,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
            ]);
            $customer = $stripe->customers->create([
                'name' => $request->name,
                'email' => $request->email,
                'description' => 'My first customer',
            ]);
            $invoice= $stripe->invoices->create([
                'customer' => $customer->id,
                'collection_method' => 'send_invoice',
                'days_until_due' => 0,
            ]);
            $stripe->invoices->finalizeInvoice($invoice->id, []);
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
