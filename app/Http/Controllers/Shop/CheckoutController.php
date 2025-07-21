<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\ShowShopCheckoutRequest;
use App\Http\Requests\Shop\WalletShopCheckoutRequest;
use App\Models\Wallet;
use App\Models\StripeConnection;
use App\Models\TransactionHistory;
use App\Models\RecordStripe;
use App\Models\SystemCoupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderProduct;
use App\Models\NewCart;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CheckoutController extends Controller
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

            $cart = $request->cart ?: NewCart::getUserCart(auth()->id());
            
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
        $user = $request->attributes->get('user');
        DB::beginTransaction();
        try {
            $userWallet = $request->attributes->get('user_wallet');
            $isCompletePayment = $request->attributes->get('complete_payment');

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
            ['coupon' => $coupon, 'fee' => $fee] = $request->couponDetails;

            $cart = NewCart::getUserCart($user->id);
            $this->addToOrdersTables( $user, $cart,  $coupon, $fee);
            $this->clearCartAndDecreaseStock($cart);

            DB::commit();

            $message = $isCompletePayment ? 'Wallet payment applied successfully.' : "Partial payment via wallet applied.";

            return redirect()
                ->route('confirmation.index')
                ->with('successMessage', $message);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Shop wallet checkout error: ' . $e);

            return $this->showErrorParticipant($e->getMessage());
        }
    }


    public function showCheckoutTransition(Request $request)
    {
        $paymentIntent = [];
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $status = $request->get('redirect_status');
            
            if (($status === 'succeeded' || $status === 'requires_capture') && $request->has('payment_intent_client_secret')) {
                $intentId = $request->get('payment_intent');
                $paymentIntent = $this->stripeClient->retrieveStripePaymentByPaymentId($intentId);
                $paymentMethodId = $paymentIntent['payment_method'];
                $paymentMethod = $this->stripeClient->retrievePaymentMethod($paymentMethodId);

                $paymentDone = (float) $paymentIntent['amount'] / 100;

                if ($paymentIntent['amount'] > 0 && ($paymentIntent['amount_capturable'] === $paymentIntent['amount'] || $paymentIntent['amount_received'] === $paymentIntent['amount'])) {
                    
                    $couponCode = $paymentIntent['metadata']['couponCode'] ?? null;
                    $coupon = null;
                    if ($couponCode) {
                        [$fee, , , $coupon] = SystemCoupon::loadCoupon($couponCode, $paymentIntent['metadata']['cartTotal'], 0.0, 'shop', $user->id);
                        $coupon?->validateAndIncrementCoupon($couponCode, $user->id);
                    } else {
                        $coupon = null;
                        $fee = SystemCoupon::emptyOrgCoupon([],  $paymentDone);
                    }

                    $transaction = RecordStripe::createTransaction(
                        $paymentIntent, 
                        $paymentMethod, 
                        $user->id, 
                        $request->query('saveDefault'), 
                        $request->query('savePayment')
                    );

                    $history = new TransactionHistory([
                        'name' => "Shop Order",
                        'type' => 'Shop Order Payment',
                        'link' => route('shop.index'),
                        'amount' => $paymentDone,
                        'summary' => "Shop order payment via Stripe RM {$paymentDone}",
                        'isPositive' => false,
                        'date' => now(),
                        'user_id' => $user->id,
                    ]);

                    $history->save();

                    $cart = NewCart::getUserCart($user->id);
                    $this->addToOrdersTables( $user, $cart, $coupon, $fee);
                    $this->clearCartAndDecreaseStock($cart);

                    DB::commit();

                    return redirect()
                        ->route('confirmation.index')
                        ->with('successMessage', 'Your payment has succeeded!');
                }
            }

            DB::rollBack();
            return $this->showErrorParticipant('Your payment has failed unfortunately!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Shop checkout transition error: ' . $e);
            
            return $this->showErrorParticipant('Your payment has failed unfortunately!');
        }
    }

    protected function clearCartAndDecreaseStock(NewCart $cart )
    {
        $cart->clearItems();
        foreach ($cart->getContent() as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->update(['quantity' => $product->quantity - $item->quantity]);
            }
        }

        session()->forget('coupon');
    }

    protected function addToOrdersTables(User $user, NewCart $cart, ? SystemCoupon $coupon, array $fee )
    {
        try {
            $order = Order::create([
                'user_id' => $user->id,
                'billing_discount' => $fee['discount'] ?? 0,
                'billing_discount_code' => $coupon->code ?? 0,
                'billing_subtotal' =>$fee['discount'] ?? 0,
                'billing_total' => $fee['finalFee'] ?? 0,
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

    

    public function thankyou(): View | RedirectResponse
    {
        return view('shop.thankyou');
    }
}