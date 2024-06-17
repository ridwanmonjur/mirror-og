<<<<<<< HEAD
@php
    use Carbon\Carbon;
@endphp
<div>
    <br>
    <div class="showcase tab-size showcase-box showcase-column border-3 pt-4 ps-4 text-left" style="width: max(500px, 55vw);">
        <p> Name: {{ $userProfile->name }} </p>
        <p> Email: {{ $userProfile->email }} </p>
        <p> Joined: {{ is_null($userProfile->created_at) ? '-' : Carbon::parse($userProfile->created_at)->format('F j, Y') }}</p>
=======
<div>
    <br>
    <div class="showcase tab-size showcase-box showcase-column pt-4 grid-4-columns text-left" style="width: max(500px, 55vw);">
        <div> 
            <h3 class="pl-0 ml-0 text-left"> Name </h3>
            <p> {{ $userProfile->name }} </p>
        </div>
        <div> 
            <h3 class="pl-0 ml-0"> Email </h3>
            <p> {{ $userProfile->email }} </p>
        </div>
>>>>>>> 8627f53 (completed feature without background)
    </div>
    <br>
    <div class="tabs">
        <button id="FollowBtn" 
            @class(["tab-button outer-tab", "tab-button-active" => $currentTab == 'Follow'])
            wire:click="setTab('Follow')">Followers
        </button>
        @if ($userProfile->role != 'ORGANIZER')
            <button id="FollowingBtn"
            @class(["tab-button outer-tab", "tab-button-active" => $currentTab == 'Following'])
                wire:click="setTab('Following')">
                Following
            </button>
            <button id="FriendsBtn" 
            @class(["tab-button outer-tab", "tab-button-active" => $currentTab == 'Friends'])
                wire:click="setTab('Friends')">
                Friends
            </button>
        @endif
    </div>
    <br>
    <div>
        @livewire("profile.friend-follow-list", [
            'userId' => $userId, 'name' => 'Follow', 'currentTab' => $currentTab
        ], key($currentTab . 'Follow'))
        @livewire("profile.friend-follow-list", [
            'userId' => $userId, 'name' => 'Following', 'currentTab' => $currentTab
        ], key($currentTab . 'Following'))
        @livewire("profile.friend-follow-list", [
            'userId' => $userId, 'name' => 'Friends', 'currentTab' => $currentTab
        ], key($currentTab . 'Friends'))
    </div>
                   
 </div>