<?php

namespace App\Models;

use Stripe\Account;
use Stripe\AccountSession;
use Stripe\Collection;
use Stripe\Customer;
use Stripe\Invoice;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\SearchResult;
use Stripe\StripeClient;

class StripeConnection
{
    private $stripeClient;

    public function __construct()
    {
        $stripeSecret = config('services.stripe.secret');

        if (empty($stripeSecret)) {
            throw new \Exception('Stripe secret key is not configured.');
        }

        $this->stripeClient = new StripeClient($stripeSecret);
    }

    /**
     * Cancel a payment intent
     *
     * @param  string|int|null  $paymentId  The ID of the payment intent to cancel
     * @param  array  $options  Additional options for cancellation
     * @return \Stripe\PaymentIntent|null The canceled payment intent or null if payment ID is not provided
     *
     * @throws \Exception If there's an error during cancellation
     */
    public function cancelPaymentIntent(string|int|null $paymentId, array $options = []): ?PaymentIntent
    {
        if ($paymentId) {
            try {
                return $this->stripeClient->paymentIntents->cancel($paymentId, $options);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                throw new \Exception("Failed to cancel payment intent: {$e->getMessage()}", $e->getCode(), $e);
            }
        }

        return null;
    }

    public function createStripeCustomer(array $options): ?Customer
    {
        return $this->stripeClient->customers->create($options);
    }

    public function createPaymentIntent(array $array): PaymentIntent
    {
        return $this->stripeClient->paymentIntents->create([
            ...$array,
            'currency' => 'myr',
            // 'setup_future_usage' => 'off_session',
        ]);
    }

    public function createStripeInvoice(string|int|null $customerId): ?Invoice
    {
        if ($customerId) {
            $invoice = $this->stripeClient->invoices->create([
                'customer' => $customerId,
                'collection_method' => 'send_invoice',
                'days_until_due' => 0,
            ]);

            // Finalize and send the invoice
            $this->stripeClient->invoices->sendInvoice($invoice->id);

            return $invoice;
        }

        return null;
    }

    public function retrieveStripeCustomer(string|int|null $customerId): ?Customer
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

        return new Collection;
    }

    public function searchStripePaymenst($arr): SearchResult
    {
        if (array_key_exists('query', $arr) && ! empty($arr['query'])) {

            return $this->stripeClient->paymentIntents->search($arr);
        }

        return new SearchResult;
    }

    public function retrieveStripePaymentByPaymentId(string|int|null $paymentId): ?PaymentIntent
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

    /**
     * Retrieve a payment method
     */
    public function retrievePaymentMethod(string $paymentMethodId): PaymentMethod
    {
        return $this->stripeClient->paymentMethods->retrieve($paymentMethodId);
    }
}
