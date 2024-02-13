<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;

use App\Models\EventDetail;
use App\Models\PaymentTransaction;

use App\Models\StripeModel;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Stripe\Exception\CardException;

class StripeController extends Controller
{
    private $stripeClient;
    
    public function __construct() {
        $this->stripeClient = new StripeModel();
    }

    public function stripeCardIntentCreate(Request $request){
        try {

            $paymentIntent = $this->stripeClient->createCreditCardIntent( $request->paymentAmount );
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
            $transaction = PaymentTransaction::createTransaction($request->paymentMethod, 'SUCCESS');

            $event = EventDetail::find($request->eventId);

            if (!$event) {
                throw new ModelNotFoundException("Event not found with id: $request->id");
            } else if ($event->user_id != $request->userId) {
                throw new UnauthorizedException('You cannot view an event of another organizer!');
            } else {
                $event->payment_transaction_id = $transaction->id;
                $event->save();
            }

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
