<?php


namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\TransactionHistoryRequest;
use App\Http\Resources\TransactionHistoryResource;
use App\Models\ParticipantCoupon;
use App\Models\PaymentIntent;
use App\Models\StripeConnection;
use App\Models\TransactionHistory;
use App\Models\UserCoupon;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StripeController extends Controller
{
    private $stripeClient;

    public function __construct(StripeConnection $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function stripeCardIntentCreate(Request $request)
    {
        try {
            DB::beginTransaction();
            $customer = $paymentIntentStripe = null;
            $paymentIntentStatus = 'created';
            $customerStatus = 'created';
            $isParticipant = $request->role === "PARTICIPANT";
            $isManualCaptureMethod = false;
            if ($isParticipant) {
                $eventType = $request['metadata']['eventType'];
                $isManualCaptureMethod = isset($request['metadata'])
                    && isset($request['metadata']['eventType'])
                    && $eventType === "normal";
            }

            $user = $request->get('user');
            $isEmptyStripeCustomerId = empty($user->stripe_customer_id);
            if ($isEmptyStripeCustomerId) {
                $customer = $this->stripeClient->createStripeCustomer([
                    'email' => $request->email,
                    'name' => $request->name
                ]);
            } else {
                try {
                    $customerStatus = 'retrieved';
                    $customer = $this->stripeClient->retrieveStripeCustomer($user->stripe_customer_id);
                } catch (Exception $e) {
                    $customerStatus = 'created';
                    $customer = $this->stripeClient->createStripeCustomer(
                        [
                            'email' => $request->email,
                            'name' => $request->name
                        ]
                    );
                }
            }

            $paymentIntentStripe = null;

            $paymentIntentStripeBody = [
                'amount' => +$request->paymentAmount * 100,
                'metadata' => $request->metadata,
            ];

            if ($isManualCaptureMethod) {
                $paymentIntentStripeBody['capture_method'] = 'manual';
            } else {
                $paymentIntentStripeBody['capture_method'] = 'automatic_async';
            }

            $paymentIntentStripeBody['customer'] = $customer->id;

            $paymentIntentStripe = $this->stripeClient->createPaymentIntent($paymentIntentStripeBody);

            $paymentIntentDBBody = [
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'payment_intent_id' => $paymentIntentStripe->id,
                'amount' => $request->paymentAmount,
                'status' => $paymentIntentStripe->status,
            ];

            PaymentIntent::insert($paymentIntentDBBody);


            $responseData = [
                'success' => 'true',
                'message' => 'Payment intent creation successful',
                'data' => [
                    'client_secret' => $paymentIntentStripe->client_secret,
                    'customer_status' => $customerStatus,
                    'payment_status' => $paymentIntentStatus,
                    'customer' => $customer,
                    'payment_intent' => $paymentIntentStripe
                ],
            ];

            if ($isEmptyStripeCustomerId) {
                $user->stripe_customer_id = $customer->id;
                $user->save();
            }

            DB::commit();

            return response()->json($responseData);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => 'false',
                'retrieved' => $isEmptyStripeCustomerId,
                'message' => $e->getMessage(),
                'data' => [
                    'client_secret' => null,
                    'customer_status' => $customerStatus,
                    'payment_status' => $paymentIntentStatus,
                    'customer' => $customer,
                    'payment_intent' => $paymentIntentStripe
                ],
            ]);
        }
    }

    /**
 * Show the bank details form
 */
