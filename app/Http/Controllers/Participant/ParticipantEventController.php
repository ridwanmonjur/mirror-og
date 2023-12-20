<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Team;
use App\Models\EventDetail;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;

class ParticipantEventController extends Controller
{
    public function home(Request $request)
    {
        
        $count = 3;
        $events = EventDetail::paginate($count);
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
        $team = new Team;
        $team->teamName = $request->input('teamName');
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
        $member = new Member;
        $member->teamName = $request->input('teamName');
        $member->user_id  = auth()->user()->id;
        $member->save();
        return redirect()->back()->with('status','Team Added Successfully');
    }


    public function ConfirmUpdate(Request $request)
    {

        return view('Participant.notify');
        
    }

    public function ViewEvent(Request $request, $id)
    {
    $event = EventDetail::find($id);
    return view('Participant.ViewEvent', compact('event'));
    }

}
