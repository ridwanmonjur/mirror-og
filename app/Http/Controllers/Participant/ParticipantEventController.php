<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
        // Get the currently authenticated user's ID
        $userId = Auth::id();

        // Retrieve current date and time
        $currentDateTime = Carbon::now()->utc();
        $count = 3;

        $events = EventDetail::query()
            ->where('status', '<>', 'DRAFT')
            ->whereRaw('CONCAT(endDate, " ", endTime) > ?', [$currentDateTime])
            ->where('sub_action_private', '<>', 'private')
            ->where(function ($query) use ($currentDateTime) {
                $query
                    ->whereRaw('CONCAT(sub_action_public_time, " ", sub_action_public_date) < ?', [$currentDateTime])
                    ->orWhereNull('sub_action_public_time')
                    ->orWhereNull('sub_action_public_date');
            })
            ->when($request->has('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));
                if (empty($search)) {
                    return $query;
                }
                return $query->where('eventName', 'LIKE', "%{$search}%")->orWhere('eventDefinitions', 'LIKE', "%{$search}%");
            })
            ->paginate($count);

        $output = [
            'events' => $events,
            'mappingEventState' => EventDetail::mappingEventStateResolve(),
            'id' => $userId, // Pass the authenticated user's ID to the view
        ];

        if ($request->ajax()) {
            $view = view('Participant.HomeScroll', $output)->render();
            return response()->json(['html' => $view]);
        }

        return view('Participant.Home', $output);
    }

    public function teamList($user_id)
    {

        $teamList = Team::Where('user_id', $user_id)->get();
        // Check if teams exist for the user
         if ($teamList->isNotEmpty()) {
        // Process the data to count unique usernames for each team
        $usernamesCountByTeam = [];
        foreach ($teamList as $team) {
            // Retrieve JoinEvents for each team
            $joinEvents = JoinEvent::whereHas('user.teams', function ($query) use ($team) {
                $query->where('team_id', $team->id);
            })->with('user')->get();

            $usernames = $joinEvents->unique('user_id')->pluck('user')->count();

            $usernamesCountByTeam[$team->id] = $usernames;
        }

        return view('Participant.TeamList', compact('teamList', 'usernamesCountByTeam'));
         } else {
        // Handle if no teams are found for the user
        return redirect()->back()->with('error', 'No teams found for the user.');
         }
         }


    public function teamManagement($id)
    {
        // $teamManage = Team::Where('id',$id)->get();

        $teamManage = Team::where('id', $id)->get();

        // Check if the team exists
        if ($teamManage) {
            // Retrieve JoinEvents related to the team_id
            $joinEvents = JoinEvent::whereHas('user.teams', function ($query) use ($id) {
                $query->where('team_id', $id);
            })->with('eventDetails', 'user')->get();

            // Process the data to display the user's name once for each team
            $eventsByTeam = [];
            foreach ($joinEvents as $event) {
            $userId = $event->user_id;
            $teamId = $event->user->teams->first(function ($team) use ($id) {
                return $team->id == $id;
            })->id;

            if (!isset($eventsByTeam[$teamId][$userId])) {
                $eventsByTeam[$teamId][$userId]['user'] = $event->user;
                $eventsByTeam[$teamId][$userId]['events'] = [];
            }

            $eventsByTeam[$teamId][$userId]['events'][] = $event;
        }

        return view('Participant.Layout.TeamManagement', compact('teamManage', 'joinEvents', 'eventsByTeam'));

        } else {
            // Handle if the team doesn't exist
            return redirect()->back()->with('error', 'Team not found.');
        }
        // return view('Participant.Layout.TeamManagement', compact('teamManage'));
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
        $selectedTeamNames = $request->input('selectedTeamName');

        // Loop through the selected teams if it's an array
        if (is_array($selectedTeamNames)) {
            foreach ($selectedTeamNames as $teamId) {
                $member = new Member(); // Assuming you're creating a new Member instance
                $member->team_id = $teamId;
                $member->user_id  = auth()->user()->id;
                $member->save();
                return redirect()->route('participant.team.view', ['id' => auth()->user()->id]);
            }
        } else {
            // Handle the case when only one team is selected
            $member = new Member();
            $member->team_id = $selectedTeamNames;
            $member->user_id  = auth()->user()->id;
            $member->save();
            return redirect()->route('participant.team.view', ['id' => auth()->user()->id]);
        }
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
