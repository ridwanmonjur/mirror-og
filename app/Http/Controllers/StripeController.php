<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


namespace App\Http\Controllers;

use App\Models\EventDetail;
use App\Models\PaymentTransaction;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Stripe\Exception\CardException;

class StripeController extends Controller
{
    private $stripeClient;
    
    public function __construct() {
        $this->stripeClient = new StripeClient(env('STRIPE_SECRET'));
    }

    public function stripeCardIntentCreate(Request $request){
        try {
            $paymentIntent = $this->stripeClient->paymentIntents->create([
                'amount' => $request->paymentAmount,
                'currency' => 'myr',
                'payment_method_types' => ['card'],
                'automatic_payment_methods' => ['enabled' => false],
            ]);

            $responseData = [
                'status' => 'success',
                'message' => 'Payment successful',
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
            }
        
            $event->payment_transaction_id = $transaction->id;
            $event->save();

            $customer = $this->stripeClient->customers->create([
                'name' => $request->name,
                'email' => $request->email,
                'description' => 'My first customer',
            ]);

            $invoice= $this->stripeClient->invoices->create([
                'customer' => $customer->id,
                'collection_method' => 'send_invoice',
                'days_until_due' => 0,
            ]);

            $this->stripeClient->invoices->finalizeInvoice($invoice->id, []);
            
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
