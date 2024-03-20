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
        <button id="PendingTeam" class="tab-button inner-tab"
            onclick="showTab(event, 'SentTeam', 'inner-tab')">
            Sent Team Requests
        </button>
        <button id="PrivateInvitationsBtn" class="tab-button inner-tab" onclick="showTab(event, 'PrivateInvitations', 'inner-tab')">New
            Members
        </button>
    </div>
    <br>
    <div class="tab-content inner-tab" id="InvitatedTeam">
        @if (isset($invitedTeamList[0]))
            @foreach ($invitedTeamList as $team)
                <a style="cursor:pointer;" href="/participant/team/{{ $team['id'] }}/manage">
                    <div class="wrapper">
                        <div class="team-section">
                            <div class="upload-container">
                                <div class="circle-container" style="cursor: pointer;">
                                    <div id="uploaded-image" class="uploaded-image"
                                        style="background-image: url({{ $team->teamBanner ? '/storage' . '/'. $team->teamBanner: '/assets/images/fnatic.jpg' }} );"
                                    ></div>
                                    </label>
                                </div>
                                <h3 class="team-name" id="team-name">{{ $team->teamName }}</h3>
                                <br>
                                <p>Total Members:
                                    {{ $membersCount[$team->id] }}
                                </p>
                                <div class"d-
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        @else
            <div class="wrapper">
                <div class="team-section">
                    <div class="upload-container">
                        <label for="image-upload" class="upload-label">
                            <div class="circle-container">
                                <div id="uploaded-image" class="uploaded-image"></div>
                            </div>
                        </label>
                    </div>
                    <h3 class="team-name" id="team-name">Not invited to any teams</h3>
                    <br>
                </div>
            </div>
        @endif
    </div>
    <div class="tab-content inner-tab d-none" id="SentTeam" data-type="member" style="text-align: center;">
        <p class="text-center mx-auto mt-2">
            View your sent team requests
        </p>
        <div class="cont mt-3 pt-3">
            <table class="member-table">
                <tbody class="pending-member-table">
                    @if (isset($pendingTeamList[0]))
                        @foreach ($pendingTeamList as $team)
                            <tr class="st" id="tr-{{ $team->id }}">
                                <td class="colorless-col">
                                    <svg class="gear-icon-btn" xmlns="http://www.w3.org/2000/svg" width="20"
                                        height="20" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                        <path
                                            d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                    </svg>
                                <td>
                                <td class="coloured-cell px-3">
                                    <div class="player-info">
                                        <span>{{ $team->teamName }}</span>
                                    </div>
                                </td>
                                <td class="flag-cell coloured-cell px-3">
                                    <img class="nationality-flag" src="{{ asset( $team->teamBanner ? '/storage' . '/'. $team->teamBanner: '/assets/images/fnatic.jpg' ) }}"
                                        alt="User's flag">
                                </td>
                                <td class="coloured-cell px-3">
                                    Pending
                                </td>
                                <td>
                                    @if ($user->id == $team->creator_id)
                                        <button id="add-{{ '$$team->members[0]->id' }}" class="gear-icon-btn"
                                            onclick="approveMember({{ $team->members[0]->id }})">
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
    <div class="tab-content inner-tab d-none" id="PrivateInvitations">
        
       
       
        <div class="no-more-data d-none"></div>
    </div>

    <script>
    
    
    </script>
