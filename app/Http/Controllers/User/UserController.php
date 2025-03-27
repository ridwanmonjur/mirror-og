<?php

namespace App\Http\Controllers\User;

use App\Exceptions\SettingsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\BannerUpdateRequest;
use App\Http\Requests\User\UpdateSettingsRequest;
use App\Models\StripePayment;
use App\Models\TeamProfile;
use App\Models\UserProfile;
use App\Models\NotifcationsUser;
use App\Models\User;
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

    public function __construct(StripePayment $stripeClient, SettingsService $settingsService)
    {
        $this->stripeClient = $stripeClient;
        $this->settingsService = $settingsService;
    }

    public function viewNotifications(Request $request)
    {
        $user = $request->attributes->get('user');
        $perPage = $request->input('per_page', 5); 
        $pageNumber =  $request->input('page', 1); 
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
            'success' => true
        ]);

    }

    public function markAsRead(Request $request, $id)
    {
        $user = $request->attributes->get('user');
        
        $notification = NotifcationsUser::where('user_id', $user->id)
            ->findOrFail($id);
        if (!$notification->is_read) $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read',
            'success' => true
        ]);
    }

    public function createNotification(Request $request) {
        try {
            $user = $request->attributes->get('user');
            $notification = [
                'user_id' => $request->texterId,
                'img_src' => $user->userBanner,
                'link' =>  route('user.message.view', [
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
                HTML,
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
        $user->is_null_password = empty($user->password);
        $limit_methods = $request->input('methods_limit', 10); // 10
        $limit_history = $request->input('history_limit', 10); // 100


        $paramsMethods = [
            'customer' => $user->stripe_customer_id,
            'limit' => $limit_methods + 1,
            'type' => 'card',
        ];

        $paramsHisotry = [
            'query' => "customer:'{$user->stripe_customer_id}' AND status:'succeeded'",
            'limit' => $limit_history + 1,
        ];

        if ($user->stripe_customer_id) {
            try {
                $paymentMethods = $this->stripeClient->retrieveAllStripePaymentsByCustomer($paramsMethods);
                $paymentHistory = $this->stripeClient->searchStripePaymenst($paramsHisotry);
                $hasMorePayments = array_key_exists($limit_methods, $paymentMethods->data);
                $hasMoreHistory = array_key_exists($limit_history, $paymentHistory->data); 
            } 
            catch (Exception $e) {
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
        return view('Users.Settings', 
            compact('user', 'paymentMethods', 'paymentHistory', 'settingsAction',
            'limit_methods', 'limit_history', 'hasMorePayments',  'hasMoreHistory', 
            
    ));
    }

    public function changeSettings(UpdateSettingsRequest $request): JsonResponse
    {
        try {
            $result = $this->settingsService->changeSettings($request);
            
            return response()->json($result);
        } catch (SettingsException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

  
}
