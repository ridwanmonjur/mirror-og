<?php

namespace App\Http\Controllers\Open;

use App\Http\Controllers\Controller;
use App\Models\CountryRegion;
use App\Models\EventDetail;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\WithdrawalPassword;
use Carbon\Carbon;
use Database\Factories\BracketsFactory;
use Database\Factories\EventDetailFactory;
use Database\Factories\JoinEventFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use ZipArchive;

class MiscController extends Controller
{
    public function countryList()
    {
     
        $countries = CountryRegion::getAllCached();

        return response()->json(['success' => true, 'data' => $countries], 200);
    }

    public function gameList()
    {
        $games = DB::table('games')->get();

        return response()->json(['success' => true, 'data' => $games], 200);
    }

    public function deadlineTasks(Request $request, $id, $taskType): JsonResponse
    {
        Cache::flush();

        $typeMap = [
            'start' => 1,
            'end' => 2,
            'org' => 3,
        ];

        $baseUrl = $request->getSchemeAndHttpHost();
        $basePath = '/deadlineTasks' .'/' . $id ;

        $helpUrls = [
            'start' => $baseUrl . $basePath . '/start',
            'end' => $baseUrl . $basePath . '/end',
            'org' => $baseUrl . $basePath . '/reg',
        ];

        if (!array_key_exists($taskType, $typeMap)) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Invalid type. Must be start, end, or org',
                    'helpUrls' => $helpUrls
                ],
                400,
            );
        }

        $exitCode = Artisan::call('tasks:deadline', [
            'type' => $typeMap[$taskType],
            '--event_id' => (string) $id,
        ]);

        Cache::flush();

        return response()->json([
            'status' => $exitCode === 0 ? 'success' : 'failed',
            'message' => ucfirst($taskType) . ' tasks executed',
        ]);
    }

    // Updated route

    public function respondTasks(Request $request, $eventId, $taskType = null): JsonResponse
    {
        Cache::flush();

        $typeMap = [
            'start' => 1,
            'live' => 2,
            'end' => 3,
            'reg' => 4,
            'resetStart' => 5,
            'all' => 0,
        ];

        $messageMap = [
            'start' => 'Start tasks executed',
            'live' => 'Live tasks executed',
            'end' => 'End tasks executed',
            'reg' => 'Registration over tasks executed',
            'all' => 'All tasks executed',
            'resetStart' => 'Reset task executed',
        ];

        $baseUrl = $request->getSchemeAndHttpHost();
        $basePath = '/respondTasks' .'/' . $eventId ;

        $helpUrls = [
            'start' => $baseUrl . $basePath . '/start',
            'live' => $baseUrl . $basePath . '/live',
            'end' => $baseUrl . $basePath . '/end',
            'reg' => $baseUrl . $basePath . '/reg',
            'resetStart' => $baseUrl . $basePath . '/resetStart',
            'all' => $baseUrl . $basePath . '/all',
        ];

        if (!isset($typeMap[$taskType])) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Invalid event type',
                    'help' => $messageMap,
                    'helpUrls' => $helpUrls
                ],
                400,
            );
        }

        $artisanParams = ['type' => $typeMap[$taskType]];

        if (in_array($taskType, ['start', 'live', 'end', 'all', 'reg', 'resetStart'])) {
            $artisanParams['--event_id'] = (string) $eventId;
        }

        $exitCode = Artisan::call('tasks:respond', $artisanParams);
        Cache::flush();

        return response()->json([
            'status' => $exitCode === 0 ? 'success' : 'failed',
            'message' => $messageMap[$taskType],
            'helpUrls' => $helpUrls
        ]);
    }

    public function seedJoins(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'join_status' => 'required|in:pending,confirmed,canceled',
            'payment_status' => 'required|in:pending,completed,waived',
            'register_time' => 'required|in:early,normal,closed',
            'type' => 'required|in:wallet,stripe',
            ], [
                // Custom error messages for enum validations
                'join_status.required' => 'Join status must be one of: pending, confirmed, canceled.',
                'join_status.in' => 'Join status must be one of: pending, confirmed, canceled.',

                'payment_status.in' => 'Payment status must be one of: pending, completed, waived.',
                'payment_status.required' => 'Payment status must be one of: pending, completed, waived.',

                'register_time.in' => 'Register time must be one of: early, normal, closed.',
                'register_time.required' => 'Register time must be one of: early, normal, closed.',

                'type.in' => 'Type must be one of: wallet, stripe.',
                'type.required' => 'Type must be one of: wallet, stripe.',

            ]
        );


        if ($validator->fails()) {
            $baseUrl = $request->getSchemeAndHttpHost();
            $basePath = '/seed/joins';

            $exampleUrls = [
                $baseUrl . $basePath . '?join_status=pending&payment_status=pending&register_time=early&type=wallet',
                $baseUrl . $basePath . '?join_status=confirmed&payment_status=completed&register_time=normal&type=stripe',
                $baseUrl . $basePath . '?join_status=canceled&payment_status=waived&register_time=closed&type=wallet',
                $baseUrl . $basePath . '?join_status=pending&payment_status=completed&register_time=early&type=stripe',
                $baseUrl . $basePath . '?join_status=confirmed&payment_status=pending&register_time=normal&type=wallet',
                $baseUrl . $basePath . '?join_status=canceled&payment_status=completed&register_time=closed&type=stripe',
                $baseUrl . $basePath . '?join_status=pending&payment_status=waived&register_time=normal&type=wallet',
                $baseUrl . $basePath . '?join_status=confirmed&payment_status=waived&register_time=early&type=stripe',
                $baseUrl . $basePath . '?join_status=canceled&payment_status=pending&register_time=early&type=wallet',
                $baseUrl . $basePath . '?join_status=confirmed&payment_status=completed&register_time=closed&type=stripe',
            ];
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Invalid URL',
                    'exampleUrls' => $exampleUrls,
                    'errors' => $validator->errors()
                ],
                400,
            );

        }



        $validated = $validator->validated();
        $factory = new JoinEventFactory();
        $key = strToUpper($validated['register_time']);
        $register_time = config("constants.SIGNUP_STATUS.{$key}");

        // dd($register_time);

        // dd($key, $register_time);
        $options = [
            'event' => [
                'eventTier' => 'Starfish',
                'eventName' => $this->generateEventName($validated),
            ],
            'joinEvent' => [
                'join_status' => $validated['join_status'],
                'payment_status' => $validated['payment_status'],
                'participantPayment' => [
                    'register_time' => $register_time,
                    'type' => $validated['type'],
                ]
            ]
        ];

        $seed = $factory->seed($options);

        [
            'events' => $events,
            'participants' => $participants,
            'organizer' => $organizer
        ] = $seed;

        $eventId = null;

        if (isset($events) && isset($events[0]->eventName)) {
            $eventId = $events[0]->id;

            $events = $events[0]->eventName;
        }

        if (isset($participants) && isset($participants[0])) {
            $participants = collect($participants)->pluck('email')->toArray();
        }

        if (isset($organizer) && isset($organizer[0]->email)) {
            $organizer = $organizer[0]->email;
        }

        return response()->json(
            [
                'success' => true,
                'message' => 'Seeding completed successfully',
                'data' => [
                    'events' => $events,
                    'participants' => $participants,
                    'organizer' => $organizer,
                    'eventId' => $eventId
                ],
            ],
            200,
        );


    }

    /**
     * Generate event name based on request parameters
     */
    private function generateEventName(array $validated): string
    {
        $registerTime = ucfirst(strtolower($validated['register_time']));
        $paymentType = ucfirst($validated['type']);
        $joinStatus = ucfirst($validated['join_status']);

        $eventName = " Event {$registerTime} Registration";
        $eventName .= " - (Payment {$paymentType})";
        $eventName .= " [Join {$joinStatus}]";

        return $eventName;
    }

    public function seedBrackets(Request $request, $tier): JsonResponse
    {
        try {
            $factory = new BracketsFactory();
            $seed = $factory->seed([
                'event' => [
                    'eventTier' => $tier,
                    'eventName' => 'Test Brackets',

                ],
                'joinEvent' => [
                    'join_status' => 'confirmed',
                    'payment_status' => 'confirmed',
                    'participantPayment' => [
                        'register_time' => config('constants.SIGNUP_STATUS.EARLY'),
                        'type' => 'wallet',
                    ]
                ]
            ]);

            [
                'eventIds' => $eventIds,
                'participants' => $participants,
                'organizers' => $organizers,
            ] = $seed;
            return response()->json(
                [
                    'success' => true,
                    'message' => 'Seeding completed successfully',
                    'data' => [
                        'events' => $eventIds,
                        'participants' => $participants,
                        'organizers' => $organizers,
                    ],
                ],
                200,
            );
        } catch (\Exception $e) {
            // Return error response if something goes wrong
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Seeding failed',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ],
                500,
            );
        }
    }

    public function showLandingPage(Request $request)
    {
        $currentDateTime = Carbon::now()->utc();

        $events = EventDetail::landingPageQuery($request, $currentDateTime)->simplePaginate();

        $output = compact('events');
        if ($request->ajax()) {
            $view = view('includes.Landing', $output)->render();

            return response()->json(['html' => $view]);
        }

        return view('Landing', $output);
    }

    public function downloadWithdrawalCsv($token)
    {
        // Verify token exists and is not expired
        $tokenData = DB::table('2fa_links')
            ->where('withdrawal_history_token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$tokenData) {
            abort(404, 'Invalid or expired download link');
        }

        $withdrawals = Withdrawal::with('user')->get();

        $csvContent = $this->generateCsvContent($withdrawals, true) ;
        
        $password = WithdrawalPassword::first();
        
        $tempDir = storage_path('app/temp/withdrawals');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $csvFileName = 'withdrawals_' . date('Y-m-d_H-i-s') . '.csv';
        $csvPath = $tempDir . '/' . $csvFileName;
        $zipPath = $tempDir . '/withdrawals_export_' . date('Y-m-d_H-i-s') . '.zip';

        file_put_contents($csvPath, $csvContent);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($csvPath, $csvFileName);
            if ($password) {
                $zip->setPassword($password->password);
            }

            $zip->setEncryptionName($csvFileName, ZipArchive::EM_AES_256);
            $zip->close();

            unlink($csvPath);

            DB::table('2fa_links')->where('withdrawal_history_token', $token)->delete();

            return Response::download($zipPath, basename($zipPath))->deleteFileAfterSend(true);
        } else {
            abort(500, 'Unable to create password-protected ZIP file');
        }
    }

    private function generateCsvContent($withdrawals, bool $includeBankDetails): string
    {
        $headers = [
            'ID',
            'User ID',
            'User Name',
            'User Email',
            'Amount (RM)',
            'Status',
            'Requested At',
        ];

        if ($includeBankDetails) {
            $headers = array_merge($headers, [
                'Bank Name',
                'Account Number',
                'Account Holder Name',
            ]);
        }

        $csvData = [];
        $csvData[] = $headers;

        $wallet = null;
        if (isset($withdrawals[0])) {
            $wallet = Wallet::retrieveOrCreateCache($withdrawals[0]->user_id);
        }

        foreach ($withdrawals as $withdrawal) {
            $row = [
                $withdrawal->id,
                $withdrawal->user_id,
                $withdrawal->user->name ?? 'N/A',
                $withdrawal->user->email ?? 'N/A',
                number_format($withdrawal->withdrawal, 2),
                ucfirst($withdrawal->status),
                $withdrawal->requested_at ? $withdrawal->requested_at->setTimezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i:s') : 'N/A',
            ];

            if ($includeBankDetails) {
                $row = array_merge($row, [
                    $wallet?->bank_name ?? 'N/A',
                    $wallet?->account_number ?? 'N/A',
                    $wallet?->account_holder_name ?? 'N/A',
                ]);
            }

            $csvData[] = $row;
        }

        // Convert to CSV format
        $output = fopen('php://temp', 'w');
        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }
}
