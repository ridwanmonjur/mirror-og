<?php

namespace App\Http\Controllers\Open;

use App\Http\Controllers\Controller;
use App\Models\EventDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Io238\ISOCountries\Models\Country;
use Illuminate\Http\Request;


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

    public function showLandingPage(Request $request)
    {
        $count = 6;
        $currentDateTime = Carbon::now()->utc();

        $events = EventDetail::landingPageQuery($request, $currentDateTime)
            ->paginate($count);


        $output = compact('events');

        if ($request->ajax()) {
            $view = view('__CommonPartials.Landing', $output)->render();

            return response()->json(['html' => $view]);
        }

        return view('Landing', $output);
    }
}
