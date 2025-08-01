<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RedeemCouponRequest;
use App\Http\Requests\User\SavePaymentMethodRequest;
use App\Http\Requests\User\TransactionHistoryRequest;
use App\Http\Requests\User\WithdrawalRequest as UserWithdrawalRequest;
use App\Http\Resources\TransactionHistoryResource;
use App\Models\SystemCoupon;
use App\Models\PaymentIntent;
use App\Models\RecordStripe;
use App\Models\StripeConnection;
use App\Models\TransactionHistory;
use App\Models\UserCoupon;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\UnauthorizedException;

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
            $isParticipant = $request->role === 'PARTICIPANT';
            $isManualCaptureMethod = false;
            if ($isParticipant) {
                $eventType = $request['metadata']['eventType'];
                $isManualCaptureMethod = isset($request['metadata'])
                    && isset($eventType)
                    && $eventType == config('constants.SIGNUP_STATUS.NORMAL');
            }

            // dd($isManualCaptureMethod);

            $user = $request->get('user');
            $isEmptyStripeCustomerId = empty($user->stripe_customer_id);
            if ($isEmptyStripeCustomerId) {
                $customer = $this->stripeClient->createStripeCustomer([
                    'email' => $request->email,
                    'name' => $request->name,
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
                            'name' => $request->name,
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
                    'payment_intent' => $paymentIntentStripe,
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
                    'payment_intent' => $paymentIntentStripe,
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
        $wallet = Wallet::retrieveOrCreateCache($user->id);

        return view('Users.PaymentMethod', [
            'wallet' => $wallet,
        ]);
    }

    /**
     * Save the payment method after successful setup
     */
    public function savePaymentMethod(SavePaymentMethodRequest $request)
    {
        $user = $request->get('user');
        $validatedData = $request->validated();

        try {
            $wallet = Wallet::retrieveOrCreateCache($user->id);

            $wallet->update([
                'bank_name' => $validatedData['bank_name'],
                'account_number' => $validatedData['account_number'],
                'account_holder_name' => $validatedData['account_holder_name'],
                'bank_last4' => substr($validatedData['account_number'], -4),
                'has_bank_account' => true,
                'bank_details_updated_at' => now(),
            ]);

            return redirect()->route('wallet.dashboard')
                ->with('success', 'Bank account details saved successfully! You can now make withdrawals.');

        } catch (Exception $e) {

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to save bank details. Please try again.');
        }
    }

    /**
     * Show the wallet dashboard
     */
    public function showWalletDashboard(Request $request)
    {
        $user = $request->get('user');

        $transactionsDemo = TransactionHistory::where('user_id', $user->id)
            ->limit(5)->orderBy('id', 'desc')->get()->toArray();

        $transactions = [
            'data' => $transactionsDemo,
            'has_more' => false,
            'next_cursor' => null,
        ];

        $wallet = Wallet::retrieveOrCreateCache($user->id);
        $demoCoupons = SystemCoupon::where('is_public', true)
            ->where('for_type', 'participant')
            ->orWhereHas('userCoupons', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['userCoupons' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->limit(2)
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        // dd($coupons);

        return view('Users.Dashboard', [
            'wallet' => $wallet,
            'demoCoupons' => $demoCoupons,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Show the wallet dashboard
     */
    public function showTransactions(TransactionHistoryRequest $request)
    {
        $user = $request->get('user');

        if ($request->expectsJson()) {
            return response()->json(TransactionHistory::getTransactionHistory($request, $user));
        }

        $transactions = TransactionHistory::getTransactionHistory(new TransactionHistoryRequest, $user);

        $wallet = Wallet::retrieveOrCreateCache($user->id);

        return view('Users.Transaction', [
            'wallet' => $wallet,
            'transactions' => $transactions,
        ]);

    }

    public function showCoupons(Request $request)
    {
        $user = $request->get('user');
        $code = trim($request->query('code', ''));
        $emptyCode = trim($request->query('empty', ''));

        if ($code === '') {
            $code = null;
        }

        if ($emptyCode === '') {
            $emptyCode = null;
        }

        $coupons = SystemCoupon::where('is_public', true)
            ->where('for_type', 'participant')
            ->orWhereHas('userCoupons', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['userCoupons' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        $wallet = Wallet::retrieveOrCreateCache($user->id);

        return view('Users.Coupon', [
            'wallet' => $wallet,
            'coupons' => $coupons,
            'code' => $code,
            'emptyCode' => $emptyCode,
        ]);
    }

    public function processWithdrawal(UserWithdrawalRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = $request->get('user');
            $wallet = $request->getWallet();
            $withdrawalAmount = $request->getWithdrawalAmount();

            Withdrawal::create([
                'user_id' => $user->id,
                'withdrawal' => $withdrawalAmount,
                'status' => Withdrawal::STATUS_PENDING,
                'requested_at' => now(),
            ]);

            $wallet->update([
                'usable_balance' => $wallet->usable_balance - $withdrawalAmount,
                'current_balance' => $wallet->current_balance - $withdrawalAmount,
            ]);

            TransactionHistory::create([
                'name' => "Wallet Funds Withdrawal: RM {$withdrawalAmount}",
                'type' => 'Withdrawal request',
                'link' => null,
                'amount' => $withdrawalAmount,
                'summary' => "{$wallet->bank_name} **** {$wallet->bank_last4}",
                'isPositive' => false,
                'date' => now(),
                'user_id' => $user->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully. Your funds will be credited to your bank account within 7 business days.',
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Unable to process withdrawal request. Please try again or contact support.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function checkoutTopup(Request $request)
    {

        $user = $request->get('user');
        $today = now();
        $dailyTotal = DB::table('wallet_topups')
            ->where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->sum('amount');
        $wallet = Wallet::retrieveOrCreateCache($user->id);

        if ($dailyTotal + $request->topup_amount > 1000) {
            return $this->showErrorParticipant('Transaction exceeds RM1000 limit.');
        }

        $amount = $request->topup_amount;

        return view('Users.TopupStripe', [
            'amount' => $amount,
            'wallet' => $wallet,
        ]);
    }

    public function topupCallback(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = $request->get('user');
            $status = $request->get('redirect_status');

            if ($status === 'succeeded' && $request->has('payment_intent_client_secret')) {
                $intentId = $request->get('payment_intent');
                $paymentIntent = $this->stripeClient->retrieveStripePaymentByPaymentId($intentId);
                $paymentMethodId = $paymentIntent['payment_method'];
                $paymentMethod = $this->stripeClient->retrievePaymentMethod($paymentMethodId);

                if ($paymentIntent['amount'] > 0) {
                    $wallet = Wallet::retrieveOrCreateCache($user->id);
                    $amount = $paymentIntent['amount'] / 100;
                    $cardBrand = ucfirst($paymentMethod->card->brand);
                    $cardLast4 = $paymentMethod->card->last4;
                    $wallet->update([
                        'usable_balance' => $wallet->usable_balance + $amount,
                        'current_balance' => $wallet->current_balance + $amount,
                    ]);

                    DB::table('wallet_topups')->insert([
                        'user_id' => $user->id,
                        'amount' => $amount,
                        'created_at' => now(),
                    ]);

                    TransactionHistory::create([
                        'name' => "Wallet Fund Purchase: RM {$amount}",
                        'type' => 'Funds Purchase',
                        'link' => null,
                        'amount' => $amount,
                        'summary' => "{$cardBrand} **** {$cardLast4}",
                        'date' => now(),
                        'user_id' => $user->id,
                    ]);

                    RecordStripe::createTransaction($paymentIntent, $paymentMethod, $user->id, $request->query('saveDefault'), $request->query('savePayment'));

                    DB::commit();

                    return redirect()->route('wallet.dashboard')->with('success', 'Successfully added RM '.number_format($amount, 2).' to your wallet.');

                }
            }

            DB::rollback();

            return $this->showErrorGeneral('Could not find payment information!');

        } catch (ModelNotFoundException|UnauthorizedException $e) {
            DB::rollback();

            return $this->showErrorGeneral($e->getMessage());
        } catch (Exception $e) {
            DB::rollback();

            return $this->showErrorGeneral($e->getMessage());

        }
    }

    public function redeemCoupon(RedeemCouponRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $request->get('user');
            $coupon = $request->getCoupon();
            $userCoupon = $request->getUserCoupon();
            $wallet = Wallet::retrieveOrCreateCache($user->id);
            $newBalance = $wallet->usable_balance + $coupon->amount;

            $wallet->update([
                'usable_balance' => $newBalance,
                'current_balance' => $newBalance,
            ]);

            if (! $userCoupon) {
                $userCoupon = UserCoupon::create([
                    'user_id' => $user->id,
                    'coupon_id' => $coupon->id,
                    'redeemed_at' => null,
                    'redeemable_count' => 0,
                ]);
            }

            $userCoupon->update([
                'redeemed_at' => now(),
            ]);

            $userCoupon->increment('redeemable_count');

            TransactionHistory::create([
                'name' => "Wallet Coupon Registration: RM {$coupon->amount}",
                'type' => 'Coupon Registration',
                'link' => null,
                'amount' => $coupon->amount,
                'summary' => "Coupon  {$coupon->code}",
                'date' => now(),
                'user_id' => $user->id,
            ]);
            DB::commit();

            $extraMessage = '';
            if ($userCoupon->redeemable_amount-1 > 0) {
                $extraMessage = "You can still redeem it {$userCoupon->redeemable_amount} more times";
            }

            return response()->json([
                'success' => true,
                'message' => "Coupon redeemed successfully!{$extraMessage}",
            ], 200);

        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while redeeming the coupon. Please try again later.',
            ], 500);
        }
    }
}
