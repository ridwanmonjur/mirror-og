<?php
namespace App\Services;

use App\Jobs\HandleFollows;
use App\Models\ActivityLogs;
use App\Models\Friend;
use App\Models\OrganizerFollow;
use Illuminate\Support\Str;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\ParticipantFollow;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SocialService {
    public function handleOrganizerFollowsAndActivityLogs(User $participant, User $organizer): array
    {
        $existingFollow = OrganizerFollow::where('participant_user_id', $participant->id)
            ->where('organizer_user_id', $organizer->id)
            ->first();
        
        if ($existingFollow) {
            $existingFollow->delete();
            $message = 'Successfully Unfollowed the organizer';
            $isFollowing = false;
            dispatch(new HandleFollows('Unfollow', [
                'subject_type' => User::class,
                'object_type' => User::class,
                'subject_id' => $participant->id,
                'object_id' => $organizer->id,
                'action' => 'Follow',
            ]));

        } else {
            OrganizerFollow::create([
                'participant_user_id' => $participant->id,
                'organizer_user_id' => $organizer->id,
            ]);
            $message = 'Successfully followed the organizer';
            $isFollowing = true;
            dispatch(new HandleFollows('Follow', [
                'subject_type' => User::class,
                'object_type' => User::class,
                'subject_id' => $participant->id,
                'object_id' => $organizer->id,
                'action' => 'Follow',
                'log' => $isFollowing ? sprintf(
                    '<span class="notification-gray"> You started following another organizer, <span class="notification-black">%s.</span></span>',
                    $organizer->name
                ) : null
            ]));
        }

        

        return [
            'message' => $message,
            'isFollowing' => $isFollowing,
        ];
    }

    public function handleFriendOperation($user, array $validatedData): array
    {
        DB::beginTransaction();
        try {
            if (isset($validatedData['deleteUserId'])) {
                $friend = Friend::checkFriendship($validatedData['deleteUserId'], $user->id);
                $friend?->deleteOrFail();
                $result = ['type' => 'successMessage', 'message' => 'Your request has been deleted.'];
            }
            elseif (isset($validatedData['addUserId'])) {
                if ($user->id == $validatedData['addUserId']) {
                    throw new Exception('You are befriending yourself!');
                }

                Friend::create([
                    'user1_id' => $user->id,
                    'user2_id' => intval($validatedData['addUserId']),
                    'status' => 'pending',
                    'actor_id' => $user->id,
                ]);

                $result = ['type' => 'successMessage', 'message' => 'Successfully created a friendship'];
            }
            else {
                $updateUser = User::where('id', $validatedData['updateUserId'])
                    ->select(['id', 'userBanner', 'name'])
                    ->firstOrFail();
                    
                $friend = Friend::checkFriendship($validatedData['updateUserId'], $user->id);
                $status = $validatedData['updateStatus'];
                
                $friend->update([
                    'actor_id' => $user->id,
                    'status' => $status
                ]);

                // Handle activity logs for status changes
                if ($status === 'left') {
                    ActivityLogs::findActivityLog([
                        'subject_type' => User::class,
                        'subject_id' => [$user->id, $updateUser->id],
                        'object_type' => Friend::class,
                        'object_id' => $friend->id,
                        'action' => 'friend',
                    ])->delete();
                } elseif ($status === 'accepted') {
                    ActivityLogs::createActivityLogs([
                        'subject_type' => User::class,
                        'subject_id' => [$user->id, $updateUser->id],
                        'object_type' => Friend::class,
                        'object_id' => $friend->id,
                        'action' => 'friend',
                        'log' => [
                            $this->generateLogHtml($updateUser, $updateUser),
                            $this->generateLogHtml($user, $user)
                        ],
                    ]);
                }

                $messages = [
                    'left' => 'Successfully removed friendship.',
                    'accepted' => 'Successfully accepted friendship request.',
                    'rejected' => 'Successfully rejected friendship request.',
                ];
                $result = ['type' => 'successMessage', 'message' => $messages[$status]];
            }

            DB::commit();
            return $result;

        } catch (Exception $e) {
            DB::rollBack();
            if (in_array($e->getCode(), ['23000', '1062'])) {
                throw new Exception('You have had a previous friend request!');
            }
            throw $e;
        }
    }

    private function generateLogHtml($imageUser, $linkUser): string
    {
        return <<<HTML
            <a href="/view/participant/{$imageUser->id}" alt="Friend Image link">
                <img class="object-fit-cover rounded-circle me-2" 
                    width="30" height="30"  
                    src="/storage/{$imageUser->userBanner}" 
                    alt="Profile picture of {$imageUser->name}"
                    onerror="this.src='/assets/images/404.png';">
            </a>
            <span class="notification-gray">
                You and 
                <a href="/view/participant/{$linkUser->id}" alt="Friend link">  
                    <span class="notification-blue">{$linkUser->name}</span>  
                </a>
                are friends.
            </span>
        HTML;
    }

    public function handleParticipantFollow(Request $request)
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

            return [
                'success' => true,
                'message' => "Successfully unfollowed the participant",
                'isFollowing' => false,
            ];
        } 

        DB::beginTransaction();
        try {
            $updateUser = User::where('id', $participantId)
                    ->select(['id', 'userBanner', 'name'])
                    ->firstOrFail();

            $follow = ParticipantFollow::create([
                'participant_follower' => $userId,
                'participant_followee' => $participantId,
            ]);

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

            return [
                'success' => true,
                'message' => "Successfully followed the participant",
                'isFollowing' => true,
            ];

        } catch (Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

}