public function showPaymentMethodForm(Request $request)
{
    $user = $request->get('user');
    $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);

        $customer = $this->stripeClient->createStripeCustomer([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);
        $wallet->update(['stripe_customer_id' => $customer->id]);

        $setupIntent = $this->stripeClient->createSetupIntent([
            'customer' => $wallet->stripe_customer_id,
            'usage' => 'off_session', // Allow future off-session usage
        ]);

        return view('Users.PaymentMethod', [
            'clientSecret' => $setupIntent->client_secret,
            'wallet' => $wallet,
        ]);
    }

    /**
     * Save the payment method after successful setup
     */
    public function savePaymentMethod(Request $request)
    {
        $user = $request->get('user');
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);
    
        $paymentMethodId = $request->input('payment_method_id');
        $paymentMethod = $this->stripeClient->retrievePaymentMethod($paymentMethodId);
    
        if (!$wallet->stripe_customer_id) {
            $customer = $this->stripeClient->createStripeCustomer([
                'email' => $user->email,
                'name' => $user->name,
                'metadata' => [
                    'user_id' => $user->id,
                ],
            ]);
            
            $customerStripeId = $customer->id;
        } else {
            $customerStripeId = $wallet->stripe_customer_id;
        }

        $wallet->update([
            'stripe_customer_id' => $customerStripeId,
            'payment_method_id' => $paymentMethodId,
            'has_bank_account' => true,
            'bank_last4' => $paymentMethod->card->last4 ?? $paymentMethod->us_bank_account->last4 ?? null,
            'bank_name' => $paymentMethod->card->brand ?? $paymentMethod->us_bank_account->bank_name ?? null,
        ]);
        // dd($wallet);
    
        return redirect()->route('wallet.dashboard')->with('success', 'Payment method added successfully!');
    }

    private function getTransactionHistory(TransactionHistoryRequest $request, $user) {
        $fields = [
            'name',
            'type',
            'link',
            'amount',
            'summary',
            'isPositive',
            'date',
            'user_id',
            'id'
        ];

        $query = TransactionHistory::where('user_id', $user->id)
            ->select($fields);

        $result = $query->cursorPaginated(
            $request->getPerPage(),
            $request->getCursor(),
        );

        $result['data'] = TransactionHistoryResource::collection($result['data'])->toArray(request());

        return $result;
    }

    /**
     * Show the wallet dashboard
     */
    public function showWalletDashboard(TransactionHistoryRequest $request)
    {
        $user = $request->get('user');

        if ($request->expectsJson()) {
            return response()->json($this->getTransactionHistory($request, $user));
         }

        $transactions = $this->getTransactionHistory( new TransactionHistoryRequest(), $user);


        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);
        $couponsQ = ParticipantCoupon::where('is_public', true)
            ->orWhereHas('userCoupons', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();
        
        $demoCoupons = $couponsQ->take(2)->toArray();
        $coupons = $couponsQ->toArray();
        // dd($transactions);

        return view('Users.Dashboard', [
            'wallet' => $wallet,
            'coupons' => $coupons,
            'demoCoupons' => $demoCoupons,
            'transactions' => $transactions
        ]);
    }

    public function processWithdrawal(Request $request)
    {
        $user = $request->get('user');
        $wallet = Wallet::where('user_id', $user->id)->firstOrFail();
        // dd($wallet);

        if (!$wallet->isReadyForPayouts()) {
            return redirect()->route('wallet.save-payment-method')->with('error', 'Please add a payment method first.');
        }

        $request->validate([
            'withdrawal_amount' => 'required|numeric|min:5|max:' . $wallet->usable_balance,
        ]);

        $amount = $request->withdrawal_amount * 100; 

        try {
            $this->stripeClient->createPayout(
                $wallet->stripe_customer_id,
                $wallet->payment_method_id,
                $amount
            );

            $wallet->update([
                'usable_balance' => $wallet->usable_balance - ($amount / 100),
                'current_balance' => $wallet->current_balance - ($amount / 100),
                'last_payout_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Withdrawal of RM ' . number_format($request->withdrawal_amount, 2) . ' processed successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Withdrawal failed: ' . $e->getMessage());
        }
    }

    public function checkoutTopup(Request $request)
    {
        $request->validate([
            'topup_amount' => 'required|numeric|min:5',
        ]);

        $user = $request->get('user');
        $amount = $request->topup_amount;
        return view('Users.TopupStripe', [
            'amount' => $amount
        ]);
    }

    public function processTopup(Request $request){
        $request->validate([
            'topup_amount' => 'required|numeric|min:5',
        ]);

        $user = $request->get('user');
        $amount = $request->topup_amount;
        try {
            $wallet = Wallet::where('user_id', $user->id)->firstOrFail();
            $wallet->update([
                'usable_balance' => $wallet->usable_balance + $amount,
                'current_balance' => $wallet->current_balance + $amount,
            ]);

            return redirect()->route('wallet.dashboard')->with('success', 'Successfully added RM ' . number_format($amount, 2) . ' to your wallet.');

        } catch (Exception $e) {
            return redirect()->route('wallet.dashboard')->with('error', 'Topup failed: ' . $e->getMessage());
        }
    }

    public function redeemCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $user = $request->get('user');
        $code = $request->coupon_code;

        $coupon = UserCoupon::where('code', $code)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();

        if (!$coupon) {
            return redirect()->back()->with('error', 'Invalid or expired coupon code.');
        }

        $usedCoupon = UserCoupon::where('user_id', $user->id)
            ->where('coupon_id', $coupon->id)
            ->first();

        if ($usedCoupon) {
            return redirect()->back()->with('error', 'You have already used this coupon.');
        }

        $wallet = Wallet::where('user_id', $user->id)->firstOrFail();
        $wallet->update([
            'usable_balance' => $wallet->usable_balance + $coupon->amount,
            'current_balance' => $wallet->usable_balance + $coupon->amount,
        ]);

        UserCoupon::create([
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
            'redeemed_at' => now()
        ]);

        return redirect()->back()->with('success', 'Successfully redeemed coupon for RM ' . number_format($coupon->amount, 2));
    }
}
