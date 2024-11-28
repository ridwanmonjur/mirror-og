<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\ApproveMemberRequest;
use App\Http\Requests\Team\DisapproveMemberRequest;
use App\Http\Requests\Team\VoteToStayRequest;
use App\Models\JoinEvent;
use App\Models\RosterCaptain;
use App\Models\RosterMember;
use App\Models\Team;
use App\Models\TeamMember;
use App\Services\PaymentService;
use Exception;
use Illuminate\Database\QueryException as DatabaseQueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParticipantRosterController extends Controller
{
    private $paymentService;

    public function __construct(
        PaymentService $paymentService,
    )
    {
        $this->paymentService = $paymentService;
    }

    public function rosterMemberManagement(Request $request, $id, $teamId)
    {
        $user_id = $request->attributes->get('user')->id;
        $selectTeam = Team::where('id', $teamId)
            ->whereHas('members', function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('status', 'accepted');
            })
            ->first();

        $joinEvent = JoinEvent::where('team_id', intval($teamId))->where('event_details_id', intval($id))->first();

        if ($selectTeam && $joinEvent) {
            $captain = RosterCaptain::where('join_events_id', $joinEvent->id)->first();
            $creator_id = $selectTeam->creator_id;
            $teamMembers = $selectTeam->members->where('status', 'accepted');
            $memberIds = $teamMembers->pluck('id')->toArray();
            $rosterMembers = RosterMember::whereIn('team_member_id', $memberIds)
                ->where('join_events_id', $joinEvent->id)->get();

            $rosterMembersKeyedByMemberId = RosterMember::keyByMemberId($rosterMembers);
            $isRedirect = $request->redirect === 'true';

            return view(
                'Participant.RosterManagement',
                compact(
                    'selectTeam',
                    'joinEvent',
                    'teamMembers',
                    'creator_id',
                    'isRedirect',
                    'rosterMembersKeyedByMemberId',
                    'rosterMembers',
                    'id',
                    'captain'
                )
            );
        }
        return $this->showErrorParticipant('This event is missing or you need to be a member to view events!');
    }

    public function approveRosterMember(ApproveMemberRequest $request)
    {

        try{
            RosterMember::create([
                'user_id' => $request->user_id,
                'join_events_id' => $request->join_events_id,
                'team_member_id' => $request->member_id,
                'team_id' => $request->team_id,
            ]);


            return response()->json([
                'success' => true, 
                'message' => 'Roster member approved successfully'
            ]);

        } catch (DatabaseQueryException $e) {
            $errorMessage = $e->getCode() === '23000' || $e->getCode() === 1062 
                ? 'Duplicate roster member entry'
                : 'Failed to approve roster member';

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function voteForEvent(VoteToStayRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $request->attributes->get('user');
            $rosterMember = $request->rosterMember;
            $rosterMember->vote_to_quit = $request->vote_to_quit;
            $rosterMember->save();

            $joinEvent = JoinEvent::where('id', $rosterMember->join_events_id)
                ->with(['roster', 'eventDetails', 'eventDetails.user'])
                ->first();

                $team = Team::where('id', $joinEvent->team_id)
                ->with(['members' => function ($query) {
                    $query->where('status', 'accepted')->with('user');
                }])
                ->first();
            $joinEvent->vote_starter_id = $user->id;
            [$leaveRatio, ] = $joinEvent->decideRosterLeaveVote();
            $joinEvent->save();
            if ($leaveRatio > 0.5) {
                $discountsByUserAndType = $this->paymentService->refundPaymentsForEvents([$joinEvent->eventDetails->id], 0.5);
                $team->cancelTeamRegistration($joinEvent->eventDetails, $discountsByUserAndType );
            }
    
            $message = !$request->vote_to_quit ? 'Voted to stay in the event' : 'Voted to leave the event';
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $rosterMember
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function disapproveRosterMember(DisapproveMemberRequest $request)
    {
        try {
            $joinEvent = JoinEvent::findOrFail( $request->join_events_id)->first();

            $member = RosterMember::where([
                'user_id' => $request->user_id,
                'join_events_id' => $request->join_events_id,
                'team_id' => $request->team_id
            ])->first();

            $joinEvent->roster_captain_id == $member->id ? null : $joinEvent->roster_captain_id;
            $joinEvent->vote_starter_id == $request->user_id ? null : $joinEvent->vote_starter_id;
            
            if ($member) {
                $member->delete();
            }

            $joinEvent->save();
    
            return response()->json([
                'success' => true, 
                'message' => 'Roster status deleted'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function captainRosterMember(Request $request)
    {
        try {
            $joinEvent = JoinEvent::findOrFail($request->join_events_id);
            $joinEvent->roster_captain_id = $request->roster_captain_id;
            $joinEvent->save();
            return response()->json(['success' => true, 'message' => 'Roster captain created']);
        }   catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function deleteCaptainRosterMember(Request $request)
    {
        try {
            $request->validate([
                'join_events_id' => 'required',
                'team_member_id' => 'required',
                'teams_id' => 'required',
            ]);

            $captain = RosterCaptain::where([
                'join_events_id' => $request->join_events_id,
                'team_member_id' => $request->team_member_id,
                'teams_id' => $request->teams_id,
            ])->first();
            if ($captain) {
                $captain->delete();
            }

            return response()->json(['success' => true, 'message' => 'Roster captain deleted']);
        } catch (DatabaseQueryException $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update data', 'error' => $e->getMessage()]);
        }
    }
}
