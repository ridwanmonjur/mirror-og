<?php

namespace App\Http\Controllers\Open;

use App\Http\Controllers\Controller;
use App\Models\CountryRegion;
use App\Models\EventDetail;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\WithdrawalPassword;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use Database\Factories\BracketsFactory;
use Database\Factories\JoinEventFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

  

    

    public function allTasks(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'taskType' => 'required|in:event_started,event_live,event_ended,event_reg_over,event_resetStart,report_start,report_end,report_org,tasks_all,weekly_tasks',
            'eventId' => 'integer',
        ], [
            'taskType.required' => 'Task type is required.',
            'taskType.in' => 'Task type must be one of: event_started, event_live, event_ended, event_reg_over, event_resetStart, report_start, report_end, report_org, tasks_all, weekly_tasks.',
            'eventId.integer' => 'Event ID must be an integer.',
        ]);

        if ($validator->fails()) {
            $baseUrl = $request->getSchemeAndHttpHost();
            $basePath = '/seed/tasks';

            $exampleUrls = [
                $baseUrl.$basePath.'?taskType=event_started&eventId=123',
                $baseUrl.$basePath.'?taskType=event_live&eventId=456',
                $baseUrl.$basePath.'?taskType=event_ended&eventId=789',
                $baseUrl.$basePath.'?taskType=event_reg_over&eventId=101',
                $baseUrl.$basePath.'?taskType=event_resetStart&eventId=202',
                $baseUrl.$basePath.'?taskType=report_start&eventId=303',
                $baseUrl.$basePath.'?taskType=report_end&eventId=404',
                $baseUrl.$basePath.'?taskType=report_org&eventId=505',
                $baseUrl.$basePath.'?taskType=tasks_all',
                $baseUrl.$basePath.'?taskType=weekly_tasks',
                $baseUrl.$basePath.'?taskType=event_started',
            ];

            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Invalid URL',
                    'exampleUrls' => $exampleUrls,
                    'errors' => $validator->errors(),
                ],
                400,
            );
        }

        Cache::flush();

        $validated = $validator->validated();
        $taskType = $validated['taskType'];
        $eventId = $validated['eventId'] ?? null;

        $respondTypeMap = [
            'event_started' => 1,
            'event_live' => 2,
            'event_ended' => 3,
            'event_reg_over' => 4,
            'event_resetStart' => 5,
            'report_start' => 6,
            'report_end' => 7,
            'report_org' => 8,
            'tasks_all' => 0,
            'weekly_tasks' => 9,
        ];

        $messageMap = [
            'event_started' => 'Event started tasks executed',
            'event_live' => 'Event live tasks executed', 
            'event_ended' => 'Event ended tasks executed',
            'event_reg_over' => 'Event registration over tasks executed',
            'event_resetStart' => 'Event reset task executed',
            'report_start' => 'Report start tasks executed',
            'report_end' => 'Report end tasks executed',
            'report_org' => 'Report org tasks executed',
            'tasks_all' => 'All tasks executed',
            'weekly_tasks' => 'Weekly cleanup tasks executed',
        ];

        try {
            $taskTypeId = $respondTypeMap[$taskType];
            
            if ($eventId) {
                Artisan::call('tasks:run-all', [
                    'task_type' => $taskTypeId,
                    '--event_id' => $eventId
                ]);
            } else {
                Artisan::call('tasks:run-all', [
                    'task_type' => $taskTypeId
                ]);
            }
            
            $status = 'success';
        } catch (\Exception $e) {
            $status = 'failed';
        }

        Cache::flush();

        return response()->json([
            'status' => $status,
            'message' => $messageMap[$taskType],
        ]);
    }

    public function seedJoins(Request $request): JsonResponse
    {
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
                $baseUrl.$basePath.'?join_status=pending&payment_status=pending&register_time=early&type=wallet',
                $baseUrl.$basePath.'?join_status=confirmed&payment_status=completed&register_time=normal&type=stripe',
                $baseUrl.$basePath.'?join_status=canceled&payment_status=waived&register_time=closed&type=wallet',
                $baseUrl.$basePath.'?join_status=pending&payment_status=completed&register_time=early&type=stripe',
                $baseUrl.$basePath.'?join_status=confirmed&payment_status=pending&register_time=normal&type=wallet',
                $baseUrl.$basePath.'?join_status=canceled&payment_status=completed&register_time=closed&type=stripe',
                $baseUrl.$basePath.'?join_status=pending&payment_status=waived&register_time=normal&type=wallet',
                $baseUrl.$basePath.'?join_status=confirmed&payment_status=waived&register_time=early&type=stripe',
                $baseUrl.$basePath.'?join_status=canceled&payment_status=pending&register_time=early&type=wallet',
                $baseUrl.$basePath.'?join_status=confirmed&payment_status=completed&register_time=closed&type=stripe',
            ];

            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Invalid URL',
                    'exampleUrls' => $exampleUrls,
                    'errors' => $validator->errors(),
                ],
                400,
            );

        }

        $validated = $validator->validated();
        $factory = new JoinEventFactory;
        $key = strtoupper($validated['register_time']);
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
                ],
            ],
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
                    'eventId' => $eventId,
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

    public function seedBrackets(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tier' => 'required|string',
            'type' => 'string',
            'game' => 'string',
            'numberOfTeams' => 'integer|min:2|max:16',
        ], [
            'tier.required' => 'Tier parameter is required.',
            'numberOfTeams.integer' => 'Number of teams must be an integer.',
            'numberOfTeams.min' => 'Number of teams must be at least 2.',
            'numberOfTeams.max' => 'Number of teams cannot exceed 16.',
        ]);

        if ($validator->fails()) {
            $baseUrl = $request->getSchemeAndHttpHost();
            $basePath = '/seed/event';

            $exampleUrls = [
                $baseUrl.$basePath.'?tier=Dolphin&type=Tournament&game='.urlencode('Dota 2').'&numberOfTeams=8',
                $baseUrl.$basePath.'?tier=Starfish&type=League&game=Chess&numberOfTeams=16',
                $baseUrl.$basePath.'?tier=Turtle&type=Tournament&game=Fifa&numberOfTeams=8',
                $baseUrl.$basePath.'?tier=Dolphin&type=League&game='.urlencode('Dota 2').'&numberOfTeams=16',
                $baseUrl.$basePath.'?tier=Starfish&type=Tournament&game=Chess&numberOfTeams=16',
                $baseUrl.$basePath.'?tier=Turtle&type=League&game=Fifa&numberOfTeams=16',
                $baseUrl.$basePath.'?tier=Dolphin&type=Tournament&game=Chess&numberOfTeams=8',
                $baseUrl.$basePath.'?tier=Starfish&type=League&game='.urlencode('Dota 2').'&numberOfTeams=16',
                $baseUrl.$basePath.'?tier=Turtle&type=Tournament&game='.urlencode('Dota 2').'&numberOfTeams=16',
                $baseUrl.$basePath.'?tier=Dolphin&type=League&game=Fifa&numberOfTeams=16',
            ];

            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Invalid URL',
                    'exampleUrls' => $exampleUrls,
                    'errors' => $validator->errors(),
                ],
                400,
            );
        }

        try {
            $validated = $validator->validated();
            $tier = $validated['tier'];
            $type = $validated['type'] ?? 'Tournament';
            $game = $validated['game'] ?? 'Dota 2';
            $numberOfTeams = $validated['numberOfTeams'] ?? 2;

            $factory = new BracketsFactory;
            $seed = $factory->seed([
                'event' => [
                    'eventTier' => $tier,
                    'eventName' => "Test {$type} {$game}",
                    'eventType' => $type,
                    'eventGame' => $game
                ],
                'joinEvent' => [
                    'join_status' => 'confirmed',
                    'payment_status' => 'confirmed',
                    'participantPayment' => [
                        'register_time' => config('constants.SIGNUP_STATUS.EARLY'),
                        'type' => 'wallet',
                    ],
                ],
                'numberOfTeams' => $numberOfTeams,
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

        $events = EventDetail::landingPageQuery($request, $currentDateTime)
            ->orderBy('startDate', 'asc')
            ->orderBy('startTime', 'asc') 
            ->orderBy('id', 'asc')
            ->simplePaginate();
        $output = compact('events');
        if ($request->ajax()) {
            $view = view('includes.Landing', $output)->render();

            return response()->json(['html' => $view]);
        }
        // dd($output);

        return view('Landing', $output);
    }

    public function downloadWithdrawalCsv($token)
    {
        // Verify token exists and is not expired
        $tokenData = DB::table('2fa_links')
            ->where('withdrawal_history_token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (! $tokenData) {
            abort(404, 'Invalid or expired download link');
        }

        $withdrawals = Withdrawal::with('user')->get();

        $csvContent = $this->generateCsvContent($withdrawals, true);

        $password = WithdrawalPassword::first();

        $tempDir = storage_path('app/temp/withdrawals');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $csvFileName = 'withdrawals_'.date('Y-m-d_H-i-s').'.csv';
        $csvPath = $tempDir.'/'.$csvFileName;
        $zipPath = $tempDir.'/withdrawals_export_'.date('Y-m-d_H-i-s').'.zip';

        file_put_contents($csvPath, $csvContent);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
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
