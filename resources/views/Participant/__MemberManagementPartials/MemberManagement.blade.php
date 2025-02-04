@php
    use Carbon\Carbon;
    $isRedirect = (isset($redirect) && $redirect);
@endphp

<div class="tabs">
    <button id="CurrentMembersBtn" class="tab-button inner-tab tab-button-active"
        onclick="showTab(event, 'CurrentMembers', 'inner-tab')">Current Members
    </button>
    <button id="PendingMembersBtn" class="tab-button inner-tab" onclick="showTab(event, 'PendingMembers', 'inner-tab')">
        Pending Members
    </button>
    <button id="NewMembersBtn" class="tab-button inner-tab" onclick="showTab(event, 'NewMembers', 'inner-tab')">New
        Members
    </button>
</div>
<br>
<div class="tab-content pb-4 inner-tab" id="CurrentMembers">
    <p class="text-center mx-auto mt-2">Team {{ $selectTeam->teamName }} has
        {{ $teamMembersProcessed['accepted']['count'] }} present member{{bladePluralPrefix($teamMembersProcessed['accepted']['count'])}}
        and {{ $teamMembersProcessed['left']['count'] }} past member{{bladePluralPrefix($teamMembersProcessed['left']['count'])}}
    </p>
    <div class="cont mt-3 pt-3 tab-size">
        <table class=" member-table responsive ">
            <tbody class="accepted-member-table">
                @if ($teamMembersProcessed['accepted']['count'] != 0)
                    <tr class="st">
                        <td> </td>
                        <td> Present Members </td>
                    </tr>
                    @foreach ($teamMembersProcessed['accepted']['members'] as $member)
                        <tr class="st invisible-until-hover-parent" id="tr-{{ $member->id }}">
                            @include('Participant.__MemberManagementPartials.MemberManagementColumns', ['member' => $member])
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
                            <td >
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
                @if ($teamMembersProcessed['left']['count'] != 0)
                    <tr class="st">
                        <td> </td>
                        <td class="pt-3"> Past Members </td>
                    </tr>
                    @foreach ($teamMembersProcessed['left']['members'] as $member)
                        <tr class="st" id="tr-{{ $member->id }}">
                            @include('Participant.__MemberManagementPartials.MemberManagementColumns', ['member' => $member])
                            <td class="coloured-cell px-3">
                                {{ $member->actor == 'team' ? 'Removed' : 'Left' }} {{ is_null($member->updated_at) ? '' : Carbon::parse($member->updated_at)->diffForHumans() }}
                            </td>
                            <td>
                                 @if ($user->id == $selectTeam->creator_id && $member->actor == 'team')
                                    <button id="add-{{ '$member->id' }}" class="gear-icon-btn"
                                        onclick="approveMember({{ $member->id }})">
                                        ✔
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
<div class="tab-content pb-4  inner-tab d-none" id="PendingMembers" data-type="member" style="text-align: center;">
   
    <p class="text-center mx-auto mt-2">Team {{ $selectTeam->teamName }} has
        {{ $teamMembersProcessed['pending']['count'] }} invited and
        and {{ $teamMembersProcessed['rejected']['count'] }} rejected member{{bladePluralPrefix($teamMembersProcessed['rejected']['count'])}}
    </p>
    <div class="cont mt-3 pt-3 tab-size">
        <table class="member-table responsive ">
            <tbody class="pending-member-table">
                @if ($teamMembersProcessed['pending']['count'] != 0 || 
                        $teamMembersProcessed['rejected']['count'] != 0 
                    )
                    @if ($teamMembersProcessed['pending']['count'] != 0)
                        <tr class="st">
                            <td> </td>
                            <td> Invited Members </td>
                        </tr>
                        @foreach ($teamMembersProcessed['pending']['members'] as $member)
                            <tr class="st" id="tr-{{ $member->id }}">
                                @include('Participant.__MemberManagementPartials.MemberManagementColumns', ['member' => $member])
                                <td class="coloured-cell px-3">
                                    @if ($member->actor == "user")
                                        Player requested to join
                                    @else
                                        Sent player invitation
                                    @endif
                                    {{ is_null($member->updated_at) ? '' : Carbon::parse($member->updated_at)->diffForHumans() }}
                                </td>
                                <td>
                                    @if ($user->id == $selectTeam->creator_id && $member->actor == 'user')
                                        <button id="add-{{ '$member->id' }}" class="gear-icon-btn"
                                            onclick="approveMember({{ $member->id }})">
                                            <span class="ps-1"> ✔ </span>
                                        </button>
                                        <button id="add-{{ '$member->id' }}" class="gear-icon-btn"
                                            onclick="rejectMember({{ $member->id }})">
                                            <span class="ps-1"> ✘ </span>
                                        </button>
                                    @endif
                               
                                    @if ($user->id == $selectTeam->creator_id && $member->actor == 'team' )
                                        <button id="withdrawInviteMember-{{ '$member->user_id' }}" class="gear-icon-btn"
                                            onclick="withdrawInviteMember({{ $member->id }})">
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
                    @if ($teamMembersProcessed['rejected']['count'] != 0)
                        <tr class="st">
                            <td> </td>
                            <td class="pt-3"> Rejected Members </td>
                        </tr>
                        @foreach ($teamMembersProcessed['rejected']['members'] as $member)
                            <tr class="st" id="tr-{{ $member->id }}">
                                @include('Participant.__MemberManagementPartials.MemberManagementColumns', ['member' => $member])
                                <td class="coloured-cell px-3">
                                    @if ($member->actor == "user")
                                        Player rejected the request
                                    @else
                                        Rejected the player's request
                                    @endif
                                    {{ is_null($member->updated_at) ? '' : Carbon::parse($member->updated_at)->diffForHumans() }}
                                </td>
                                <td>
                                    @if ($user->id == $selectTeam->creator_id && $member->actor != 'user' )
                                        <button id="add-{{ '$member->id' }}" class="gear-icon-btn"
                                            onclick="approveMember({{ $member->id }})">
                                            <span class="ps-1"> ✔ </span>
                                        </button>
                                    @endif
                               
                                </td>
                            </tr>
                        @endforeach 
                    @endif               
                @endif
            </tbody>
        </table>
   
    </div>
