<?php

namespace App\Http\Controllers\Open;

use App\Http\Controllers\Controller;
use App\Models\EventDetail;
use Carbon\Carbon;
use Database\Factories\BracketsFactory;
use Database\Factories\EventDetailFactory;
use Database\Factories\JoinEventFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Io238\ISOCountries\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MiscController extends Controller
{
    public function countryList()
    {
        $countries = Country::all(['name', 'emoji_flag', 'id']);

        return response()->json(['success' => true, 'data' => $countries], 200);
    }

    public function gameList()
    {
        $games = DB::table('games')->get();

        return response()->json(['success' => true, 'data' => $games], 200);
    }

    public function deadlineTaks(Request $request, $id, $type): JsonResponse
    {
        Cache::flush();

        $typeMap = [
            'start' => 1,
            'end' => 2,
            'org' => 3,
        ];

        if (!array_key_exists($type, $typeMap)) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Invalid type. Must be start, end, or org',
                ],
                400,
            );
        }

        $exitCode = Artisan::call('tasks:deadline', [
            'type' => $typeMap[$type],
            '--event_id' => (string) $id,
        ]);

        Cache::flush();

        return response()->json([
            'status' => $exitCode === 0 ? 'success' : 'failed',
            'message' => ucfirst($type) . ' tasks executed',
        ]);
    }

    // Updated route

    public function respondTasks(Request $request, $eventId, $type = null): JsonResponse
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

        if (!isset($typeMap[$type])) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Invalid event type',
                    'help' => $messageMap,
                ],
                400,
            );
        }

        $artisanParams = ['type' => $typeMap[$type]];

        if (in_array($type, ['start', 'live', 'end', 'all', 'reg', 'resetStart'])) {
            $artisanParams['--event_id'] = (string) $eventId;
        }

        $exitCode = Artisan::call('tasks:respond', $artisanParams);
        Cache::flush();

        return response()->json([
            'status' => $exitCode === 0 ? 'success' : 'failed',
            'message' => $messageMap[$type],
        ]);
    }

    public function seedBrackets(Request $request, $type): JsonResponse
    {
        try {
            $factory = new BracketsFactory();
            $seed = $factory->seed([
                'event' => [
                    'eventTier' => $type,
                ],
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
        $count = 6;
        $currentDateTime = Carbon::now()->utc();

        $events = EventDetail::landingPageQuery($request, $currentDateTime)->paginate($count);

        $output = compact('events');

        if ($request->ajax()) {
            $view = view('includes.Landing', $output)->render();

            return response()->json(['html' => $view]);
        }

        return view('Landing', $output);
    }
}
