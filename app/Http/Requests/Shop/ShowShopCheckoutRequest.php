<?php

namespace App\Http\Requests\Shop;

use App\Models\SystemCoupon;
use App\Models\Product;
use App\Models\NewCart;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class ShowShopCheckoutRequest extends FormRequest
{
    public float $total;

    public array $prevForm;

    public float $cartPaymentSum = 0;

    public $cart; // Cache the cart instance

    public array $fee;

    public bool $paymentDone = false;

    public array $couponStatus = [
        'error' => null,
        'success' => null,
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'nullable|numeric|min:0',
            'coupon_code' => 'nullable|string|max:255',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateCartAndPayment($validator);
        });
    }

    private function validateCartAndPayment($validator)
    {
        $user = $this->attributes->get('user');

        $userId = $user->id;

        $this->cart = NewCart::getUserCart($userId);

        $cart = $this->cart;

        $prevForm = [
            'coupon_code' => $this->input('coupon_code') ?? null,
        ];

        $this->prevForm = $prevForm;

        if ($cart->getCount() == 0) {
            $validator->errors()->add(
                'cart',
                'Your cart is empty!'
            );

            return;
        }

        // Check product availability
        foreach ($cart->getContent() as $item) {
            $product = Product::find($item->product_id);
            if (! $product) {
                $validator->errors()->add(
                    'products',
                    'Some items in your cart are no longer available.'
                );

                return;
            }
        }

        $total = (float) $cart->getNumbers()->get('newTotal');
        $this->total = $total;
        $paymentOptionLower = config('constants.STRIPE.MINIMUM_RM');
        $paymentOptionHigher = $total - $paymentOptionLower;

        // For GET requests, use the total as default amount if not provided
        $amount = $this->input('amount', $total);

        [$fee, $isCouponApplied, $error, $coupon] = SystemCoupon::loadCoupon(
            $this->input('coupon_code'),
            $amount,
            0.0,
            'shop',
            $user->id
        );

        $this->couponStatus = [
            'success' => $isCouponApplied,
            'error' => $error,
        ];

        $this->fee = $fee;

        $soFarPaid = $this->cartPaymentSum + $fee['totalFee'];
        $pending = $total - $soFarPaid;

        if ($fee['finalFee'] <= config('constants.STRIPE.ZERO')) {
            if (! $isCouponApplied) {
                $validator->errors()->add(
                    'amount',
                    "You have tried to pay RM {$fee['finalFee']}. If you don't pay full amount, you have to pay higher than RM $paymentOptionLower."
                );

                return;
            }

            $this->paymentDone = true;

            return;
        }

        if ($fee['finalFee'] < $paymentOptionLower) {
            $validator->errors()->add(
                'amount',
                "You have tried to pay RM {$fee['finalFee']}. If you don't pay full amount, you have to pay higher than RM $paymentOptionLower."
            );

            return;
        }

        if ($pending > config('constants.STRIPE.ZERO') && $soFarPaid > $paymentOptionHigher) {
            $minimum = ($total - $this->cartPaymentSum) - $paymentOptionLower;
            $validator->errors()->add(
                'amount',
                "You have tried to pay RM {$fee['finalFee']}. If you don't pay full amount, you have to pay higher than RM $paymentOptionLower and lower than RM $minimum."
            );

            return;
        }

        $pendingBeforePayment = $this->total - $this->cartPaymentSum;
        if ($pendingBeforePayment < config('constants.STRIPE.ZERO')) {
            $validator->errors()->add(
                'amount',
                "You've already paid for this order!"
            );

            return;
        }
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $error = $validator->errors()->first();
        throw new \Illuminate\Validation\ValidationException($validator, response()
            ->view('Participant.Error', compact('error')));
    }
}
