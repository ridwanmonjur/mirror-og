<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamList.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @include('__CommonPartials.HeadIcon')
</head>

<body>
    @include('googletagmanager::body')
    @include('__CommonPartials.NavbarGoToSearchPage')
    @php
        use Carbon\Carbon;
    @endphp
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
    <input type="hidden" id="memberUpdateUrl" value="{{ route('participant.member.update', ['id' => ':id']) }}">
    <input type="hidden" id="memberDeleteInviteUrl" value="{{ route('participant.member.deleteInvite', ['id' => ':id']) }}">
    <input type="hidden" id="requestViewUrl" value="{{ route('participant.request.view') }}">
    <input type="hidden" id="publicParticipantViewUrl" value="{{ route('public.participant.view', ['id' => ':id']) }}">
    <input type="hidden" id="teamManageUrl" value="{{ route('participant.team.manage', ['id' => ':id']) }}">
    <main >
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
        <div class="tab-content pb-4 px-5 inner-tab" id="InvitatedTeam">
            <h5 class="text-center mx-auto mt-2">
                Teams requesting you to join them.
            </h5>
            @if (isset($invitedTeamAndMemberList[0]))
                @foreach ($invitedTeamAndMemberList as $teamAndMember)
                    <div class="wrapper">
                        <div class="team-section">
                            <div class="upload-container">
                                <div class="circle-container" style="cursor: pointer;">
                                    <img
                                        onerror="this.onerror=null;this.src='/assets/images/404.png';"
                                        id="uploaded-image" class="uploaded-image" 
                                        src="{{'/storage' . '/' . $teamAndMember->teamBanner }}">
                                    </label>
                                </div>
                                <h3 class="team-name" id="team-name">{{ $teamAndMember->teamName }}</h3>
                                <i> {{ $teamAndMember->status == 'pending' ? 'Requested' : 'Rejected' }}  {{ is_null($teamAndMember->updated_at) ? '' : Carbon::parse($teamAndMember->updated_at)->diffForHumans() }} </i>
                                <br>
                                <p>Total Members:
                                    @if (isset($membersCount[$teamAndMember->id]))
                                        {{ $membersCount[$teamAndMember->id] }}
                                    @else {{ 0 }}
                                    @endif
                                </p>
                                <div class="d-flex justify-content-between">
                                    <div class="">
                                        <button class="btn btn-link"
                                            onclick="redirectToTeamPage({{ $teamAndMember->team_id }});"
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
                                    <div class="">
                                        <button id="add-{{ '$teamAndMember->id' }}" class="btn btn-link"
                                            onclick="approveTeam({{ $teamAndMember->id }})">
                                            ✔ Approve
                                        </button>
                                    </div>
                                    @if ($teamAndMember->status == 'pending')
                                        <div class="">
                                            <button id="remove-{{ $teamAndMember->id }}" class="btn btn-link"
                                                onclick="disapproveTeam({{ $teamAndMember->id }})">
                                                ✘ Reject
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="wrapper mx-auto">
                    <div class="team-section">
                        <br>
                        <img                       
                            src="{{asset('assets/images/animation/empty-exclamation.gif') }}"
                            width="150"
                            height="150"
                        >
                        <h3 class="team-name text-center" id="team-name">Not invited to any teams</h3>
                        <br>
                    </div>
                </div>
            @endif
        </div>
        <div class="tab-content pb-4 px-5 inner-tab d-none" id="SentTeam" data-type="member" style="text-align: center;">
            <h5 class="text-center mx-auto mt-2">
                View your sent team requests.
            </h5>
            <div class="mt-3 pt-3 tab-size">
                @if (isset($pendingTeamAndMemberList[0]))
                    <table class="member-table responsive  mx-auto">
                        <caption>Table Caption</caption>
                        <thead class="d-lg-none">
                            <tr>
                                <th scope="col">Link</th>
                                <th scope="col">Name</th>
                                <th scope="col">Description</th>
                                <th scope="col">Date</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
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
                                    </td>
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
                                            onclick="withdrawInviteMember({{ $teamAndMember->id }})">
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
                    <div>
                        <img                       
                            src="{{asset('assets/images/animation/empty-exclamation.gif') }}"
                            width="150"
                            height="150"
                            class="mx-auto d-block"
                        >
                    </div>
                    <p class="text-center mx-auto mt-3"> No team requested currently by you! </p>
                @endif
            </div>
        </div>
        <div class="tab-content pb-4 px-5 inner-tab d-none" id="PrivateInvitations">
            <h5 class="text-center mx-auto mt-2">
                View all events you have been invited
            </h5>
            <div class="mt-3 pt-3">
                @if (isset($invitedEventsList[0]))
                    @foreach ($invitedEventsList as $invitation)
                        <div class="d-block position-relative text-left coloured-cell mx-auto py-3 px-3 mx-3 mb-5 w-75"
                            id="div-{{ $invitation->id }}">
                            <div class="position-absolute d-flex justify-content-center w-100" style="top: -30px">
                                <img src="{{ bladeImageNull($invitation->event?->tier?->tierIcon) }}" height="60"
                                    width="80">
                            </div>
                            <div class="d-inline pe-4">
                                <img src="{{ bladeImageNull($invitation->event?->game?->gameIcon) }}" height="40"
                                    width="40" class="object-fit-cover me-3">
                                You have been invited to event
                                <span>"{{ $invitation->event?->eventName }}"</span>
                                by {{ $invitation->event?->user?->name }}
                                {{ is_null($invitation->updated_at) ? '' : Carbon::parse($invitation->updated_at)->diffForHumans()}}.
                                <button 
                                    onclick="redirectToProfilePage({{ $invitation->event?->id }});"    
                                    class="btn btn-link text-left ms-3 d-inline"
                                >
                                <u>View event</u>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div>
                        <img                       
                            src="{{asset('assets/images/animation/empty-exclamation.gif') }}"
                            width="150"
                            height="150"
                            class="mx-auto d-block"
                        >
                    </div>
                    <p class="text-center mx-auto mt-3"> No event invites requested currently! </p>
                @endif
            </div>
        </div>
    </main>

    <script src="{{ asset('/assets/js/organizer/DialogForMember.js') }}"></script>
    <script src="{{ asset('/assets/js/participant/ParticipantRequest.js') }}"></script>

</body>
