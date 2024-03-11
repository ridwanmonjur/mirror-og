  
<div class="tabs">
    <button class="tab-button inner-tab tab-button-active" onclick="showTab(event, 'CurrentMembers', 'inner-tab')">Current
        Members</button>
    <button class="tab-button inner-tab" onclick="showTab(event, 'PendingMembers', 'inner-tab')">Pending Members</button>
    <button class="tab-button inner-tab" onclick="showTab(event, 'NewMembers', 'inner-tab')">New Members</button>
</div>
<br>
<div class="tab-content inner-tab" id="CurrentMembers">
    <p class="text-center mx-auto mt-2">Team {{ $selectTeam->teamName }} has
        {{ $teamMembersProcessed['accepted']['count'] }} accepted members
    </p>
    @if ($teamMembersProcessed['accepted']['count'] != 0)
        <div class="cont mt-3 pt-3">
            <table class="member-table">
                <tbody>
                    @foreach ($teamMembersProcessed['accepted']['members'] as $member)
                        <tr class="st">
                            {{-- <td class="colorless-col">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-gear" viewBox="0 0 16 16">
                                    <path
                                        d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0" />
                                    <path
                                        d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z" />
                                </svg>
                            <td> --}}
                            <td class="coloured-cell">
                                <div class="player-info">
                                    <div class="player-image"
                                        style="background-image: url('https://www.vhv.rs/dpng/d/511-5111355_register-super-admin-icon-png-transparent-png.png')">
                                        <span class="crown">&#x1F451;</span> <!-- Crown emoji -->
                                    </div>
                                    <span>{{ $member->user->name }}</span>
                                </div>
                            </td>
                            <td class="flag-cell coloured-cell">
                                <img class="nationality-flag" src="{{ asset('/assets/images/china.png') }}"
                                    alt="User's flag">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
<div class="tab-content inner-tab d-none" id="PendingMembers" data-type="member" style="text-align: center;">
    <p class="text-center mx-auto mt-2">Team {{ $selectTeam->teamName }} has
        {{ $teamMembersProcessed['pending']['count'] }} pending members
    </p>
    @if ($teamMembersProcessed['pending']['count'] != 0)
        <div class="cont mt-3 pt-3">
            <table class="member-table">
                <tbody>
                    @foreach ($teamMembersProcessed['pending']['members'] as $member)
                        <tr class="st">
                            <td>
                                <div class="player-info">
                                    <div class="player-image"
                                        style="background-image: url('https://cdn-icons-png.flaticon.com/512/149/149071.png')">
                                    </div>
                                    <div class="player-image"
                                        style="background-image: url('{{ $pendingMember->user->profile_image_url }}')">
                                    </div>
                                    <span>{{ $member->user->name }}</span>
                                </div>
                            </td>
                            <td class="flag-cell">
                                <img class="nationality-flag" src="{{ asset('/assets/images/china.png') }}"
                                    alt="User's flag">
                            </td>
                            <td>
                                @if ($user->id == $selectTeam->creator_id)
                                    <button onclick="approveMember({{$member->id}})"
                                        style="background-color: #3498db; color: #fff; border: none; padding: 5px 10px; cursor: pointer; margin-right: 5px;">
                                        ✔
                                    </button>
                                    <button onclick="rejectMember({{ $member->id }})"
                                        style="background-color: #e74c3c; color: #fff; border: none; padding: 5px 10px; cursor: pointer;">
                                        ✘
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
<div class="tab-content inner-tab d-none" id="NewMembers">
    <p class="text-center mx-auto mt-2">Team {{ $selectTeam->teamName }} has
        {{ $teamMembersProcessed['accepted']['count'] }} accepted members
    </p>
    @if ($teamMembersProcessed['accepted']['count'] != 0)
        <div class="cont mt-3 pt-3">
            <div class="leftC">
                <span class="icon2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-filter">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3">
                        </polygon>
                    </svg>
                    <span> Filter </span>
                </span>
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <span class="icon2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.2 7.8l-7.7 7.7-4-4-5.7 5.7" />
                        <path d="M15 7h6v6" />
                    </svg>
                    <span>
                        Sort
                    </span>
                </span>
            </div>
            <div class="rightC">
                <div class="search_box">
                    <i class="fa fa-search"></i>
                    <input class="nav__input" type="text" placeholder="Search for player name">
                </div>
                <div style="padding-right: 200px; transform: translateY(-95%);">
                    @if ($user->id == $selectTeam->creator_id)
                        <img src="/assets/images/add.png" height="40px" width="40px">
                    @endif
                </div>
            </div>
        </div>
        <table class="member-table">
            <tbody>
                @foreach ($teamMembersProcessed['accepted']['members'] as $member)
                    <tr class="st">
                        <td class="colorless-col">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-gear" viewBox="0 0 16 16">
                                <path
                                    d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0" />
                                <path
                                    d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z" />
                            </svg>
                        <td>
                        <td class="coloured-cell">
                            <div class="player-info">
                                <div class="player-image"
                                    style="background-image: url('https://www.vhv.rs/dpng/d/511-5111355_register-super-admin-icon-png-transparent-png.png')">
                                    <span class="crown">&#x1F451;</span> <!-- Crown emoji -->
                                </div>
                                <span>{{ $member->user->name }}</span>
                            </div>
                        </td>
                        <td class="flag-cell coloured-cell">
                            <img class="nationality-flag" src="{{ asset('/assets/images/china.png') }}"
                                alt="User's flag">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>


