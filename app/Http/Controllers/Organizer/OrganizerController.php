<?php

namespace App\Http\Controllers\Organizer;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrganizersRequest;
use App\Models\Address;
use App\Models\EventDetail;
use App\Models\EventInvitation;
use App\Models\EventTier;
use App\Models\Follow;
use App\Models\JoinEvent;
use App\Models\Organizer;
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
        
        $followersCount = Follow::where('organizer_user_id', $user_id)->count();
        $joinEvents = EventDetail::where('user_id', $user_id)
            ->with( ['tier',  'game'])->get();
        $lastYearEventsCount = EventDetail::whereYear('created_at', now()->year)
            ->where('user_id', $user_id)
            ->whereNotIn('status', ['DRAFT', 'PENDING'])
            ->count();
        $beforeLastYearEventsCount = EventDetail::whereYear('created_at', '<=', now()->year - 1)
            ->where('user_id', $user_id)    
            ->whereNotIn('status', ['DRAFT'. 'PENDING'])
            ->count();

        $teamsCount = JoinEvent::whereIn('event_details_id', function ($query) use ($user_id) {
                $query->select('id')
                    ->from('event_details')
                    ->whereNotIn('status', ['DRAFT', 'ENDED', 'PENDING'])
                    ->where('user_id', $user_id);
            })
            ->count();

        $tierPrizeCount = DB::table('event_details')
                ->where('event_details.user_id', $user_id) 
                ->whereNotIn('status', ['DRAFT', 'PENDING'])
                ->leftJoin('event_tier', 'event_details.event_tier_id', '=', 'event_tier.id')
                ->select(['event_details.id as event_id', 
                    'event_details.event_tier_id',
                    'event_tier.tierPrizePool'
                ])
                ->sum('tierPrizePool');

        $userIds = $joinEvents->pluck('user_id')->flatten()->toArray();
        $followCounts = Follow::getFollowCounts($userIds);
        $isFollowing = Follow::getIsFollowing($user_id, $userIds);
        $joinEventsHistory = $joinEventsActive = $values = [];
        ['joinEvents' => $joinEvents, 'activeEvents' => $joinEventsActive, 'historyEvents' => $joinEventsHistory] 
            = EventDetail::processEvents($joinEvents, $isFollowing);


        return view('Organizer.PlayerProfile', 
            compact('joinEvents', 'userProfile', 'isOwnProfile', 'followersCount',
                'joinEventsHistory', 'joinEventsActive', 'followCounts', 'lastYearEventsCount',
                'beforeLastYearEventsCount', 'teamsCount', 'tierPrizeCount'
            )
        );
       
    }

    public function editProfile(UpdateOrganizersRequest $request) {
        $user = $request->attributes->get('user');

        $validatedData = $request->validated();

        $address = $validatedData['address']['id']
            ? Address::findOrFail($validatedData['address']['id'])
            : new Address();
        $address->fill($validatedData['address'])->save();
        
        User::where('id', $user->id)
            ->first()
            ->fill($validatedData['userProfile'])->save();

        $organizer = $validatedData['organizer']['id']
            ? Organizer::findOrFail($validatedData['organizer']['id'])
            : new Organizer();
        // dd($validatedData['organizer']);
        $organizer->fill($validatedData['organizer'])->save();

        return response()->json(['success' => true, 'message' => 'Data saved successfully']);
    }
}
