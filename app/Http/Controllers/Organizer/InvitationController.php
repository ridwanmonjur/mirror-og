<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\EventDetail;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $authUser = Auth::user();
        $user_id = $authUser->id;
        $participationList = User::where('role', 'PARTICIPANT')->get();
        $tier = $type = $game = null;
        $event = EventDetail::with('invitationList')
            ->where('user_id', $user_id)
            ->find($id);
        $isUserSameAsAuth = true;
        if (!$event) {
            throw new ModelNotFoundException("Event not found with id: $id");
        }
        return view('Organizer.Invitation', compact('event', 'isUserSameAsAuth', 'participationList', 'user_id', 'game', 'tier', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $invitation = new Invitation();
        $invitation->organizer_id = $request->organizer_id;
        $invitation->event_id = $request->event_id;
        $invitation->participant_id = $request->participant_id;
        $invitation->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Payment successful',
            'data' => [
                'invitation' => $invitation,
            ],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invitation = Invitation::find($id);
        $invitation->delete();
        return response()->json(['success' => 'Invitation deleted successfully.']);
    }
}
