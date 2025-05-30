<?php


namespace App\Http\Controllers\Shared;

use App\Exceptions\BankAccountNeededException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\RedeemCouponRequest;
use App\Http\Requests\User\TransactionHistoryRequest;
use App\Http\Requests\User\WithdrawalRequest as UserWithdrawalRequest;
use App\Http\Requests\WithdrawalRequest;
use App\Http\Requests\WithdrawalRequest\WithdrawalRequest as WithdrawalRequestWithdrawalRequest;
use App\Http\Resources\TransactionHistoryResource;
use App\Models\ParticipantCoupon;
use App\Models\PaymentIntent;
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
    $wallet = Wallet::retrieveOrCreateCache($user->id);

    return view('Users.PaymentMethod', [
        'wallet' => $wallet,
    ]);
}

    /**
     * Save the payment method after successful setup
     */
    public function savePaymentMethod(Request $request)
    {
        $user = $request->get('user');
    
        $validatedData = $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|min:8|max:20|regex:/^[0-9\-]+$/',
            'account_holder_name' => 'required|string|max:100',
        ], [
            'bank_name.required' => 'Please select a bank',
            'account_number.required' => 'Account number is required',
            'account_number.min' => 'Account number must be at least 8 characters',
            'account_number.max' => 'Account number cannot exceed 20 characters',
            'account_number.regex' => 'Account number can only contain numbers and hyphens',
            'account_holder_name.required' => 'Account holder name is required',
            'account_holder_name.max' => 'Account holder name cannot exceed 100 characters',
        ]);

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
    public function showWalletDashboard(Request $request)
    {
        $user = $request->get('user');

        $transactionsDemo = TransactionHistory::where('user_id', $user->id)
            ->limit(5)->get()->toArray();

        $transactions = [
            'data' => $transactionsDemo,
            'has_more' => false,
            'next_cursor' => null
        ];

        $wallet = Wallet::retrieveOrCreateCache($user->id);
        $couponsQ = ParticipantCoupon::where('is_public', true)
            ->orWhereHas('userCoupons', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();
        
        $demoCoupons = $couponsQ->take(2)->toArray();
        $coupons = $couponsQ->toArray();

        return view('Users.Dashboard', [
            'wallet' => $wallet,
            'coupons' => $coupons,
            'demoCoupons' => $demoCoupons,
            'transactions' => $transactions
        ]);
    }

     /**
     * Show the wallet dashboard
     */
    public function showTransactions(TransactionHistoryRequest $request)
    {
        $user = $request->get('user');

        if ($request->expectsJson()) {
            return response()->json($this->getTransactionHistory($request, $user));
         }

        $transactions = $this->getTransactionHistory( new TransactionHistoryRequest(), $user);

        $wallet = Wallet::retrieveOrCreateCache($user->id);
        
        return view('Users.Transaction', [
            'wallet' => $wallet,
            'transactions' => $transactions
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
                'name' => "Withdrawal request: {$withdrawalAmount}",
                'type' => "Withdrawal request",
                'link' => null,
                'amount' => $withdrawalAmount,
                'summary' => "{$wallet->bank_name} **** {$wallet->bank_last4}",
                'isPositive' => false,
                'date' => now(),
                'user_id' => $user->id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully. Your funds will be credited to your bank account within 7 business days.',
            ], 201);

        }   catch (Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to process withdrawal request. Please try again or contact support.',
                'error' => $e->getMessage() 
            ], 500);
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


    public function topupCallback(Request $request){
        try {
            DB::beginTransaction();
            $user = $request->get('user');
            $userId = $user->id;
            $status = $request->get('redirect_status');

            if ($status === 'succeeded' && $request->has('payment_intent_client_secret')) {
                $intentId = $request->get('payment_intent');
                $paymentIntent = $this->stripeClient->retrieveStripePaymentByPaymentId($intentId);
                
                if ($paymentIntent['amount'] > 0) {
                    $wallet = Wallet::retrieveOrCreateCache($user->id);
                    $amount = $paymentIntent['amount'] / 100;
                    $wallet->update([
                        'usable_balance' => $wallet->usable_balance + $amount,
                        'current_balance' => $wallet->current_balance + $amount,
                    ]);
                   

                    DB::commit();
                    return redirect()->route('wallet.dashboard')->with('success', 'Successfully added RM ' . number_format($amount, 2) . ' to your wallet.');

                }
            }
            DB::rollback();
            return $this->showErrorGeneral("Could not find payment information!");

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
        try {
            $user = $request->get('user');
            $code = $request->coupon_code;
            $coupon = $request->getCoupon();
            $wallet = Wallet::retrieveOrCreateCache($user->id);
            $newBalance = $wallet->usable_balance + $coupon->amount;
            
            $wallet->update([
                'usable_balance' => $newBalance,
                'current_balance' => $newBalance,
            ]);
    
            UserCoupon::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'coupon_id' => $coupon->id,
                ],
                [
                    'redeemed_at' => now()
                ]
            );
    
            return response()->json([
                'success' => true,
                'message' => 'Coupon redeemed successfully!',
            ], 200);
    
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while redeeming the coupon. Please try again later.'
            ], 500);
        }
    }
}
