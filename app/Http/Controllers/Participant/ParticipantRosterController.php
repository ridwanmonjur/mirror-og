<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\ApproveMemberRequest;
use App\Http\Requests\Team\DisapproveMemberRequest;
use App\Http\Requests\Team\VoteToStayRequest;
use App\Jobs\HandleEventJoinConfirm;
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
                ->with(['roster', 'roster.user',
                    'eventDetails.user:id,name,userBanner',
                    'eventDetails.tier:id,eventTier'
                ])
                ->firstOrFail();
            
            if ($joinEvent && $joinEvent->join_status != 'confirmed') {
                return response()->json([
                    'success' => true, 
                    'message' => 'The vote cannot start now.'
                ]);
            }
           
            $team = Team::where('id', $joinEvent->team_id)->first();
            
            $joinEvent->vote_starter_id = $user->id;
            [$leaveRatio, $stayRatio] = $joinEvent->decideRosterLeaveVote();
            if ($leaveRatio > 0.5) {
                $discountsByUserAndType = null;
                if ($joinEvent->status == "pending") {
                    $discountsByUserAndType = $this->paymentService->refundPaymentsForEvents($joinEvent->id, 0);
                }
                
                dispatch(new HandleEventJoinConfirm('VoteEnd', [
                    'selectTeam' => $team,
                    'user' => $user,
                    'event' => $joinEvent->eventDetails,
                    'discount' => $discountsByUserAndType,
                    'willQuit' => true,
                    'join_id' => $joinEvent->id,
                    'joinEvent' => $joinEvent
                ]));
            } 
            
            if ($stayRatio >= 0.5) {
                dispatch(new HandleEventJoinConfirm('VoteEnd', [
                    'selectTeam' => $team,
                    'user' => $user,
                    'event' => $joinEvent->eventDetails,
                    'discount' => null,
                    'willQuit' => false,
                    'join_id' => $joinEvent->id,
                    'joinEvent' => $joinEvent
                ]));
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
            $joinEvent = JoinEvent::findOrFail( $request->join_events_id);
            $user = $request->attributes->get('user');

            $member = RosterMember::where([
                'user_id' => $request->user_id,
                'join_events_id' => $request->join_events_id,
                'team_id' => $request->team_id
            ])->firstOrFail();

            if ( isset($joinEvent->roster_captain_id) && $member->id == $joinEvent->roster_captain_id) {
                $isAcceptedMember = TeamMember::where([
                    'team_id' => $request->team_id,
                    'status' => 'accepted',
                    'user_id' => $request->user_id,
                ])->exists();
                
                $userRoster = RosterMember::where([
                    'join_events_id' => $request->join_events_id,
                    'user_id' =>  $user->id,
                ])->first();

                if ($isAcceptedMember && $joinEvent->roster_captain_id != $userRoster?->id) {
                    return response()->json(['success' => false, 
                        'message' => 'Captain can remove himself from roster only!'
                    ]);
                }
            }

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
            if ($joinEvent->join_status != "pending") {
                return response()->json([
                    'success' => false, 
                    'message' => 'Roster is now locked.'
                ]);
            }
          
            if (isset($joinEvent->roster_captain_id)) {
                $user = $request->attributes->get('user');
                $userRoster = RosterMember::where([
                    'join_events_id' => $request->join_events_id,
                    'user_id' => $user->id,
                ])->first();

                $capRoster = RosterMember::find($joinEvent->roster_captain_id);

                if ($capRoster) {
                    $isAcceptedMember =  TeamMember::where([
                        'team_id' => $capRoster->team_id,
                        'status' => 'accepted',
                        'user_id' => $capRoster->user_id,
                    ])->exists();
                } else {
                    $isAcceptedMember = false;
                }

                if ($isAcceptedMember && $joinEvent->roster_captain_id != $userRoster?->id) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Only captain can remove himself or appoint another as captain.'
                    ]);
                }
            }

            $joinEvent->roster_captain_id = $request->roster_captain_id;
            $joinEvent->save();
            return response()->json(['success' => true, 'message' => 'Roster captain created']);
        }   catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
   
}
