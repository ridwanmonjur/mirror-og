@php
    use Carbon\Carbon;
@endphp
<div>
    <br>
    <div class="showcase tab-size showcase-box showcase-column border-3 pt-4 ps-4 text-left" style="width: min(800px,  80%);">
        <p> Name: {{ $userProfile->name }} </p>
        <p> Email: {{ $userProfile->email }} </p>
        <p> Joined: {{ is_null($userProfile->created_at) ? '-' : Carbon::parse($userProfile->created_at)->format('F j, Y') }}</p>
    </div>
    <br>
    <div class="tabs">
        <button id="FollowBtn" 
            @class(["tab-button py-2 outer-tab", "tab-button-active" => $currentTab == 'Follow'])
            wire:click="setTab('Follow')">Followers
        </button>
        @if ($userProfile->role != 'ORGANIZER')
            <button id="FollowingBtn"
            @class(["tab-button py-2 outer-tab", "tab-button-active" => $currentTab == 'Following'])
                wire:click="setTab('Following')">
                Following
            </button>
            <button id="FriendsBtn" 
            @class(["tab-button py-2 outer-tab", "tab-button-active" => $currentTab == 'Friends'])
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