</div>
<div class="tab-content pb-4 inner-tab mx-auto d-none" id="NewMembers">
    @if (isset($redirect) && $redirect) 
        <div class="text-center">
            <h5><u>New Members</u></h5>
            <p>Add new members to your team</p>
        </div>
    @endif
    <form id="newMembersForm">
         <input type="hidden" name="sortKeys" id="sortKeys" value="">
        <input type="hidden" name="sortType" id="sortType" value="">
        <div class="tab-size d-flex justify-content-between align-items-center flex-wrap tab-size mt-0">
            <div class="mb-2">
                <span class="cursor-pointer me-4" onclick="
                        document.getElementById('filter-option').classList.remove('d-none'); 
                        document.getElementById('sort-option').classList.add('d-none');
                        document.getElementById('filter-search-results').classList.remove('d-none');
                    ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-filter">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3">
                        </polygon>
                    </svg>
                    <span> Filter </span>
                </span>
                <span class="cursor-pointer" onclick="
                        document.getElementById('filter-option').classList.add('d-none'); 
                        document.getElementById('sort-option').classList.remove('d-none');
                        document.getElementById('filter-search-results').classList.remove('d-none');
                    ">
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
            <div class="mb-2">
                <div class="d-flex justify-content-end">
                    <input name="search" style="width: min(90vw, 350px); font-size: 1rem;" id="searchInput" class="rounded-pill px-4 form-control"
                        type="text" placeholder="Search for player name/ email...">
                    <button type="button" class="btn btn-primary text-light px-2 ms-2 border-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather feather-search cursor-pointer"
                        >
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="tab-size px-0">
            <div id="filter-option" class="mx-0 px-0 d-none mb-2">
                <div class="d-flex justify-content-start">
                    <div class="dropdown me-3">
                        <button
                            class="ps-0 pe-3 py-1 py-2 button-design-removed" type="button" id="dropdownFilterType"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span>Age </span>
                            <span class="dropbtn-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-chevron-down">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                        <div
                            onclick="event.stopPropagation();" 
                            class="dropdown-menu px-3" aria-labelledby="dropdownFilterTier"
                        >
                            <p class="mb-1">Choose a date of birth to filter age</p>
                            <input  type="date" class="form-control" name="birthDate">
                            <button id="birthDateResetButton" type="button" class="my-2 rounded-pill btn btn-sm btn-primary text-light" onclick="
                                resetInput('birthDate');
                            "> Reset </button>
                        </div>
                    </div>

                    <div class="dropdown me-3">
                        <button class="ps-0 pe-3 py-2 button-design-removed" type="button" id="dropdownFilterTier"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span>Region </span>
                            <span class="dropbtn-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-chevron-down">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                        <div
                            onclick="event.stopPropagation;"; 
                            class="dropdown-menu px-0 py-1" aria-labelledby="dropdownFilterTier"
                        >
                            <div class="px-3 py-1">
                                <p class="mb-1">Choose a country of origin</p>
                                {{-- <input id="select2-country2" type="checkbox" name="venue"> --}}
                                <select id="select2-country2" class="form-control form-select" name="region" style="width: 200px !important;">
                                    <option value=""> </option>
                                </select>
                                <button id="regionResetButton" type="button" class="my-2 rounded-pill btn btn-sm btn-primary text-light" onclick="
                                    resetInput('region');
                                "> Reset </button>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown me-3">
                        <button class="ps-0 pe-3 py-2 button-design-removed" type="button" id="dropdownFilterTier"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span>Status</span>
                            <span class="dropbtn-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-chevron-down">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                        <div
                            onclick="event.stopPropagation();" 
                            class="dropdown-menu px-0 py-1" aria-labelledby="dropdownFilterTier"
                        >
                            @foreach([
                                ['title' => 'Team member', 'value' => 'accepted'],
                                ['title' => 'Pending invite', 'value' => 'pending'],
                                ['title' => 'Rejected invite', 'value' => 'rejected'],
                                ['title' => 'Left team', 'value' => 'left'],
                            ] as $status)
                                <div class="px-3 py-1" style="width: 200px;">
                                    <input
                                        type="checkbox" checked name="status" value="{{$status['value']}}" class="form-check-input"
                                    >
                                    <label for="status">{{$status['title']}}</label>
                                </div>
                            @endforeach
                        </div>
                    </div> 
                </div>
            </div>
            <div id="sort-option" class="mx-0 px-0 mb-3 d-none">
                <div class="ddropdown dropdown-click-outside d-inline-block">
                    <span class="sort-icon-list" onclick="changeSortType()">
                        {{-- Ascending --}}
                        <svg data-value="asc-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="d-none cursor-pointer gear-icon-button bi bi-sort-alpha-up" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M10.082 5.629 9.664 7H8.598l1.789-5.332h1.234L13.402 7h-1.12l-.419-1.371zm1.57-.785L11 2.687h-.047l-.652 2.157z"/>
                        <path d="M12.96 14H9.028v-.691l2.579-3.72v-.054H9.098v-.867h3.785v.691l-2.567 3.72v.054h2.645zm-8.46-.5a.5.5 0 0 1-1 0V3.707L2.354 4.854a.5.5 0 1 1-.708-.708l2-1.999.007-.007a.5.5 0 0 1 .7.006l2 2a.5.5 0 1 1-.707.708L4.5 3.707z"/>
                        </svg>
                        {{-- Descending --}}
                        <svg data-value="desc-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="d-none cursor-pointer gear-icon-button bi bi-sort-alpha-down" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10.082 5.629 9.664 7H8.598l1.789-5.332h1.234L13.402 7h-1.12l-.419-1.371zm1.57-.785L11 2.687h-.047l-.652 2.157z"/>
                            <path d="M12.96 14H9.028v-.691l2.579-3.72v-.054H9.098v-.867h3.785v.691l-2.567 3.72v.054h2.645zM4.5 2.5a.5.5 0 0 0-1 0v9.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L4.5 12.293z"/>
                        </svg>
                    </span>
                    <button 
                        class="dropbtn py-1 px-2 me-3" 
                        type="button" id="dropdownSortButton" 
                        style="width: 150px; display: inline-block;"
                        data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span id="sortByTitleId">Sort by:</span>
                        <span class="dropbtn-arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-chevron-down">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </span>
                    </button>
                    <div
                        onclick="event.stopPropagation();" 
                        class="dropdown-menu px-3 ms-3" aria-labelledby="dropdownSortButton"
                    >
                        <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1" onclick="setSortForFetch('recent');">
                            <label class="me-3 cursor-pointer" for="recent">Recent</label>
                        </div>
                        {{-- <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1" onclick="setSortForFetch('birthDate');">
                            <label class="me-3 cursor-pointer" for="age">Age</label>
                        </div>
                        <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1" onclick="setSortForFetch('region');">
                            <label class="me-3 cursor-pointer" for="region">Region</label>
                        </div> --}}
                        <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1" onclick="setSortForFetch('name');">
                            <label class="me-3 cursor-pointer" for="prize">Name</label>
                        </div>
                        <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1" onclick="setSortForFetch('created_at');">
                            <label class="me-3 cursor-pointer" for="prize">Joined</label>
                        </div>
                        <button id="sortKeysResetButton" type="button" class="my-2 ms-3 rounded-pill btn btn-sm btn-primary text-light"
                            onclick="
                            resetInput('sortKeys');
                        ">
                            Reset
                        </button>
                    </div>
                </div>
            </div> 

            <div id="filter-search-results" class="d-none pb-2">
                <span class="me-5  pb-2">
                    <small class="me-4">Filter/ Sort: </small>
                    <span class="">
                        <small data-form-parent="default-filter" class="me-2">
                            <small class="btn btn-secondary text-light rounded-pill px-2 py-0">
                                Default
                            </small>
                        </small>
                        <small data-form-parent="birthDate" class="me-2">  
                        </small>
                        <small data-form-parent="region" class="me-2">  
                        </small>
                        <small data-form-parent="status" class="me-2">  
                        </small>
                        <small data-form-parent="sortKeys">  
                        </small>
                    </span>
                    {{-- <small  id="default-sorts" class="btn btn-primary text-light px-2 py-0">Default</small> --}}
                </span> 
            </div>
        </div>
        <input type="hidden" id ="teamId" value="{{$selectTeam->id}}">
        <input type="hidden" id ="membersUrl" value="{{route('user.teams.index', ['type' => 'team'])}}">
    </form>

    <section class="featured-events scrolling-pagination tab-size">
        <table class="member-table responsive " id="member-table-body">
            <tbody>
            </tbody>
        </table>

        <div class="tab-size mt-4"> 
            <ul class="pagination cursor-pointer py-3" id="member-table-links">
            </ul>
        </div>
    </section>
</div>




