<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\EventDetail;
use App\Models\JoinEvent;
use App\Models\Member;
use Illuminate\Support\Facades\Validator;
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


    public function teamList($user_id)
    {

        $teamList = Team::Where('user_id',$user_id)->get();
        return view('Participant.TeamList', compact('teamList'));
    }


    public function teamManagement($id)
    {
        $teamManage = Team::Where('id',$id)->get();
        return view('Participant.Layout.TeamManagement', compact('teamManage'));
    }


    public function createTeamView(Request $request, $user_id)
    {
        $teamm = Team::find($user_id);
        return view('Participant.CreateTeam', compact('teamm'));
    }


    public function TeamStore(Request $request)
    {

        $validatedData = $request->validate([
            'teamName' => 'required|string|max:25', // Validation rule for teamName field
            // You can add more validation rules if needed for other fields
        ]);

        $team = new Team;
        $team->teamName = $request->input('teamName');
        $existingTeam = Team::where('teamName', $team->teamName)->first();

        if ($existingTeam) {
            return redirect()->back()->with('error', 'Team name already exists. Please choose a different name.');
            // Redirect back to the form with an error message
        }
        $team->user_id  = auth()->user()->id;
        $team->save();
        return redirect()->route('participant.team.view', ['id' => auth()->user()->id]);

    }

    /* Select Team to Register */

    public function SelectTeamtoRegister(Request $request)
    {
        $selectTeam = Team::all();
        return view('Participant.SelectTeamtoRegister', compact('selectTeam'));
    }


    public function TeamtoRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'selectedTeamName' => 'required', // Validation rule for 'selectedTeamName'
            // Add other validation rules for additional fields if needed
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $member = new Member;
        $member->teamName = $request->input('selectedTeamName');
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
    }

    public function ViewEvent(Request $request, $id)
    {
    $event = EventDetail::find($id);
    return view('Participant.ViewEvent', compact('event'));
    }

    public function JoinEvent(Request $request, $id)
    {
        $joint = new JoinEvent();
        $joint->user_id  = auth()->user()->id;
        // $join->event_details_id = $request->input('event_details_id');
        $joint->event_details_id = $id;
        $joint->save();

        return redirect('/participant/selectTeam');
    }
}
