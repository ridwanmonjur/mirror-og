<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Team;
use App\Models\EventDetail;
use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ParticipantEventController extends Controller
{
    public function home(Request $request)
    {
        $currentDateTime = Carbon::now()->utc();
        $count = 3;
        $events = EventDetail::query()
            ->where(function ($query) use ($currentDateTime) {
                return $query
                    ->whereNull('sub_action_public_date')
                    ->orWhereNull('sub_action_public_time')
                    ->orWhereRaw('CONCAT(sub_action_public_date, " ", sub_action_public_time) > ?', [$currentDateTime]);
            })
            ->where('status', '<>', 'DRAFT')
            ->where('status', '<>', 'PREVIEW')
            ->whereRaw('CONCAT(endDate, " ", endTime) > ?', [$currentDateTime])
            ->paginate($count);
        $output = ['events' => $events, 'mappingEventState' => EventDetail::mappingEventStateResolve()];
        if ($request->ajax()) {
            $view = view(
                'Participant.HomeScroll',
                $output
            )->render();

            return response()->json(['html' => $view]);
        }
        return view(
            'Participant.Home',
            $output
        );
    }


    public function teamDetails($user_id)
    {
        $teamDetail = Team::Where('user_id', $user_id)->get();
        return view('Participant.Layout.HeadTag', compact('teamDetail'));
    }


    public function createTeamView(Request $request, $user_id)
    {
        $teamm = Team::find($user_id);
        return view('Participant.CreateTeam', compact('teamm'));
    }


    public function TeamStore(Request $request)
    {
        $team = new Team;
        $team->teamName = $request->input('teamName');
        $team->user_id  = auth()->user()->id;
        $team->save();
        return redirect()->back()->with('status', 'Team Added Successfully');
    }

    /* Select Team to Register */

    public function SelectTeamtoRegister(Request $request)
    {
        $selectTeam = Team::all();
        return view('Participant.SelectTeamtoRegister', compact('selectTeam'));
    }


    public function TeamtoRegister(Request $request)
    {
        $member = new Member;
        $member->teamName = $request->input('teamName');
        $member->user_id  = auth()->user()->id;
        $member->save();
        return redirect()->back()->with('status', 'Team Added Successfully');
    }


    public function ConfirmUpdate(Request $request)
    {

        return view('Participant.notify');
    }
    public function ViewSearchEvents(Request $request)
    {
        $currentDateTime = Carbon::now()->utc();
        $eventListQuery =  EventDetail::query()
            ->where(function ($query) use ($currentDateTime) {
                return $query
                    ->whereNull('sub_action_public_date')
                    ->orWhereNull('sub_action_public_time')
                    ->orWhereRaw('CONCAT(sub_action_public_date, " ", sub_action_public_time) > ?', [$currentDateTime]);
            })
            ->where('status', '<>', 'DRAFT')
            ->where('status', '<>', 'PREVIEW')
            ->whereRaw('CONCAT(endDate, " ", endTime) > ?', [$currentDateTime]);
        $eventListQuery->when($request->has('search'), function ($query) use ($request) {
            $search = trim($request->input('search'));
            if (empty($search)) {
                return $query;
            }
            return $query->where('gameTitle', 'LIKE', "%{$search}% COLLATE utf8mb4_general_ci")
                ->orWhere('eventDescription', 'LIKE', "%{$search}% COLLATE utf8mb4_general_ci")
                ->orWhere('eventDefinitions', 'LIKE', "%{$search}% COLLATE utf8mb4_general_ci");
        });
        $count = 4;
        $eventList = $eventListQuery->paginate($count);
        $mappingEventState = EventDetail::mappingEventStateResolve();

        $outputArray = compact('eventList', 'count', 'user', 'organizer', 'mappingEventState');
        return response()->json($outputArray);
    }
}
