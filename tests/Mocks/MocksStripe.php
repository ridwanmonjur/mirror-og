<?php

namespace Tests\Mocks;

use Mockery;
use Stripe\PaymentIntent;
use Stripe\Customer;
use Stripe\Refund;
use Stripe\PaymentMethod;
use App\Models\StripeConnection;

trait MocksStripe
{
    protected $stripeMock;
    protected $stripeClientMock;

    /**
     * Mock the StripeConnection class
     */
    protected function mockStripeClient()
    {
        $this->stripeMock = Mockery::mock(StripeConnection::class);
        $this->app->instance(StripeConnection::class, $this->stripeMock);
        return $this->stripeMock;
    }

    /**
     * Mock Stripe customer creation
     */
    protected function mockStripeCustomerCreation($customerId = 'cus_test123', $email = 'test@example.com')
    {
        $customer = Mockery::mock(Customer::class);
        $customer->id = $customerId;
        $customer->email = $email;

        $this->stripeMock
            ->shouldReceive('createStripeCustomer')
            ->andReturn($customer);

        return $customer;
    }

    /**
     * Mock Stripe customer retrieval
     */
    protected function mockStripeCustomerRetrieval($customerId = 'cus_test123', $email = 'test@example.com')
    {
        $customer = Mockery::mock(Customer::class);
        $customer->id = $customerId;
        $customer->email = $email;

        $this->stripeMock
            ->shouldReceive('retrieveStripeCustomer')
            ->with($customerId)
            ->andReturn($customer);

        return $customer;
    }

    /**
     * Mock payment intent creation
     */
    protected function mockPaymentIntentCreation(
        $amount,
        $status = 'requires_payment_method',
        $captureMethod = 'automatic_async',
        $paymentIntentId = 'pi_test123'
    ) {
        $paymentIntent = Mockery::mock(PaymentIntent::class);
        $paymentIntent->id = $paymentIntentId;
        $paymentIntent->status = $status;
        $paymentIntent->amount = $amount * 100; // Convert to cents
        $paymentIntent->capture_method = $captureMethod;
        $paymentIntent->client_secret = 'pi_test123_secret_test';

        $this->stripeMock
            ->shouldReceive('createPaymentIntent')
            ->andReturn($paymentIntent);

        return $paymentIntent;
    }

    /**
     * Mock payment intent retrieval
     */
    protected function mockPaymentIntentRetrieval($paymentId, $status = 'requires_capture', $amount = 5000)
    {
        $paymentIntent = Mockery::mock(PaymentIntent::class);
        $paymentIntent->id = $paymentId;
        $paymentIntent->status = $status;
        $paymentIntent->amount = $amount;

        // Mock capture method
        $paymentIntent->shouldReceive('capture')
            ->andReturnSelf();

        // Mock cancel method
        $paymentIntent->shouldReceive('cancel')
            ->andReturnSelf();

        $this->stripeMock
            ->shouldReceive('retrieveStripePaymentByPaymentId')
            ->with($paymentId)
            ->andReturn($paymentIntent);

        return $paymentIntent;
    }

    /**
     * Mock refund creation
     */
    protected function mockRefundCreation($paymentIntentId, $amount = null)
    {
        $refund = Mockery::mock(Refund::class);
        $refund->id = 'ref_test123';
        $refund->amount = $amount ? $amount * 100 : null;
        $refund->status = 'succeeded';
        $refund->payment_intent = $paymentIntentId;

        $this->stripeMock
            ->shouldReceive('createRefund')
            ->andReturn($refund);

        return $refund;
    }

    /**
     * Mock payment method attachment
     */
    protected function mockPaymentMethodAttachment($paymentMethodId = 'pm_test123', $customerId = 'cus_test123')
    {
        $paymentMethod = Mockery::mock(PaymentMethod::class);
        $paymentMethod->id = $paymentMethodId;
        $paymentMethod->customer = $customerId;

        $this->stripeMock
            ->shouldReceive('attachPaymentMethod')
            ->andReturn($paymentMethod);

        return $paymentMethod;
    }

    /**
     * Mock partial capture
     */
    protected function mockPartialCapture($paymentId, $captureAmount)
    {
        $paymentIntent = $this->mockPaymentIntentRetrieval($paymentId);

        $paymentIntent->shouldReceive('capture')
            ->with(['amount_to_capture' => $captureAmount])
            ->once()
            ->andReturnSelf();

        return $paymentIntent;
    }

    /**
     * Cleanup Mockery after test
     */
    protected function tearDownMockery()
    {
        if (class_exists(Mockery::class)) {
            Mockery::close();
        }
    }
}
