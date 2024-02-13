<?php

namespace App\Models;

use Stripe\PaymentIntent;
use Stripe\StripeClient;

class StripeModel
{
    private $stripeClient;
    
    public function __construct() {
        $this->stripeClient = new StripeClient(env('STRIPE_SECRET'));
    }

    public function createCreditCardIntent($paymentAmount): PaymentIntent {
        $paymentIntent = $this->stripeClient->paymentIntents->create([
            'amount' => $paymentAmount,
            'currency' => 'myr',
            'payment_method_types' => ['card'],
            'automatic_payment_methods' => ['enabled' => false],
        ]);

        return $paymentIntent;
    }

    public function send($name, $email) {

        $customer = $this->stripeClient->customers->create([
            'name' => $name,
            'email' => $email,
            'description' => 'My first customer',
        ]);

        $invoice= $this->stripeClient->invoices->create([
            'customer' => $customer->id,
            'collection_method' => 'send_invoice',
            'days_until_due' => 0,
        ]);

        $this->stripeClient->invoices->finalizeInvoice($invoice->id, []);
        
    }
}
