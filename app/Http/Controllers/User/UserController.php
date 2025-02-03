<?php

namespace App\Http\Controllers\User;

use App\Exceptions\SettingsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\BannerUpdateRequest;
use App\Http\Requests\User\UpdateSettingsRequest;
use App\Models\StripePayment;
use App\Models\TeamProfile;
use App\Models\User;
use App\Models\UserProfile;
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
        $limit_history = 10; // 100
        $page_history = intval($request->input('history_page', 1)); // 100
        $page_next = $request->input('page_next'); // 100

        $count_history = $limit_history * $page_history;

        $paramsMethods = [
            'customer' => $user->stripe_customer_id,
            'limit' => $limit_methods + 1,
            'type' => 'card',
        ];

        $paramsHisotry = [
            'query' => "customer:'{$user->stripe_customer_id}' AND status:'succeeded'",
            'limit' => $limit_history + 1,
        ];

        if ($page_next != null) {
            $paramsHisotry['page'] = $page_next;
        }

        if ($user->stripe_customer_id) {
            $paymentMethods = $this->stripeClient->retrieveAllStripePaymentsByCustomer($paramsMethods);
            $paymentHistory = $this->stripeClient->searchStripePaymenst($paramsHisotry);
            $hasMorePayments = array_key_exists($limit_methods, $paymentMethods->data);
            $hasMoreHistory = array_key_exists($limit_history, $paymentHistory->data); 
        } else {
            $paymentMethods = new Collection();
            $paymentHistory = new Collection();
            $hasMorePayments = $hasMoreHistory = false;
        }
    
        $settingsAction = config('constants.SETTINGS_ROUTE_ACTION');
        return view('Shared.Settings', 
            compact('user', 'paymentMethods', 'paymentHistory', 'limit_methods', 
            'limit_history', 'count_history', 'page_history', 'hasMorePayments', 'settingsAction', 'hasMoreHistory',
            'page_next'
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
