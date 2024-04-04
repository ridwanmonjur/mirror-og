<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roster Management</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>

<body>
    @include('CommonLayout.NavbarGoToSearchPage')
    @include('Participant.Layout.TeamHead')
    @php
        use Carbon\Carbon;
    @endphp
    <main class="main2">
        <div class="mb-4 text-success mx-auto text-center">
            You have joined this event successfully!
            
            <form class="d-inline" method="GET"
                action="{{ route('participant.event.view', ['id' => $id]) }}">
                <button class="oceans-gaming-default-button oceans-gaming-default-button-link ms-2 me-2" type="submit" style="display: inline !important;">
                    <u> View Event </u>
                </button>
            </form>
        </div>
        <div>
            <p class="text-center mx-auto">Team {{ $selectTeam->teamName }} has
                {{ $rosterMembers->count() }} accepted roster members 
                from {{$teamMembers->count()}} available team member(s).
            </p>
            @if (isset($teamMembers[0]))
                <table class="member-table">
                    <br>
                    <tbody>
                        @foreach ($teamMembers as $member)
                            @php
                                if (isset($rosterMembersKeyedByMemberId[$member->id])) {
                                    $isRoster = true;
                                    $roster = $rosterMembersKeyedByMemberId[$member->id];
                                } else {
                                    $isRoster = false;
                                    $roster = null;
                                }
                            @endphp
                            <tr class="st" id="tr-{{$member->id}}">
                                <td class="coloured-cell" style="width: 25px;">
                                    <span class="player-info" style="cursor: pointer;">
                                        @if ($captain && $member->id == $captain->team_member_id)
                                            <div style="cursor: pointer;" 
                                                class="player-image"
                                                onclick="deleteCaptain({{$member->id}})"
                                            > </div>
                                        @endif
                                    </span>
                                </td>
                                <td class="coloured-cell">
                                    <span>{{ $member->user->name }}</span>
                                </td>
                                <td class="coloured-cell">
                                    <span>{{ $member->user->email }}</span>
                                </td>
                                <td class="flag-cell coloured-cell">
                                    <img class="nationality-flag" src="{{ asset('/assets/images/china.png') }}"
                                        alt="User's flag">
                                </td>
                                <td class="flag-cell coloured-cell">
                                    @if ($isRoster)
                                        Joined  {{is_null($roster->created_at) ? "": Carbon::parse($roster->created_at)->diffForHumans()}}
                                    @else
                                        Not in roster
                                    @endif
                                </td>
                                <td class="colorless-col">
                                    @if (!$isRoster)
                                        <button id="add-{{$member->id}}" class="gear-icon-btn" onclick="approveMember({{$member->id}})">
                                            ✔
                                        </button>
                                    @else
                                        <button id="remove-{{$member->id}}" class="gear-icon-btn" onclick="disapproveMember({{$member->id}})">
                                        ✘
                                        </button>
                                    @endif
                                </td>
                                <td  class="colorless-col">
                                    @if (!$captain || $member->id != $captain->team_member_id)
                                        <button id="captain-{{$member->id}}" class="gear-icon-btn invisible-until-hover ml-2" onclick="capatainMember({{$member->id}})">
                                            <img height="30" width="30" src="{{asset('assets/images/participants/crown-straight.png')}}">
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-danger mx-auto">
                    You need accepted members to continue
                </p>
            @endif
        </div>

    </main>

    <script src="{{ asset('/assets/js/fetch/fetch.js') }}"></script>
    @include('CommonLayout.BootstrapV5Js')
    <script src="{{ asset('/assets/js/models/DialogForMember.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.6/dist/sweetalert2.all.min.js"></script>
    @include('CommonLayout.Toast')
    @include('CommonLayout.Dialog')
    <script>
        let dialogForMember = new DialogForMember();
        function loadToast() {
            let currentUrl = window.location.href;
            let urlParams = new URLSearchParams(window.location.search);
            let successValue = urlParams.get('success');

            if (successValue == 'true') {
                Toast.fire({
                    icon: 'success',
                    text: "Successfully updated user."
                })

                document.querySelector('.main2').scrollIntoView();
            }
        }

        loadToast();

        let actionMap = {
            'approve': approveMemberAction,
            'disapprove': disapproveMemberAction,
            'captain': capatainMemberAction,
            'deleteCaptain': deleteCaptainAction,
        };

        function reloadUrl(currentUrl, buttonName) {
            if (currentUrl.includes('?')) {
                currentUrl = currentUrl.split('?')[0];
            } 

            currentUrl += `?tab=${buttonName}&success=true`;
            console.log({currentUrl})
            window.location.replace(currentUrl);
        }

        function toastError(message, error = null) {
            console.error(error)
            Toast.fire({
                icon: 'error',
                text: message
            });
        }

        function takeYesAction() {
            console.log({
                memberId: dialogForMember.getMemberId(),
                action: dialogForMember.getActionName()
            })

            const actionFunction = actionMap[dialogForMember.getActionName()];
            if (actionFunction) {
                actionFunction();
            } else {
                Toast.fire({
                    icon: 'error',
                    text: "No action found."
                })
            }
        } 

        function takeNoAction() {
            dialogForMember.reset();
        }

        function approveMember(memberId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setActionName('approve')
            dialogOpen('Continue with approval?', takeYesAction, takeNoAction)
        }

           function disapproveMember(memberId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setActionName('disapprove')
            dialogOpen('Continue with disapproval?', takeYesAction, takeNoAction)
        }

        function capatainMember(memberId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setActionName('captain')
            dialogOpen('Are you sure you want to this user captain?', takeYesAction, takeNoAction)
        }

        function deleteCaptain(memberId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setActionName('deleteCaptain')
            dialogOpen('Are you sure you want to remove this user from captain?', takeYesAction, takeNoAction)
        }

        function approveMemberAction() {
            const memberId = dialogForMember.getMemberId();
            const url = "{{ route('participant.roster.approve') }}";
            console.log({
                memberId: dialogForMember.getMemberId(),
                action: dialogForMember.getActionName()
            });

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.roster.manage', ['id' => $joinEvent->event_details_id, 'teamId' => $selectTeam->id] ) }}";
                        reloadUrl(currentUrl, 'CurrentMembersBtn');
                    } else {
                        toastError(responseData.message);
                    }
                },
                function(error) {
                    toastError('Error making captain.', error);
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        'user_id' : {{ $user->id }},
                        'join_events_id': {{ $joinEvent->id }},
                        'team_member_id': memberId
                    })
                }
            );
        }

        async function disapproveMemberAction() {
            const memberId = dialogForMember.getMemberId();
            const url = "{{ route('participant.roster.disapprove') }}";
            console.log({
                memberId: dialogForMember.getMemberId(),
                action: dialogForMember.getActionName()
            });

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.roster.manage', ['id' => $joinEvent->event_details_id, 'teamId' => $selectTeam->id] ) }}";
                        reloadUrl(currentUrl, 'PendingMembersBtn');
                    } else {
                        toastError(responseData.message)
                    }
                },
                function(error) {
                    toastError('Error disapproving member.', error);
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }, 
                    body: JSON.stringify({
                        'teams_id' : {{ $selectTeam->id }},
                        'user_id' : {{ $user->id }},
                        'join_events_id': {{ $joinEvent->id }},
                        'team_member_id': memberId
                    })
                }
            );
        }

        async function capatainMemberAction() {
            const memberId = dialogForMember.getMemberId();
            const url = "{{ route('participant.roster.captain') }}"
            console.log({
                memberId: dialogForMember.getMemberId(),
                action: dialogForMember.getActionName()
            });

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.roster.manage', ['id' => $joinEvent->event_details_id, 'teamId' => $selectTeam->id] ) }}";
                        reloadUrl(currentUrl, 'CurrentMembersBtn');
                    } else {
                        toastError(responseData.message);
                    }
                },
                function(error) {
                    toastError('Error making captain.', error);
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        'teams_id' : {{ $selectTeam->id }},
                        'join_events_id': {{ $joinEvent->id }},
                        'team_member_id': memberId
                    })
                }
            );
        }

        async function deleteCaptainAction() {
            const memberId = dialogForMember.getMemberId();
            const teamId = dialogForMember.getTeamId();
            const url = "{{ route('participant.roster.deleteCaptain') }}"
            console.log({
                memberId: dialogForMember.getMemberId(),
                action: dialogForMember.getActionName()
            });

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.roster.manage', ['id' => $joinEvent->event_details_id, 'teamId' => $selectTeam->id] ) }}";
                        reloadUrl(currentUrl, 'PendingMembersBtn');
                    } else {
                       toastError(responseData.message);
                    }
                },
                function(error) {
                    toastError('Error making captain.', error);
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        'teams_id' : {{ $selectTeam->id }},
                        'join_events_id': {{ $joinEvent->id }},
                        'team_member_id': memberId
                    })
                }
            );
        }

        function redirectToProfilePage(userId) {
            window.location.href = "{{route('participant.profile.view', ['id' => ':id']) }}"
                .replace(':id', userId);
        }
    </script>
</body>
