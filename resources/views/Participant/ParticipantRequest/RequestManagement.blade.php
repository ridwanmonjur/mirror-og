@php
    use Carbon\Carbon;
@endphp
<br> <br>
@if (isset($id))
    <div class="mb-2 text-success mx-auto text-center">
        You have joined this event successfully!
        @if ($selectTeam->creator_id == $user->id)
            <form class="d-inline" method="GET"
                action="{{ route('participant.roster.manage', ['id' => $id, 'teamId' => $selectTeam->id]) }}">
                <button class="oceans-gaming-default-button oceans-gaming-default-button-link ms-2 me-2" type="submit"
                    style="display: inline !important;">
                    <u> Manage Roster </u>
                </button>
            </form>
        @endif
    </div>
@endif

<div>
    <div class="tabs">
        <button id="InvitatedTeamBtn" class="tab-button inner-tab tab-button-active"
            onclick="showTab(event, 'InvitatedTeam', 'inner-tab')">
            Accept Team Requests
        </button>
        <button id="PendingTeamBtn" class="tab-button inner-tab" onclick="showTab(event, 'SentTeam', 'inner-tab')">
            Sent Team Requests
        </button>
        <button id="PrivateInvitationsBtn" class="tab-button inner-tab"
            onclick="showTab(event, 'PrivateInvitations', 'inner-tab')">
            Private invitations
        </button>
    </div>
    <br>
    <div class="tab-content inner-tab" id="InvitatedTeam">
        <p class="text-center mx-auto mt-2">
            Teams requesting you to join them.
        </p>
        @if (isset($invitedTeamAndMemberList[0]))
            @foreach ($invitedTeamAndMemberList as $teamAndMember)
                <div class="wrapper">
                    <div class="team-section">
                        <div class="upload-container">
                            <div class="circle-container" style="cursor: pointer;">
                                <div id="uploaded-image" class="uploaded-image"
                                    style="background-image: url({{ $teamAndMember->teamBanner ? '/storage' . '/' . $teamAndMember->teamBanner : '/assets/images/fnatic.jpg' }} );">
                                </div>
                                </label>
                            </div>
                            <h3 class="team-name" id="team-name">{{ $teamAndMember->teamName }}</h3>
                            <i> Requested {{ is_null($teamAndMember->updated_at) ? '' : Carbon::parse($teamAndMember->updated_at)->diffForHumans() }} </i>
                            <br>
                            <p>Total Members:
                                @if (isset($membersCount[$teamAndMember->id]))
                                    {{ $membersCount[$teamAndMember->id] }}
                                @else {{ 0 }}
                                @endif
                            </p>
                            <div class="d-flex justify-content-around">
                                <div class="px-5">
                                    <button class="btn btn-link gear-icon-btn"
                                        onclick="redirectToTeamPage({{ $teamAndMember->team_id }});"
                                        style="cursor:pointer; padding: 0; color: black; text-decoration: none;"
                                        >
                                        <svg 
                                            xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                            <path
                                                d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                        </svg>
                                        View
                                    </button>
                                </div>
                                <div class="px-5">
                                    <button id="add-{{ '$teamAndMember->id' }}" class="gear-icon-btn"
                                        onclick="approveTeam({{ $teamAndMember->id }})">
                                        ✔ Approve
                                    </button>
                                </div>
                                <div class="px-5">
                                    <button id="remove-{{ $teamAndMember->id }}" class="gear-icon-btn"
                                        onclick="disapproveTeam({{ $teamAndMember->id }})">
                                        ✘ Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="wrapper">
                <div class="team-section">
                    <br>
                    <img                       
                        src="{{asset('assets/images/animation/empty-exclamation.gif') }}"
                        width="150"
                        height="150"
                    >
                    <h3 class="team-name" id="team-name">Not invited to any teams</h3>
                    <br>
                </div>
            </div>
        @endif
    </div>
    <div class="tab-content inner-tab d-none" id="SentTeam" data-type="member" style="text-align: center;">
        <p class="text-center mx-auto mt-2">
            View your sent team requests. You may delete these requests if you don't approve of the team.
        </p>
        <div class="cont mt-3 pt-3">
            @if (isset($pendingTeamAndMemberList[0]))
                <table class="member-table">
                    <tbody class="pending-member-table">
                        @foreach ($pendingTeamAndMemberList as $teamAndMember)
                            <tr class="st" id="tr-{{ $teamAndMember->team_id }}">
                                <td class="colorless-col">
                                    <svg
                                        onclick="redirectToTeamPage({{ $teamAndMember->team_id }});" 
                                        class="gear-icon-btn" xmlns="http://www.w3.org/2000/svg" width="20"
                                        height="20" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                        <path
                                            d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                    </svg>
                                <td>
                                <td class="coloured-cell px-3">
                                    <div class="player-info">
                                        <span>{{ $teamAndMember->teamName }}</span>
                                    </div>
                                </td>
                                <td class="coloured-cell px-3">
                                    {{ $teamAndMember->teamDescription }}
                                </td>
                                <td class="coloured-cell px-3">
                                    Sent
                                    {{ is_null($teamAndMember->updated_at) ? '' : Carbon::parse($teamAndMember->updated_at)->diffForHumans() }}
                                </td>
                                <td>
                                    <button id="add-{{ '$teamAndMember->id' }}" class="gear-icon-btn"
                                        onclick="deleteInviteMember({{ $teamAndMember->id }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                            <path
                                                d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                            <path
                                                d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-center mx-auto"> No team requested currently by you! </p>
            @endif
        </div>
    </div>
    <div class="tab-content inner-tab d-none" id="PrivateInvitations">
        <p class="text-center mx-auto mt-2">
            View all events you have been invited
        </p>
        <div class="mt-3 pt-3">
            @if (isset($invitedEventsList[0]))
                @foreach ($invitedEventsList as $invitation)
                    <div class="d-block position-relative text-left coloured-cell mx-auto py-3 px-3 mx-3 mb-5 w-75"
                        id="div-{{ $invitation->id }}">
                        <div class="position-absolute d-flex justify-content-center w-100" style="top: -30px">
                            <img src="{{ bladeImageNull($invitation->event->tier->tierIcon) }}" height="60"
                                width="80">
                        </div>
                        <div class="d-inline pe-4">
                            <img src="{{ bladeImageNull($invitation->event->game->gameIcon) }}" height="60"
                                width="60">
                            You have been invited to event
                            <span>"{{ $invitation->event->eventName }}"</span>
                            by {{ $invitation->event->user->name }}
                            {{ is_null($invitation->updated_at) ? '' : Carbon::parse($invitation->updated_at)->diffForHumans()}}.
                            <button 
                                onclick="redirectToProfilePage({{ $invitation->event->id }});"    
                                class="btn btn-link text-left d-inline"
                            >
                               <u>View event</u>
                            </button>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-center mx-auto"> No event invites requested currently! </p>
            @endif
        </div>
    </div>
</div>
