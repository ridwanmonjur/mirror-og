<?php

namespace App\Http\Requests\Shop;

use App\Models\SystemCoupon;
use App\Models\Wallet;
use App\Models\Product;
use App\Models\NewCart;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Exception;

class WalletShopCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public float $total;
    public float $wallet_to_decrement;
    public float $cartPaymentSum = 0;
    public $cart; // Cache the cart instance
    public array $fee;
    public array $couponDetails = [
        'error' => null,
        'success' => null,
        'coupon' => null,
        'fee' => null
    ];

    public function rules(): array
    {
        return [
            'amount' => 'required',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = $this->attributes->get('user');
            $userId = $user->id;
            
            if (!$this->cart) {
                $this->cart = NewCart::getUserCart($userId);
            }
            $cart = $this->cart;

            $is_complete_payment = true;
            
            if ($cart->getCount() == 0) {
                $validator->errors()->add('cart', "Your cart is empty!");
                return;
            }

            foreach ($cart->getContent() as $item) {
                $product = Product::find($item->product_id);
                if (!$product || $product->quantity < $item->quantity) {
                    $validator->errors()->add('products', "Some items in your cart are no longer available.");
                    return;
                }
            }

            // load coupon
            [$fee, $isCouponApplied, $error, $coupon] = SystemCoupon::loadCoupon(
                $this->input('coupon_code'), 
                $this->input('amount'), 
                0.0, 
                'shop', 
                $user->id
            );
            
            $this->couponDetails = [
                'success' => $isCouponApplied,
                'error' => $error,
                'coupon' => $coupon,
                'fee' => $fee
            ];
            
            $this->wallet_to_decrement = $fee['finalFee'];

            $total = (float) $cart->getNumbers()->get('newTotal');
            $this->total = $total;

            $userWallet = Wallet::retrieveOrCreateCache($user->id);

            if (!$userWallet || $userWallet->usable_balance < 0) {
                $validator->errors()->add('wallet', "Your wallet is not available!");
                return;
            }

            if ($userWallet->usable_balance < $this->amount) {
                $is_complete_payment = false;
                $this->wallet_to_decrement = min(
                    $userWallet->usable_balance,
                    ($total - $this->cartPaymentSum) - config("constants.STRIPE.MINIMUM_RM")
                );
            }
            
            $pendingBeforeWallet = $this->total - $this->cartPaymentSum;
            $pendingAfterWallet = $pendingBeforeWallet - $this->wallet_to_decrement;

            if ($userWallet->usable_balance - $this->wallet_to_decrement < 0) {
                $validator->errors()->add(
                    'amount',
                    "Not enough money in your wallet!"
                );
                return;
            }

            if ($pendingAfterWallet <= config("constants.STRIPE.ZERO")) {
                $is_complete_payment = true;
            } else {
                $is_complete_payment = false;
            }

            if ($pendingBeforeWallet < 0) {
                $validator->errors()->add(
                    'amount',
                    "You've already paid for this order!"
                );
                return;
            }

            if ($pendingAfterWallet < -0.1) {
                $validator->errors()->add(
                    'amount',
                    "Have you already made another payment in another device/tab?"
                );
                return;
            }

            if ($this->wallet_to_decrement < 0) {
                $validator->errors()->add('wallet', "After adjusting for the next transaction, we cannot apply this payment with your wallet!");
                return;    
            }
          
            $stripe_after_wallet = $this->amount - $this->wallet_to_decrement;

            $this->attributes->add([
                'user_wallet' => $userWallet,
                'stripe_after_wallet' => $stripe_after_wallet,
                'complete_payment' => $is_complete_payment,
                'pending_total_after_wallet' => $pendingAfterWallet
            ]);
        });
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $error = $validator->errors()->first();
        throw new \Illuminate\Validation\ValidationException($validator, response()
            ->view('Participant.Error', compact('error')));
    }
}