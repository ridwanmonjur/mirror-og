<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


namespace App\Http\Controllers;

use App\Models\EventDetail;
use App\Models\PaymentTransaction;
use App\Models\StripePayment;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Stripe\Exception\CardException;

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
                'metadata' => [
                    'eventId' => $request->eventId
                ]
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
    public function organizerTeamPay(Request $request)
    {
        try {
            $transaction = new PaymentTransaction();
            $transaction->payment_id = $request->paymentMethod;
            $transaction->payment_status = 'SUCCESS';
            $transaction->save();

            $event = EventDetail::find($request->eventId);

            if (!$event) {
                throw new ModelNotFoundException("Event not found with id: $request->id");
            } else if ($event->user_id != $request->userId) {
                throw new UnauthorizedException('You cannot view an event of another organizer!');
            } else {
                $event->payment_transaction_id = $transaction->id;
                $event->save();
            }

            $transaction = new PaymentTransaction();
            $transaction->createStripeCustomer($request->name, $request->email);
            $customer = $transaction->createStripeCustomer($request->name, $request->email);
            $invoice = $transaction->createStripeInvoice($customer->id);
            $transaction->finalizeStripeInvoice($invoice->id);
            
            $responseData = [
                'status' => 'success',
                'message' => 'Payment successful',
                'data' => [
                    'name' => $request->name,
                    'email' => $request->email,
                ],
            ];
        
            return response()->json($responseData);
        } catch (CardException $th) {
            throw new Exception("There was a problem processing your payment", 1);
        }
    }
}
