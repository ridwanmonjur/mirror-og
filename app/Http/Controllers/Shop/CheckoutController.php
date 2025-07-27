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
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    private $stripeClient;
    private $cartService;

    public function __construct(StripeConnection $stripeClient, CartService $cartService)
    {
        $this->stripeClient = $stripeClient;
        $this->cartService = $cartService;
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

            $has_wallet_balance = !is_null($user_wallet) && $user_wallet->usable_balance > 0;
            $can_pay_full_amount = $has_wallet_balance && $user_wallet->usable_balance >= $request->total;
            $wallet_shortfall = $has_wallet_balance ? max(0, $request->total - $user_wallet->usable_balance) : $request->total;

            $cart = $request->cart ?: $this->cartService->getUserCart(auth()->id());
            
            $numbers = $cart->getNumbers();

            return view('shop.checkout', [
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
                'has_wallet_balance' => $has_wallet_balance,
                'can_pay_full_amount' => $can_pay_full_amount,
                'wallet_shortfall' => $wallet_shortfall,
                'total_amount' => $request->total,
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

            $cart = $this->cartService->getUserCart($user->id);
            $this->cartService->validateStock($cart);
            $this->cartService->addToOrdersTables($cart, $user, $coupon, $fee);
            $this->cartService->clearItemsAndDecreaseStock($cart);

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

                    $cart = $this->cartService->getUserCart($user->id);
                    $this->cartService->validateStock($cart);
                    $this->cartService->addToOrdersTables($cart, $user, $coupon, $fee);
                    $this->cartService->clearItemsAndDecreaseStock($cart);

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




    

    public function thankyou(): View | RedirectResponse
    {
        return view('shop.thankyou');
    }
}