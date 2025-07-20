<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\ShowShopCheckoutRequest;
use App\Http\Requests\Shop\WalletShopCheckoutRequest;
use App\Models\Wallet;
use App\Models\StripeConnection;
use App\Models\TransactionHistory;
use App\Order;
use App\Product;
use App\OrderProduct;
use App\NewCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class Cart2Controller extends Controller
{
    private $stripeClient;

    public function __construct(StripeConnection $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function showCheckout(ShowShopCheckoutRequest $request)
    {
        try {
            $user = $request->user();
            $prevForm = $request->prevForm;
            
            if ($request->paymentDone) {
                $message = "100% discount achieved through coupon.";
                session()->flash('successMessage', $message);
                return redirect()
                    ->route('confirmation.index')
                    ->with('successMessage', $message);
            }

            $fee = $request->fee; 
            $user = $request->user();
            $user->stripe_customer_id = $user->organizer()->value('stripe_customer_id');

            $user_wallet = Wallet::retrieveOrCreateCache($user->id);

            $paymentMethods = $this->stripeClient->retrieveAllStripePaymentsByCustomer([
                'customer' => $user->stripe_customer_id,
            ]);

            $walletStatusEnums = config('constants.DISCOUNT_STATUS');
            $walletStatus = is_null($user_wallet) || $user_wallet?->usable_balance < 0 ? $walletStatusEnums['ABSENT'] : $walletStatusEnums['COMPLETE'];
            $payment_amount_min = $pendingAfterWallet = 0.0;
            
            if ($walletStatus != $walletStatusEnums['ABSENT']) {
                $payment_amount_min = $fee['finalFee'];
                if ($user_wallet->usable_balance < $payment_amount_min) {
                    $payment_amount_min = min($user_wallet->usable_balance, $request->total - config('constants.STRIPE.MINIMUM_RM'));

                    $walletStatus = $walletStatusEnums['PARTIAL'];
                    $pendingAfterWallet = $request->total - $payment_amount_min;
                    if ($pendingAfterWallet < config('constants.STRIPE.MINIMUM_RM')) {
                        $walletStatus = $walletStatusEnums['INVALID'];
                    }
                }
            }

            // Use the cached cart from the request to avoid duplicate queries
            $cart = $request->cart ?: NewCart::getUserCart(auth()->id());
            
            // Use cart model method to get numbers
            $numbers = $cart->getNumbers();

            return view('shop.cartv2', [
                'cart' => $cart,
                'discount' => $numbers->get('discount'),
                'newSubtotal' => $numbers->get('newSubtotal'),
                'newTotal' => $numbers->get('newTotal'),
                'amount' => $request->total,
                'user' => $user,
                'user_wallet' => $user_wallet,
                'fee'=> $fee,
                'couponStatus' => $request->couponStatus,
                'prevForm' => $prevForm,
                'livePreview' => 1,
                'paymentMethods' => $paymentMethods,
                'payment_amount_min' => number_format($payment_amount_min),
                'walletStatusEnums' => $walletStatusEnums,
                'walletStatus' => $walletStatus,
                'paymentLowerMin' => config('constants.STRIPE.MINIMUM_RM'),
            ]);
        } catch (Exception $e) {
            Log::error('Shop cart2 checkout error: ' . $e->getMessage());
            session()->flash('errorMessage', $e->getMessage());
            return back()->with('errorMessage', $e->getMessage());
        }
    }

    public function walletCheckout(WalletShopCheckoutRequest $request)
    {
        $user = $request->user();
        DB::beginTransaction();
        try {
            $userWallet = $request->attributes->get('user_wallet');
            $isCompletePayment = $request->attributes->get('complete_payment');
            $pending_total_after_wallet = $request->attributes->get('pending_total_after_wallet');

            $walletAmount = $userWallet->usable_balance - $request->wallet_to_decrement;
            $currentAmount = $userWallet->current_balance - $request->wallet_to_decrement;
            $userWallet = Wallet::retrieveOrCreateCache($user->id);
            $userWallet->update([
                'usable_balance' => $walletAmount,
                'current_balance' => $currentAmount,
            ]);

            $transaction = new TransactionHistory([
                'name' => "Shop Order",
                'type' => 'Shop Order Payment',
                'link' => route('shop.index'),
                'amount' => $request->wallet_to_decrement,
                'summary' => "Shop order payment via wallet RM {$request->wallet_to_decrement}",
                'isPositive' => false,
                'date' => now(),
                'user_id' => $user->id,
            ]);

            $transaction->save();

            $order = $this->addToOrdersTables($request, null, $transaction->id);

            [$coupon] = $request->couponDetails;
            $coupon?->validateAndIncrementCoupon();

            $this->clearCartAndDecrease($cart);

            DB::commit();

            $message = $isCompletePayment ? 'Wallet payment applied successfully.' : "Partial payment via wallet applied.";

            return redirect()
                ->route('confirmation.index')
                ->with('successMessage', $message);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Shop wallet checkout error: ' . $e->getMessage());

            return redirect()
                ->route('shop.cart2')
                ->with('errorMessage', $e->getMessage());
        }
    }

    public function showCheckoutTransition(Request $request)
    {
        // For now, just redirect to confirmation page
        // This can be expanded later to handle Stripe payment confirmations similar to participant checkout
        return redirect()->route('confirmation.index')->with('successMessage', 'Your payment has succeeded!');
    }

    protected function clearCartAndDecrease($cart = null)
    {
        if (!$cart) {
            $userId = auth()->id();
            $cart = NewCart::getUserCart($userId);
        }
        $cart->clearItems();
        $this->decreaseQuantities($cart);
        session()->forget('coupon');
    }

    protected function addToOrdersTables($request, $error, $walletTransactionId = null)
    {
        try {
            $finalFee = isset($request->fee) ? $request->fee['finalFee'] : $request->wallet_to_decrement;
            $couponCode = $request->input('coupon_code');
            $discountAmount = isset($request->fee) ? $request->fee['discount'] : 0;

            $userId = auth()->id();
            $cart = NewCart::getUserCart($userId);
            $numbers = $cart->getNumbers();

            $order = Order::create([
                'user_id' => auth()->user() ? auth()->user()->id : null,
                'billing_email' => auth()->user()->email,
                'billing_name' => auth()->user()->name,
                'billing_address' => '',
                'billing_city' => '',
                'billing_province' => '',
                'billing_postalcode' => '',
                'billing_phone' => '',
                'billing_name_on_card' => '',
                'billing_discount' => $discountAmount,
                'billing_discount_code' => $couponCode,
                'billing_subtotal' => $numbers->get('newSubtotal'),
                'billing_tax' => 0,
                'billing_total' => $finalFee,
                'error' => $error,
                'wallet_transaction_id' => $walletTransactionId,
                'payment_method' => $walletTransactionId ? 'wallet' : 'stripe',
            ]);

            foreach ($cart->getContent() as $item) {
                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                ]);
            }

            return $order;
        } catch (Exception $e) {
            Log::error('Shop order creation error: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function decreaseQuantities($cart = null)
    {
        try {
            if (!$cart) {
                $userId = auth()->id();
                $cart = NewCart::getUserCart($userId);
            }
            foreach ($cart->getContent() as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->update(['quantity' => $product->quantity - $item->quantity]);
                }
            }
        } catch (Exception $e) {
            Log::error('Shop quantity decrease error: ' . $e->getMessage());
            throw $e;
        }
    }
}