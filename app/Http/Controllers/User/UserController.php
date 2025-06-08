<?php

namespace App\Http\Controllers\User;

use App\Exceptions\SettingsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\BannerUpdateRequest;
use App\Http\Requests\User\TransactionHistoryRequest;
use App\Http\Requests\User\UpdateSettingsRequest;
use App\Models\StripeConnection;
use App\Models\TeamProfile;
use App\Models\UserProfile;
use App\Models\NotifcationsUser;
use App\Models\TransactionHistory;
use App\Models\User;
use App\Models\Wallet;
use App\Services\SettingsService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    private $stripeClient;
    private $settingsService;

    public function __construct(StripeConnection $stripeClient, SettingsService $settingsService)
    {
        $this->stripeClient = $stripeClient;
        $this->settingsService = $settingsService;
    }

    public function changeEmail(Request $request, $token, $newEmail)
    {
        ['success' => $success, 'message' => $message, 'route' => $route] = $this->settingsService->changeMailAction($token, $newEmail);

        if (!$success) {
            return $this->showErrorGeneral($message);
        }

        return redirect()->route($route)->with('success', $message);
    }

    public function viewNotifications(Request $request)
    {
        $user = $request->attributes->get('user');
        $perPage = $request->input('per_page', 5);
        $pageNumber = $request->input('page', 1);
        $type = $request->input('type', 'all');
        $page = NotifcationsUser::where('user_id', $user->id)
            ->when($type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->orderBy('id', 'desc')
            ->simplePaginate($perPage, ['*'], 'notification_page', $pageNumber);
        return response()->json([
            'data' => [$type => $page->items()],
            'hasMore' => $page->hasMorePages(),
            'success' => true,
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $user = $request->attributes->get('user');

        $notification = NotifcationsUser::where('user_id', $user->id)->findOrFail($id);
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return response()->json([
            'message' => 'Notification marked as read',
            'success' => true,
        ]);
    }

    public function createNotification(Request $request)
    {
        try {
            $user = $request->attributes->get('user');
            $notification = [
                'user_id' => $request->texterId,
                'img_src' => $user->userBanner,
                'link' => route('user.message.view', [
                    'userId' => $user->id,
                ]),
                'type' => 'social',
                'created_at' => DB::raw('NOW()'),
                'html' => <<<HTML
                    <span class="notification-gray">
                        <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/participant/{$user->id}">
                            {$user->name}</button>
                        has texted you.
                    </span>
                HTML
            ,
            ];

            NotifcationsUser::insertWithCount([$notification]);
            return response()->json(['success' => true, 'message' => 'Succeeded'], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function replaceBackground(BannerUpdateRequest $request)
    {
        try {
            $user = $request->attributes->get('user');
            $validated = $request->validated();
            if ($request->teamId) {
                $profile = TeamProfile::where('team_id', $request->teamId)->firstOrNew();
                $profile->team_id = $request->teamId;
            } else {
                $profile = UserProfile::where('user_id', $user->id)->firstOrNew();
                $profile->user_id = $user->id;
            }

            $oldBanner = $profile->backgroundBanner;
            if ($request->backgroundBanner) {
                $user->uploadBackgroundBanner($request, $profile);
                $user->destroyUserBanner($oldBanner);
            } else {
                $profile->fill($validated);
                if ($profile->backgroundColor || $profile->backgroundGradient) {
                    $profile->backgroundBanner = null;
                }
                $profile->save();
            }

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Succeeded', 'data' => $profile], 201);
            }

            return back();
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            session()->flash('errorMessage', $e->getMessage());

            return back();
        }
    }

    public function settings(Request $request)
    {
        $user = $request->attributes->get('user');
        $wallet = Wallet::retrieveOrCreateCache($user->id);
        $user->is_null_password = empty($user->password);
        $isShowFirstInnerAccordion = $request->has('methods_limit');
        $isShowSecondInnerAccordion = $request->has('history_limit');
        $isShowNextAccordion = $isShowSecondInnerAccordion || $isShowFirstInnerAccordion;
        $limit_methods = $request->input('methods_limit', 10); // 10
        $limit_history = $request->input('history_limit', 10); // 100
        $transactions = TransactionHistory::getTransactionHistory( new TransactionHistoryRequest(), $user);


        if ($user->stripe_customer_id) {
            try {
                $paymentMethodsQuery = DB::table('saved_cards')
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->limit($limit_methods + 1);
            
                $paymentMethods = $paymentMethodsQuery->get();
                $hasMorePayments = isset($paymentMethods[$limit_methods]);

                $paymentHistoryQuery = DB::table('saved_payments')
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->limit($limit_history + 1);
                
                $paymentHistory = $paymentHistoryQuery->get();
                $hasMoreHistory = isset($paymentHistory[$limit_history]);
            } catch (Exception $e) {
                $paymentMethods = new Collection();
                $paymentHistory = new Collection();
                $hasMorePayments = $hasMoreHistory = false;
            }
        } else {
            $paymentMethods = new Collection();
            $paymentHistory = new Collection();
            $hasMorePayments = $hasMoreHistory = false;
        }

        $settingsAction = config('constants.SETTINGS_ROUTE_ACTION');
        return view('Users.Settings', compact('user', 'paymentMethods', 'paymentHistory', 'settingsAction', 
                'limit_methods', 'limit_history', 'hasMorePayments', 'hasMoreHistory', 'wallet', 'transactions',
                'isShowFirstInnerAccordion', 'isShowSecondInnerAccordion', 'isShowNextAccordion'
            )
        );
    }

    /**
     * Unlink user's bank account
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function unlinkBankAccount(Request $request): JsonResponse
    {
        try {
            $user = $request->get('user');
            $wallet = Wallet::retrieveOrCreateCache($user->id);

            if (!$wallet->has_bank_account) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No bank account is currently linked to your account',
                    ],
                    400,
                );
            }

            $wallet->update([
                'has_bank_account' => false,
                'bank_last4' => null,
                'bank_name' => null,
                'account_number' => null,
                'account_holder_name' => null,
                'bank_details_updated_at' => null,
            ]);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Bank account unlinked successfully',
                ],
                200,
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'An error occurred while unlinking bank account',
                ],
                500,
            );
        }
    }

    public function changeSettings(UpdateSettingsRequest $request): JsonResponse
    {
        try {
            $result = $this->settingsService->changeSettings($request);

            return response()->json($result);
        } catch (SettingsException $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                400,
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'An unexpected error occurred',
                ],
                500,
            );
        }
    }
}
