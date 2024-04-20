@php
    use Carbon\Carbon;
    $isRedirect = (isset($redirect) && $redirect);
@endphp

    <div class="mb-2 text-success mx-auto text-center">
        @if ($isRedirect)
            <span>
                You have joined this event successfully!
            </span>
            <a
                href="{{ route('participant.roster.manage', ['id' => $id, 'teamId' => $selectTeam->id]) }}">
                <button class="oceans-gaming-default-button oceans-gaming-default-button-link ms-2 me-2" type="submit"
                    style="display: inline !important;">
                    <u> Manage Roster</u>
                </button>
            </a>
        @endif
        <a
            href="{{ route('participant.event.view', ['id' => $id]) }}">
            <button class="oceans-gaming-default-button oceans-gaming-gray-button ms-2 me-2" type="submit" style="display: inline !important;">
                View Event
            </button>
        </a>
        <a href={{ route('participant.home.view')}}>
            <button class="btn btn-link ms-0 me-2" type="submit" style="display: inline !important;">
                Home Screen
            </button>
        </a>
    </div>

<div class="tabs">
    <button id="CurrentMembersBtn" class="tab-button inner-tab tab-button-active"
        onclick="showTab(event, 'CurrentMembers', 'inner-tab')">Current
        Members
    </button>
    <button id="PendingMembersBtn" class="tab-button inner-tab" onclick="showTab(event, 'PendingMembers', 'inner-tab')">
        Pending Members
    </button>
    <button id="NewMembersBtn" class="tab-button inner-tab" onclick="showTab(event, 'NewMembers', 'inner-tab')">New
        Members
    </button>
