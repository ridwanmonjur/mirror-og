<?php

namespace App\Models;


use App\Models\EventDetail;
use App\Models\PaymentTransaction;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Stripe\Exception\CardException;

class StripePayment 
{
    private $stripeClient;

    public function __construct()
    {
        $this->stripeClient = new StripeClient(env('STRIPE_SECRET'));
    }

    public function createStripeCustomer($name, $email)
    {
        return $this->stripeClient->customers->create([
            'email' => $email,
            'name' => $name
        ]);
    }

    public function createPaymentIntent($array)
    {
        return $this->stripeClient->paymentIntents->create([
            ...$array,
            'currency' => 'myr',
            'payment_method_types' => ['card'],
            'automatic_payment_methods' => ['enabled' => false],
            'setup_future_usage' => 'off_session',
        ]);
    }

    public function createStripeInvoice($customerId)
    {
        return $this->stripeClient->invoices->create([
            'customer' => $customerId,
            'collection_method' => 'send_invoice',
            'days_until_due' => 0,
        ]);
    }

    public function retrieveStripeCustomer($customerId)
    {
        return $this->stripeClient->customers->retrieve($customerId, []);
    }

    public function retrieveAllStripePaymentsByCustomer($arr) 
    {
        if (array_key_exists('customer', $arr) && !empty($arr['customer'])) {
            return $this->stripeClient->paymentMethods->all($arr);
        } else {
            return [];
        }
    }

    public function retrieveStripePaymentByPaymentId($paymentId) 
    {
        return $this->stripeClient->paymentIntents->retrieve($paymentId, []);
    }

    public function finalizeStripeInvoice($invoiceId)
    {
        return $this->stripeClient->invoices->finalizeInvoice($invoiceId, []);
    }
}