<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\EventDetail;
use App\Models\EventInvitation;
use App\Models\Team;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizerInvitationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $id)
    {
        $authUser = $request->attributes->get('user');
        $user_id = $authUser->id;
        $teamList = Team::all();
        $tier = $type = $game = null;

        $event = EventDetail::with(['invitationList', 'invitationList.team'])
            ->where('user_id', $user_id)
            ->find($id);

        $isUserSameAsAuth = true;

        if (! $event) {
            throw new ModelNotFoundException("Event not found with id: {$id}");
        }

        return view('Organizer.Invitation', compact('event', 'isUserSameAsAuth', 'teamList', 'user_id', 'game', 'tier', 'type'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $team = Team::where('id', $request->team_id)->first();
        $isExistsBefore = EventInvitation::where('team_id', $request->team_id)
            ->where('event_id', $request->event_id)
            ->exists();
        if ($isExistsBefore) {
            return response()->json([
                'success' => false,
                'message' => 'You have already sent an invitation to this team!',
            ]);
        }

        $invitation = new EventInvitation();
        $invitation->organizer_user_id = $request->organizer_id;
        $invitation->event_id = $request->event_id;
        $invitation->team_id = $request->team_id;
        $invitation->save();

        return response()->json([
            'success' => 'true',
            'message' => 'Invitation has been sent successfully.',
            'data' => [
                'invitation' => $invitation,
                'team' => $team,
            ],
        ]);
    }

    public function destroy(Request $request)
    {
        $invitation = EventInvitation::find($request->inviteId);
        $invitation?->delete();

        return response()->json(['success' => true, 'message' => 'Invitation deleted successfully.']);
    }
}
