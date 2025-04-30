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
                    'events_count' => $eventIds,
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
