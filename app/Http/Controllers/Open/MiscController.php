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

    public function seedStart(Request $request, $id): JsonResponse {
        Cache::flush();
    
        $exitCode = Artisan::call('tasks:deadline', [
            'type' => 1,
            '--event_id' => (string) $id
        ]);
        
        return response()->json([
            'status' => $exitCode === 0 ? 'success': 'failed',
            'message' => 'Start tasks executed',
        ]);
    }
    
    public function seedEnd(Request $request, $id): JsonResponse {
        Cache::flush();
    
        $exitCode = Artisan::call('tasks:deadline', [
            'type' => 2,
            '--event_id' => (string) $id
        ]);
        
        return response()->json([
            'status' => $exitCode === 0 ? 'success': 'failed',
            'message' => 'End tasks executed',
        ]);
    }
    
    public function seedOrg(Request $request, $id): JsonResponse {
        Cache::flush();
    
        $exitCode = Artisan::call('tasks:deadline', [
            'type' => 3,
            '--event_id' => (string) $id
        ]);
        
        return response()->json([
            'status' => $exitCode === 0 ? 'success': 'failed',
            'message' => 'Org tasks executed',
        ]);
    }

    public function seedEvent(Request $request, $id, $type = null): JsonResponse 
    {
        Cache::flush();

        $typeMap = [
            'start' => 1,
            'live' => 2,
            'end' => 3,
            'reg' => 4,
            'all' => 0,
        ];

        $messageMap = [
            'start' => 'Start tasks executed',
            'live' => 'Live tasks executed',
            'end' => 'End tasks executed',
            'reg' => 'Registration over tasks executed',
            'all' => 'All tasks executed',
        ];

        if (!isset($typeMap[$type])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid event type',
                'help' => $messageMap
            ], 400);
        }

        $artisanParams = ['type' => $typeMap[$type]];
        
        if (in_array($type, ['start', 'live', 'end', 'all', 'reg'])) {
            $artisanParams['--event_id'] = (string) $id;
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
                    'eventTier' => $type
                ]
            ]);
            [
                'eventIds' => $eventIds,
                'participants' => $participants,
                'organizers' => $organizers
            ] = $seed;
            return response()->json([
                'success' => true,
                'message' => 'Seeding completed successfully',
                'data' => [
                    'events' => $eventIds,
                    'participants' => $participants,
                    'organizers' => $organizers
                ]
            ], 200);
            
        } catch (\Exception $e) {
            // Return error response if something goes wrong
            return response()->json([
                'success' => false,
                'message' => 'Seeding failed',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function showLandingPage(Request $request)
    {
        $count = 6;
        $currentDateTime = Carbon::now()->utc();

        $events = EventDetail::landingPageQuery($request, $currentDateTime)
            ->paginate($count);


        $output = compact('events');

        if ($request->ajax()) {
            $view = view('includes.Landing', $output)->render();

            return response()->json(['html' => $view]);
        }

        return view('Landing', $output);
    }
}
