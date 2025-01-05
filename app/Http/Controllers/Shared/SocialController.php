<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\FriendRequest;
use App\Http\Requests\User\FriendUpdateRequest;
use App\Http\Requests\User\LikeRequest;
use App\Http\Requests\User\OrganizerFollowRequest;
use App\Http\Requests\User\UpdateParticipantsRequest;
use App\Jobs\HandleFollows;
use App\Models\ActivityLogs;
use App\Models\EventInvitation;
use App\Models\Friend;
use App\Models\JoinEvent;
use App\Models\Like;
use App\Models\OrganizerFollow;
use App\Models\TeamFollow;
use App\Models\Participant;
use App\Models\ParticipantFollow;
use App\Models\Report;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Services\SocialService;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Io238\ISOCountries\Models\Country;


class SocialController extends Controller
{
    public function __construct(private SocialService $socialService) {}

    public function followOrganizer(OrganizerFollowRequest $request)
    {
        try {
            $result = $this->socialService->handleOrganizerFollowsAndActivityLogs(
                $request->attributes->get('user'),
                $request->organizer
            );
            
            return response()->json($result, 201);
        } catch (Exception $e) {
            Log::error(($e->getMessage()));
            return response()->json([
                'success' => false,
                'message' => 'Failed to process follow/unfollow action',
            ], 500);
        }
    }


    public function updateFriend(FriendUpdateRequest $request)
    {
        try {
            $user = $request->attributes->get('user');
            
            $result = $this->socialService->handleFriendOperation($user, $request->toArray());

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $result['message']
                ]);
            }

            session()->flash($result['type'], $result['message']);
            return back();

        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 500);
            }

            return $this->showErrorParticipant($e->getMessage());
        }
        
    }

    public function followParticipant(Request $request)
    {
        $PARTICIPANT_FOLLOW_ACTION = "participant_follow";

        $user = $request->attributes->get('user');
        $userId = $user->id;
        $participantId = $request->participant_id;
        $existingFollow = ParticipantFollow::checkFollow($user->id, $participantId);
        if ($existingFollow) {
            ActivityLogs::findActivityLog([
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'object_type' => ParticipantFollow::class,
                'object_id' => $existingFollow->id,
                'action' => $PARTICIPANT_FOLLOW_ACTION,
            ])->delete();
           
            $existingFollow->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Successfully unfollowed user.",
                    'isFollowing' => false,
                ]);
            }

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
                ActivityLogs::createActivityLogs([
                    'subject_type' => User::class,
                    'subject_id' => [$user->id],
                    'object_type' => ParticipantFollow::class,
                    'object_id' => $follow->id,
                    'action' => $PARTICIPANT_FOLLOW_ACTION,
                    'log' =>  <<<HTML
                    <a href="/view/participant/{$updateUser->id}" alt="Follow Image link"> 
                        <img class="object-fit-cover rounded-circle me-2" 
                            width='30' height='30'  
                            alt="Profile picture of {$user->name}"
                            src="/storage/{$updateUser->userBanner}" 
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

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'isFollowing' => true,

                        'message' => "Successfully followed user."
                    ]);
                }

                session()->flash('successMessage', $message);
                return back();
            } catch (Exception $e) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ]);
                }

                DB::rollBack();
                session()->flash('errorMessage', $e->getMessage());
            }
        }
    }

    public function getConnections(Request $request, $id)
    {
        $type = $request->input('type', 'all');
        $role = $request->input('role', 'ORGANIZER');
        $page = $request->input('page', 1);
        $search = $request->input('search');
        $perPage = 6;
        $response = [];

        if ($type === 'all') {
            if ($role == 'PARTICIPANT') {
                $response['count'] = [
                    'followers' => ParticipantFollow::getFollowerCount($id),
                    'following' => ParticipantFollow::getFollowingCount($id),
                    'friends' =>  Friend::getFriendCount($id)
                ];
            } 
        } else {
            if ($request->has('loggedUserId')) {
                $loggedUser = User::where('id', $request->input('loggedUserId'))
                    ->select(['id', 'role'])
                    ->firstOrFail();
                
                $loggedUserId = $loggedUser?->id;
            } else {
                $loggedUserId = null;
            }

            $followers = null;
            if ($role === "ORGANIZER") {
                $followers = OrganizerFollow::getOrganizerFollowersPaginate($id, $loggedUserId, $perPage, $page, $search);
            } elseif ($role === "PARTICIPANT") {
                $followers = ParticipantFollow::getFollowersPaginate($id, $loggedUserId, $perPage, $page, $search);
            } else {
                $followers = TeamFollow::getFollowersPaginate($id,  $loggedUserId, $perPage, $page, $search);
            }
            
            $data = match($type) {
                'followers' => $followers,
                'following' => ParticipantFollow::getFollowingPaginate($id, $loggedUserId, $perPage, $page, $search),
                'friends' => Friend::getFriendsPaginate($id, $loggedUserId,  $perPage, $page, $search),
                default => throw new \InvalidArgumentException('Invalid connection type')
            };
            $response['connections'] = [$type => $data];
        }

        return response()->json($response);
    }

    public function toggleStar(Request $request, $id): JsonResponse
    {
        $authenticatedUser = $request->attributes->get('user');
        $user = User::where('id', $id)
            ->select('id')
            ->first();
        
        if ($authenticatedUser->hasStarred($user)) {
            $authenticatedUser->stars()->detach($user);
            $message = 'User unstarred successfully';
        } else {
            $authenticatedUser->stars()->attach($user);
            $message = 'User starred successfully';
        }

        return response()->json([
            'message' => $message,
            'is_starred' => $authenticatedUser->hasStarred($user)
        ]);
    }

    public function toggleBlock(Request $request, $id): JsonResponse
    {
        $authenticatedUser = $request->attributes->get('user');
        $user = User::where('id', $id)
            ->select('id')
            ->first();
        
        if ($authenticatedUser->hasBlocked($user)) {
            $authenticatedUser->blocks()->detach($user);
            $message = 'User unblocked successfully';
            $isBlocked = false;
        } else {
            $authenticatedUser->blocks()->attach($user);
            // Also remove any existing stars when blocking
            $authenticatedUser->stars()->detach($user);
            $message = 'User blocked successfully';
            $isBlocked = true;
        }

        return response()->json([
            'message' => $message,
            'is_blocked' => $isBlocked
        ]);
    }

    public function report(Request $request, $id): JsonResponse
    {
        $authenticatedUser = $request->attributes->get('user');
        $user = User::where('id', $id)
            ->select('id')
            ->first();

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000'
        ]);

        $report = Report::create([
            'reporter_id' => auth()->id(),
            'reported_user_id' => $user->id,
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Report submitted successfully',
            'report' => $report
        ], 201);
    }

    public function getStats(Request $request, $id): JsonResponse
    {
        $authenticatedUser = $request->attributes->get('user');
        $user = User::where('id', $id)
            ->select('id')
            ->first();

        $user = $request->attributes->get('user');
        return response()->json([
            'stars_count' => $user->starredBy()->count(),
            'is_starred' => $authenticatedUser->hasStarred($user) ,
            'is_blocked' => $authenticatedUser->hasBlocked($user) ,
        ]);
    }

    function getReports(Request $request, $id)  {
        $user = User::findOrFail($id);
    
        $reports = Report::where('reported_user_id', $user->id)
            ->with('reporter:id,name')  // Optionally include reporter details
            ->orderBy('created_at', 'desc')
            ->get();
    
        return response()->json([
            'reports' => $reports
        ]);
    }

}
