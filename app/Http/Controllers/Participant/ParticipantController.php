<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\FriendRequest;
use App\Http\Requests\LikeRequest;
use App\Http\Requests\UpdateParticipantsRequest;
use App\Models\ActivityLogs;
use App\Models\EventInvitation;
use App\Models\Friend;
use App\Models\JoinEvent;
use App\Models\Like;
use App\Models\OrganizerFollow;
use App\Models\Participant;
use App\Models\ParticipantFollow;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Io238\ISOCountries\Models\Country;

class ParticipantController extends Controller
{
    public function searchParticipant(Request $request)
    {
        try {
            $page = 5;
            $userList = User::getParticipants($request)->paginate($page);
            foreach ($userList as $user) {
                // @phpstan-ignore-next-line
                $user->is_in_team = $user->members->isNotEmpty();
            }

            return response()->json(['data' => $userList, 'success' => true]);
        } catch (Exception $e) {
            return response()->json(['data' => [], 'success' => false, 'error' => $e->getMessage()], 400);
        }
    }

    public function viewRequest(Request $request)
    {
        $user = $request->attributes->get('user');
        $user_id = $user->id;

        // pending requests
        $invitedTeamAndMemberList = Team::join('team_members', 'teams.id', '=', 'team_members.team_id')
            ->where('team_members.user_id', $user_id)
            ->where(function ($query) {
                $query->where([
                    ['team_members.status', 'pending'],
                    ['team_members.actor', 'team'],
                ])->orWhere([
                    ['team_members.status', 'rejected'],
                    ['team_members.actor', 'user'],
                ]);
            })
            ->select('teams.*', 'team_members.*')
            ->get();

        $teamIdList = $invitedTeamAndMemberList->pluck('team_id')->toArray();
        $membersCount = DB::table('teams')
            ->leftJoin('team_members', 'teams.id', '=', 'team_members.team_id')
            ->whereIn('teams.id', $teamIdList)
            ->where(function ($query) {
                $query->where([
                    ['team_members.status', 'pending'],
                    ['team_members.actor', 'team'],
                ])->orWhere([
                    ['team_members.status', 'rejected'],
                    ['team_members.actor', 'user'],
                ]);
            })
            ->groupBy('teams.id')
            ->selectRaw('teams.id as team_id, COALESCE(COUNT(team_members.id), 0) as member_count')
            ->pluck('member_count', 'team_id')
            ->toArray();

        // sentTeam
        $pendingTeamAndMemberList = Team::join('team_members', 'teams.id', '=', 'team_members.team_id')
            ->where('team_members.user_id', $user_id)
            ->where([
                ['team_members.status', 'pending'],
                ['team_members.actor', 'user'],
            ])
            ->select('teams.*', 'team_members.*')
            ->get();

        // invitations
        $teamMembersList = TeamMember::where('user_id', $user_id)->pluck('team_id')->unique();
        $invitedEventsList = EventInvitation::whereIn('team_id', $teamMembersList)
            ->with('event', 'event.tier', 'event.game', 'event.user')
            ->get();

        // dd($invitedTeamAndMemberList, $membersCount, $pendingTeamAndMemberList, $pendingTeamAndMemberList, $invitedEventsList);
        return view('Participant.ParticipantRequest', compact('membersCount', 'invitedTeamAndMemberList', 'pendingTeamAndMemberList', 'invitedEventsList'));
    }

    public function viewOwnProfile(Request $request)
    {
        try {
            $user = $request->attributes->get('user');
            $user_id = $user?->id ?? null;

            return $this->viewProfile($request, $user_id, $user, true);
        } catch (Exception $e) {
            return $this->showErrorParticipant($e->getMessage());
        }
    }

