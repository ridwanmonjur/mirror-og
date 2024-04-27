<?php

namespace App\Http\Controllers\Organizer;
use App\Http\Controllers\Controller;
use App\Models\EventInvitation;
use App\Models\Follow;
use App\Models\JoinEvent;
use App\Models\Team;
use App\Models\TeamCaptain;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrganizerController extends Controller
{
    public function viewOwnProfile(Request $request) {
        $user = $request->attributes->get('user');
        $user_id = $user?->id ?? null;
        return $this->viewProfile($request, $user_id, $user, true);
    }

    public function viewProfileById(Request $request, $id) {
        $user = User::findOrFail($id);
        return $this->viewProfile($request, $id, $user, false);
    }

    private function viewProfile(Request $request, $user_id, $userProfile, $isOwnProfile = true) {
   
        [
            'teamList' => $teamList,
            'teamIdList' => $teamIdList,
        ] = Team::getUserTeamList($user_id);   

        $awardList = Team::getAwardListByTeamIdList($teamIdList);
        $achievementList = Team::getAchievementListByTeamIdList($teamIdList);
        $joinEvents = JoinEvent::getJoinEventsForTeamListWithEventsRosterResults($teamIdList);
        $totalEventsCount = $joinEvents->count();
        ['wins' => $wins, 'streak' => $streak] = 
            JoinEvent::getJoinEventsWinCountForTeamList($teamIdList);
        
        $userIds = $joinEvents->pluck('eventDetails.user.id')->flatten()->toArray();
        $followCounts = Follow::getFollowCounts($userIds);
        $isFollowing = Follow::getIsFollowing($user_id, $userIds);
        $joinEventsHistory = $joinEventsActive = $values = [];
        ['joinEvents' => $joinEvents, 'activeEvents' => $joinEventsActive, 'historyEvents' => $joinEventsHistory] 
            = JoinEvent::processEvents($joinEvents, $isFollowing);

        $joinEventIds = $joinEvents->pluck('id')->toArray();

        return view('Participant.Profile.PlayerProfile', 
            compact('joinEvents', 'userProfile', 'isOwnProfile'
                'joinEventsHistory', 'joinEventsActive', 'followCounts', 'totalEventsCount',
                'wins', 'streak', 'awardList', 'achievementList'
            )
        );
       
    }
}
