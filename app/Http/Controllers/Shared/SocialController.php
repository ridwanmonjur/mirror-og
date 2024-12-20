<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\FriendRequest;
use App\Http\Requests\User\LikeRequest;
use App\Http\Requests\User\UpdateParticipantsRequest;
use App\Jobs\HandleFollows;
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
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Io238\ISOCountries\Models\Country;


class SocialController extends Controller
{
    public function followOrganizer(Request $request)
    {
        $user = $request->attributes->get('user');
        $userId = $user->id;
        $organizerId = $request->organizer_id;
        $existingFollow = OrganizerFollow::where('participant_user_id', $userId)
            ->where('organizer_user_id', $organizerId)
            ->first();
        $organizer = User::findOrFail($organizerId);

        if ($existingFollow) {
            dispatch(new HandleFollows('Unfollow', [
                'subject_type' => User::class,
                'object_type' => User::class,
                'subject_id' => $userId,
                'object_id' => $organizerId,
                'action' => 'Follow',
            ]));

            $existingFollow->delete();

            return response()->json([
                'message' => 'Successfully Unfollowed the organizer',
                'isFollowing' => false,
            ], 201);
        }
        OrganizerFollow::create([
            'participant_user_id' => $userId,
            'organizer_user_id' => $organizerId,
        ]);

        // dispatch(new HandleFollows('Unfollow', [
            //     'subject_type' => User::class,
            //     'object_type' => User::class,
            //     'subject_id' => $userId,
            //     'object_id' => $organizerId,
            //     'action' => 'Follow',
            //     'log' => '<span class="notification-gray"> User'
            //     . ' <span class="notification-black">' . $user->name . '</span> started following '
            //     . ' <span class="notification-black">' . $organizer->name . '.</span> '
            //     . '</span>'
        // ]));

        return response()->json([
            'message' => 'Successfully followed the organizer',
            'isFollowing' => true,
        ], 201);
    }


    public function updateFriend(FriendRequest $request)
    {
        $user = $request->attributes->get('user');
        $validatedData = $request->validated();
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
                    ActivityLogs::findActivityLog([
                        'subject_type' => User::class,
                        'subject_id' => [$user->id, $updateUser->id],
                        'object_type' => Friend::class,
                        'object_id' => $friend->id,
                        'action' => $FRIEND_ACTION,
                    ])->delete();
                }

                if ($status === 'accepted') {
                    ActivityLogs::createActivityLogs([
                        'subject_type' => User::class,
                        'subject_id' => [$user->id, $updateUser->id],
                        'object_type' => Friend::class,
                        'object_id' => $friend->id,
                        'action' => $FRIEND_ACTION,
                        'log' => [
                            <<<HTML
                                <a href="/view/participant/{$updateUser->id}" alt="Friend Image link"> 
                                    <img class="object-fit-cover rounded-circle me-2" 
                                        width="30" height="30"  
                                        src="/storage/{$updateUser->userBanner}" 
                                        alt="Profile picture of {$updateUser->name}"
                                        onerror="this.src='/assets/images/404.png';">
                                </a>
                                <span class="notification-gray me-2">
                                    You and  
                                    <a href="/view/participant/{$updateUser->id}" alt="Friend link"> 
                                        <span class="notification-blue">{$updateUser->name}</span>  
                                    </a>
                                    are friends.
                                </span>
                            HTML,
                            <<<HTML
                                <a href="/view/participant/{$user->id}" alt="Friend Image link">
                                    <img class="object-fit-cover rounded-circle me-2" 
                                        width="30" height="30"  
                                        src="/storage/{$user->userBanner}" 
                                        alt="Profile picture of {$user->name}"
                                        onerror="this.src='/assets/images/404.png';">
                                </a>
                                <span class="notification-gray">
                                    You and 
                                    <a href="/view/participant/{$user->id}" alt="Friend link">  
                                        <span class="notification-blue">{$user->name}</span>  
                                    </a>
                                    are friends.
                                </span>
                            HTML,
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
        if ($existingFollow) {
            ActivityLogs::findActivityLog([
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

                session()->flash('successMessage', $message);
                return back();
            } catch (Exception $e) {
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
        $perPage = 5;
        $response = [];

        if ($type === 'all') {
            if ($role == 'ORGANIZER') {
                $response['count'] = [
                    'followers' => OrganizerFollow::getOrganizerFollowersPaginate($id, $perPage, $search)
                ];
            } else {
                $response['count'] = [
                    'followers' => ParticipantFollow::where('participant_followee', $id)
                        ->when($search, function($query) use ($search) {
                            $query->whereHas('followerUser', function($q) use ($search) {
                                $q->where('name', 'LIKE', "%{$search}%");
                            });
                        })
                        ->count(),
                    'following' => ParticipantFollow::where('participant_follower', $id)
                        ->when($search, function($query) use ($search) {
                            $query->whereHas('followeeUser', function($q) use ($search) {
                                $q->where('name', 'LIKE', "%{$search}%");
                            });
                        })
                        ->count(),
                    'friends' =>  Friend::where(function ($query) use ($id) {
                        $query->where('user1_id', $id)
                            ->orWhere('user2_id', $id);
                    })
                        ->when($search, function($query) use ($search) {
                            $query->where(function($q) use ($search) {
                                $q->whereHas('user1', function($q1) use ($search) {
                                    $q1->where('name', 'LIKE', "%{$search}%");
                                })
                                ->orWhereHas('user2', function($q2) use ($search) {
                                    $q2->where('name', 'LIKE', "%{$search}%");
                                });
                            });
                        })
                        ->where('status', 'accepted')
                        ->count()
                ];
            }
        } else {
            $data = match($type) {
                'followers' => $role == 'ORGANIZER' 
                    ? OrganizerFollow::getOrganizerFollowersPaginate($id, $perPage, $page, $search)
                    : ParticipantFollow::getFollowersPaginate($id, $perPage, $page, $search),
                'following' => ParticipantFollow::getFollowingPaginate($id, $perPage, $page, $search),
                'friends' => Friend::getFriendsPaginate($id, $perPage, $page, $search),
                default => throw new \InvalidArgumentException('Invalid connection type')
            };
            $response['connections'] = [$type => $data];
        }

        return response()->json($response);
    }

  

}
