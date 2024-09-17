<?php

namespace App\Models;

use Stripe\Collection;
use Stripe\Customer;
use Stripe\Invoice;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class StripePayment
{
    private $stripeClient;

    public function __construct()
    {
        $this->stripeClient = new StripeClient(env('STRIPE_SECRET'));
    }

    public function createStripeCustomer(string $name, string $email): ?Customer
    {
        return $this->stripeClient->customers->create([
            'email' => $email,
            'name' => $name,
        ]);
    }

    public function createPaymentIntent(array $array): PaymentIntent
    {
        return $this->stripeClient->paymentIntents->create([
            ...$array,
            'currency' => 'myr',
            'setup_future_usage' => 'off_session',
        ]);
    }

    public function createStripeInvoice(string| int | null $customerId): ?Invoice
    {
        if ($customerId) {
            return $this->stripeClient->invoices->create([
                'customer' => $customerId,
                'collection_method' => 'send_invoice',
                'days_until_due' => 0,
            ]);
        }

        return null;
    }

    public function retrieveStripeCustomer(string | int | null $customerId): ?Customer
    {
        if ($customerId) {
            return $this->stripeClient->customers->retrieve($customerId, []);
        }

        return null;
    }

    public function retrieveAllStripePaymentsByCustomer($arr): Collection
    {
        if (array_key_exists('customer', $arr) && ! empty($arr['customer'])) {
            return $this->stripeClient->paymentMethods->all($arr);
        }

        return new Collection();
    }

    public function retrieveStripePaymentByPaymentId(string| int | null $paymentId): ?PaymentIntent
    {
        if ($paymentId) {
            return $this->stripeClient->paymentIntents->retrieve($paymentId, []);
        }

        return null;
    }

    public function updatePaymentIntent(string $id, array $array): PaymentIntent
    {
        return $this->stripeClient->paymentIntents->update($id, $array);
    }

    public function updateStripeCustomer(string $id, array $values): ?Customer
    {
        return $this->stripeClient->customers->update($id, $values);
    }


    public function finalizeStripeInvoice(string| int | null $invoiceId): ?Invoice
    {
        if ($invoiceId) {
            return $this->stripeClient->invoices->finalizeInvoice($invoiceId, []);
        }

        return null;
    }
}