</div>
<br>
<div class="tab-content inner-tab" id="CurrentMembers">
    <p class="text-center mx-auto mt-2">Team {{ $selectTeam->teamName }} has
        {{ $teamMembersProcessed['accepted']['count'] }} accepted members
    </p>
    <div class="cont mt-3 pt-3">
        <table class="member-table">
            <tbody class="accepted-member-table">
                @if ($teamMembersProcessed['accepted']['count'] != 0)
                    @foreach ($teamMembersProcessed['accepted']['members'] as $member)
                        <tr class="st" id="tr-{{ $member->id }}">
                            <td class="colorless-col">
                                <svg    
                                    onclick="redirectToProfilePage({{$member->user_id}});"
                                    class="gear-icon-btn" xmlns="http://www.w3.org/2000/svg" width="20"
                                    height="20" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                    <path
                                        d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                </svg>
                            <td>
                            <td class="coloured-cell px-3">
                                <div 
                                    onclick="deleteCaptain({{ $member->id }}, {{ $selectTeam->id }})"
                                    class="player-info cursor-pointer">
                                    @if ($captain && $member->id == $captain->team_member_id)
                                        <div class="player-image"> </div>
                                    @endif
                                    <span>{{ $member->user->name }}</span>
                                </div>
                            </td>
                            <td class="flag-cell coloured-cell px-3">
                                <span>{{ $member->user->email }}</span>
                            </td>
                            <td class="flag-cell coloured-cell px-3">
                                <img class="nationality-flag" src="{{ asset('/assets/images/china.png') }}"
                                    alt="User's flag">
                            </td>
                            <td class="coloured-cell px-3">
                                Accepted {{ is_null($member->updated_at) ? '' : Carbon::parse($member->updated_at)->diffForHumans() }}
                            </td>
                            <td>
                                @if ($user->id == $selectTeam->creator_id)
                                    <button id="remove-{{ $member->id }}" class="gear-icon-btn"
                                        onclick="disapproveMember({{ $member->id }})">
                                    ✘
                                    </button>
                                @endif
                            </td>
                            <td>
                                @if (!$captain || $member->id != $captain->team_member_id)
                                    <button id="captain-{{ $member->id }}" class="gear-icon-btn invisible-until-hover"
                                        onclick="captainMember({{ $member->id }}, {{ $selectTeam->id }})">
                                        <img height="30" width="30"
                                            src="{{ asset('assets/images/participants/crown-straight.png') }}">
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
<div class="tab-content inner-tab d-none" id="PendingMembers" data-type="member" style="text-align: center;">
    <p class="text-center mx-auto mt-2">Team {{ $selectTeam->teamName }} has
        {{ $teamMembersProcessed['pending']['count'] }} pending,
        {{ $teamMembersProcessed['invited']['count'] }} invited and
        and {{ $teamMembersProcessed['rejected']['count'] }} rejected members
    </p>
    <div class="cont mt-3 pt-3">
        <table class="member-table">
            <tbody class="pending-member-table">
                @if ($teamMembersProcessed['pending']['count'] != 0 || 
                        $teamMembersProcessed['rejected']['count'] != 0 || 
                        $teamMembersProcessed['invited']['count'] != 0
                    )
                    @foreach ($teamMembersProcessed['pending']['members'] as $member)
                        <tr class="st" id="tr-{{ $member->id }}">
                            <td class="colorless-col">
                                <svg 
                                    onclick="redirectToProfilePage({{$member->user_id}});"
                                    class="gear-icon-btn" xmlns="http://www.w3.org/2000/svg" width="20"
                                    height="20" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                    <path
                                        d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                </svg>
                            <td>
                            <td class="coloured-cell px-3">
                                <div class="player-info">
                                    <span>{{ $member->user->name }}</span>
                                </div>
                            </td>
                             <td class="flag-cell coloured-cell px-3">
                                <span>{{ $member->user->email }}</span>
                            </td>
                            <td class="flag-cell coloured-cell px-3">
                                <img class="nationality-flag" src="{{ asset('/assets/images/china.png') }}"
                                    alt="User's flag">
                            </td>
                            <td class="coloured-cell px-3">
                                Pending {{ is_null($member->updated_at) ? '' : Carbon::parse($member->updated_at)->diffForHumans() }}
                            </td>
                            <td>
                                @if ($user->id == $selectTeam->creator_id)
                                    <button id="add-{{ '$member->id' }}" class="gear-icon-btn"
                                        onclick="approveMember({{ $member->id }})">
                                        ✔
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @foreach ($teamMembersProcessed['rejected']['members'] as $member)
                        <tr class="st" id="tr-{{ $member->id }}">
                            <td class="colorless-col">
                                <svg 
                                    onclick="redirectToProfilePage({{$member->user_id}});"
                                    class="gear-icon-btn" xmlns="http://www.w3.org/2000/svg" width="20"
                                    height="20" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                    <path
                                        d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                </svg>
                            <td>
                            <td class="coloured-cell px-3">
                                <div class="player-info">
                                    <span>{{ $member->user->name }}</span>
                                </div>
                            </td>
                             <td class="flag-cell coloured-cell px-3">
                                <span>{{ $member->user->email }}</span>
                            </td>
                            <td class="flag-cell coloured-cell px-3">
                                <img class="nationality-flag" src="{{ asset('/assets/images/china.png') }}"
                                    alt="User's flag">
                            </td>
                            <td class="coloured-cell px-3">
                                Rejected {{ is_null($member->updated_at) ? '' : Carbon::parse($member->updated_at)->diffForHumans() }}
                            </td>
                            <td>
                                @if ($user->id == $selectTeam->creator_id && $member->rejector!='invitee')
                                    <button id="add-{{ '$member->id' }}" class="gear-icon-btn"
                                        onclick="approveMember({{ $member->id }})">
                                        ✔
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @foreach ($teamMembersProcessed['invited']['members'] as $member)
                        <tr class="st" id="tr-{{ $member->id }}">
                            <td class="colorless-col">
                                <svg
                                    onclick="redirectToProfilePage({{$member->user_id}});" 
                                    class="gear-icon-btn" xmlns="http://www.w3.org/2000/svg" width="20"
                                    height="20" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                    <path
                                        d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                </svg>
                            <td>
                            <td class="coloured-cell px-3">
                                <div class="player-info">
                                    <span>{{ $member->user->name }}</span>
                                </div>
                            </td>
                             <td class="flag-cell coloured-cell px-3">
                                <span>{{ $member->user->email }}</span>
                            </td>
                            <td class="flag-cell coloured-cell px-3">
                                <img class="nationality-flag" src="{{ asset('/assets/images/china.png') }}"
                                    alt="User's flag">
                            </td>
                            <td class="coloured-cell px-3">
                                Invited {{ is_null($member->updated_at) ? '' : Carbon::parse($member->updated_at)->diffForHumans() }}
                            </td>
                            <td>
                                @if ($user->id == $selectTeam->creator_id)
                                    <button id="deleteInviteMember-{{ '$member->user_id' }}" class="gear-icon-btn"
                                        onclick="deleteInviteMember({{ $member->id }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                            <path
                                                d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                            <path
                                                d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                        </svg>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
<div class="tab-content inner-tab d-none" id="NewMembers">

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
                    fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
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
                <input id="searchInput" onchange="handleSearch();" style="font-size: 15px;" class="nav__input"
                    type="text" id="" placeholder="Search for player name/ email">
            </div>
        </div>
    </div>
    <section class="featured-events scrolling-pagination">
        @include('Participant.MemberManagementLayout.MemberManagementScroll')
    </section>
    <div class="no-more-data d-none"></div>
</div>

<script></script>