    public function viewProfileById(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $loggedInUser = Auth::user();

            if ($user->role === 'ORGANIZER') {
                return redirect()->route('public.organizer.view', ['id' => $id]);
            }
            if ($user->role === 'ADMIN') {
                return $this->showErrorParticipant('This is an admin view!');
            }

            return $this->viewProfile($request, $loggedInUser ? $loggedInUser->id : null, $user, false);
        } catch (Exception $e) {
            return $this->showErrorParticipant($e->getMessage());
        }
    }

    public function editProfile(UpdateParticipantsRequest $request)
    {
        $validatedData = $request->validated();
        $participant = Participant::findOrFail($validatedData['participant']['id']);
        $participant->update($validatedData['participant']);
        $user = User::findOrFail($validatedData['user']['id']);
        $user->update($validatedData['user']);
        if (isset($participant->region)) {
            $region = Country::select('emoji_flag', 'name', 'id')
                ->findOrFail($participant->region);
        } else {
            $region = null;
        }

        return response()->json([
            'message' => 'Participant updated successfully',
            'success' => true,
            'age' => $participant->age,
            'region' => $region,
        ], 200);
    }

    public function likeEvent(LikeRequest $request)
    {
        $validatedData = $request->validated();
        $user = $request->attributes->get('user');
        $existingLike = Like::where('user_id', $user->id)
            ->where('event_id', $validatedData['event_id'])
            ->first();

        if ($existingLike) {
            $existingLike->delete();

            return response()->json([
                'success' => true,
                'message' => 'Successfully unliked the event',
                'isLiked' => false,
            ], 201);
        }
        Like::create([
            'user_id' => $user->id,
            'event_id' => $validatedData['event_id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully liked the event',
            'isLiked' => true,
        ], 201);
    }

    public function updateFriend(FriendRequest $request)
    {
        $user = $request->attributes->get('user');
        $validatedData = $request->validated();
        $activityLog = new ActivityLogs();
        if (array_key_exists('deleteUserId', $validatedData)) {
            $friend = Friend::checkFriendship($validatedData['deleteUserId'], $user->id);
            $friend?->deleteOrFail();

            User::where('id', $request->input('deleteUserId'))->select("id")->firstOrFail();;

            session()->flash('successMessage', 'Your request has been deleted.');

            return back();
        }
        if (array_key_exists('addUserId', $validatedData)) {
            try {
                User::where('id', $request->input('addUserId'))
                    ->select("id")->firstOrFail();
                $friend = Friend::create([
                    'user1_id' => $user->id,
                    'user2_id' => intval($validatedData['addUserId']),
                    'status' => 'pending',
                    'actor_id' => $user->id,
                ]);

                session()->flash('successMessage', 'Successfully created a friendship');

                return back();
            } catch (Exception $e) {
                if ($e->getCode() === '23000' || $e->getCode() === 1062) {
                    $errorMessage = 'You have had a previous friend request!';
                } else {
                    $errorMessage = $e->getMessage();
                }

                session()->flash('errorMessage', $errorMessage);

                return back();
            }
        } else {
            DB::beginTransaction();
            $FRIEND_ACTION = "friend";

            try {
                // dd($request->input('updateUserId'));
                $updateUser = User::where('id', $request->input('updateUserId'))
                    ->select(['id', 'userBanner', 'name'])
                    ->firstOrFail();
                $friend = Friend::checkFriendship($validatedData['updateUserId'], $user->id);
                $status = $validatedData['updateStatus'];
                $isPermitted = $status === 'left';
                if (! $isPermitted) {
                    if ($status === 'accepted' || $status === 'rejected') {
                        $isPermitted = ($friend->status === 'pending' && $user->id !== $friend->actor_id) ||
                        ($friend->status === 'left' && $user->id === $friend->actor_id) ||
                        ($friend->status === 'rejected' && $user->id === $friend->actor_id);
                    }
                }

                if (! $isPermitted) {
                    session()->flash('errorMessage', 'This request is not allowed.');

                    return back();
                }

                $friend->update([
                    'actor_id' => $user->id,
                    'status' => $status,
                ]);

                if ($status === 'left') {
                    $activityLog->findActivityLog([
                        'subject_type' => User::class,
                        'subject_id' => [$user->id, $updateUser->id],
                        'object_type' => Friend::class,
                        'object_id' => $friend->id,
                        'action' => $FRIEND_ACTION,
                    ])->delete();
                }

                if ($status === 'accepted') {
                    $activityLog->createActivityLogs([
                        'subject_type' => User::class,
                        'subject_id' => [$user->id, $updateUser->id],
                        'object_type' => Friend::class,
                        'object_id' => $friend->id,
                        'action' => $FRIEND_ACTION,
                        'log' =>  [<<<HTML
                        <a href="/view/participant/$updateUser->id" alt="Friend Image link"> 
                            <img class="object-fit-cover rounded-circle me-2" 
                                width='30' height='30'  
                                src="/storage/$updateUser->userBanner" 
                                onerror="this.src='/assets/images/404.png';"
                            >
                        </a>
                        <span class="notification-gray me-2">
                            You and  
                            <a href="/view/participant/$updateUser->id" alt="Friend link"> 
                                <span class="notification-blue">{$updateUser->name}</span>  
                            </a>
                            are friends.
                        </span>
                        HTML,
                        <<<HTML
                            <a href="/view/participant/$user->id" alt="Friend Image link">
                                <img class="object-fit-cover rounded-circle me-2" 
                                    width='30' height='30'  
                                    src="/storage/$user->userBanner" 
                                    onerror="this.src='/assets/images/404.png';"
                                >
                            </a>
                            <span class="notification-gray">
                                You and 
                                <a href="/view/participant/$user->id" alt="Friend link">  
                                    <span class="notification-blue">{$user->name}</span>  
                                        are friends.
                                    </span>
                                </a>
                            HTML
                        ],
                    ]);
                }
                DB::commit();

                $message = [
                    'left' => 'Successfully removed friendship.',
                    'accepted' => 'Successfully accepted friendship request.',
                    'rejected' => 'Successfully rejected friendship request.',
                ];

                session()->flash('successMessage', $message[$status]);

                return back();
            } catch (Exception $e) {
                DB::rollBack();
                return $this->showErrorParticipant($e->getMessage());
            }
        }
    }

    public function followParticipant(Request $request)
    {
        $PARTICIPANT_FOLLOW_ACTION = "participant_follow";

        $user = $request->attributes->get('user');
        $userId = $user->id;
        $participantId = $request->participant_id;
        $existingFollow = ParticipantFollow::checkFollow($user->id, $participantId);
        $activityLog = new ActivityLogs();
        if ($existingFollow) {
            $activityLog->findActivityLog([
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'object_type' => ParticipantFollow::class,
                'object_id' => $existingFollow->id,
                'action' => $PARTICIPANT_FOLLOW_ACTION,
            ])->delete();
           
            $existingFollow->delete();

            $message = 'Successfully unfollowed the participant';
            session()->flash('successMessage', $message);
            return back();
        } else {
            DB::beginTransaction();
            
            try {
                $updateUser = User::where('id', $participantId)
                        ->select(['id', 'userBanner', 'name'])
                        ->firstOrFail();

                $follow = ParticipantFollow::create([
                    'participant_follower' => $userId,
                    'participant_followee' => $participantId,
                ]);

                $message = 'Successfully followed the participant';
                $activityLog->createActivityLogs([
                    'subject_type' => User::class,
                    'subject_id' => [$user->id],
                    'object_type' => ParticipantFollow::class,
                    'object_id' => $follow->id,
                    'action' => $PARTICIPANT_FOLLOW_ACTION,
                    'log' =>  <<<HTML
                    <a href="/view/participant/$updateUser->id" alt="Follow Image link"> 
                        <img class="object-fit-cover rounded-circle me-2" 
                            width='30' height='30'  
                            src="/storage/$updateUser->userBanner" 
                            onerror="this.src='/assets/images/404.png';"
                        >
                    </a>
                    <span class="notification-gray me-2">
                        You started  following another player,
                        <a href="/view/participant/$updateUser->id" alt="Follow link"> 
                            <span class="notification-blue">{$updateUser->name}</span>  
                        </a>.
                    </span>
                    HTML,

                ]); 
                DB::commit();

                session()->flash('successMessage', $message);
                return back();
            } catch (Exception $e) {
                DB::rollBack();
                session()->flash('errorMessage', $e->getMessage());
            }
        }

       
    }

    private function viewProfile(Request $request, $logged_user_id, $userProfile, $isOwnProfile = true)
    {
        try {
            [
                'teamList' => $teamList,
                'teamIdList' => $teamIdList,
            ] = Team::getUserTeamList($userProfile->id);
            $pastTeam = Team::getUserPastTeamList($userProfile->id);

            $awardList = Team::getAwardListByTeamIdList($teamIdList);
            $achievementList = Team::getAchievementListByTeamIdList($teamIdList);
            $joinEvents = JoinEvent::getJoinEventsForTeamListWithEventsRosterResults($teamIdList);
            $totalEventsCount = $joinEvents->count();
            ['wins' => $wins, 'streak' => $streak] =
                JoinEvent::getJoinEventsWinCountForTeamList($teamIdList);

            $userIds = $joinEvents->pluck('eventDetails.user.id')->flatten()->toArray();
            $followCounts = OrganizerFollow::getFollowCounts($userIds);
            if ($logged_user_id) {
                $isFollowingOrganizerList = OrganizerFollow::getIsFollowing($logged_user_id, $userIds);
                $friend = Friend::checkFriendship($logged_user_id, $userProfile->id);
                $isFollowingParticipant = ParticipantFollow::checkFollow($logged_user_id, $userProfile->id);
            } else {
                $isFollowingOrganizerList = [];
                $friend = null;
                $isFollowingParticipant = null;
            }
            $joinEventsHistory = $joinEventsActive = $values = [];
            ['joinEvents' => $joinEvents, 'activeEvents' => $joinEventsActive, 'historyEvents' => $joinEventsHistory]
                = JoinEvent::processEvents($joinEvents, $isFollowingOrganizerList);

            $joinEventIds = $joinEvents->pluck('id')->toArray();

            return view(
                'Participant.PlayerProfile',
                compact(
                    'joinEvents',
                    'userProfile',
                    'teamList',
                    'isOwnProfile',
                    'joinEventsHistory',
                    'joinEventsActive',
                    'followCounts',
                    'totalEventsCount',
                    'wins',
                    'streak',
                    'awardList',
                    'achievementList',
                    'pastTeam',
                    'friend',
                    'isFollowingParticipant'
                )
            );
        } catch (Exception $e) {
            return $this->showErrorParticipant($e->getMessage());
        }
    }
}
