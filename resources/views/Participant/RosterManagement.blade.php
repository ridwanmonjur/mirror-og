<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roster Management</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.css">
    <link rel="stylesheet" href="{{ asset('/assets/css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>

<body>
    @include('CommonLayout.NavbarforParticipant')
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
                                        @if (isset($captain))
                                            @if ($captain && $member->id == $captain->team_member_id)
                                                <div class="player-image"> </div>
                                            @endif
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
                                        Joined {{Carbon::parse($roster->created_at)->diffForHumans()}}
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
                                        <button id="remove-{{$member->id}}" class="gear-icon-btn" onclick="disapproveMember('{{$member->id}}')">
                                        ✘
                                        </button>
                                    @endif
                                </td>
                                <td  class="colorless-col">
                                    @if (!$captain || $member->id != $captain->team_member_id)
                                        <button id="captain-{{$member->id}}" class="gear-icon-btn invisible-until-hover ml-2" onclick="capatainMember('{{$member->id}}')">
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

    @include('CommonLayout.BootstrapV5Js')
    <script src="{{ asset('/assets/js/models/DialogForMember.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.6/dist/sweetalert2.all.min.js"></script>
    @include('CommonLayout.Toast')
    @include('CommonLayout.Dialog')
    <script>
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

        function captainMember(memberId, teamId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setTeamId(teamId);
            dialogForMember.setActionName('captain')
            dialogOpen('Are you sure you want to this user captain?', takeYesAction, takeNoAction)
        }

        function deleteCaptain(memberId, teamId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setTeamId(teamId);
            dialogForMember.setActionName('deleteCaptain')
            dialogOpen('Are you sure you want to remove this user from captain?', takeYesAction, takeNoAction)
        }

        function disapproveMember(memberId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setActionName('disapprove')
            dialogOpen('Continue with disapproval?', takeYesAction, takeNoAction)
        }

        function approveMemberAction() {
            const memberId = dialogForMember.getMemberId();
            const url = "{{ route('participant.roster.approve', ['id' => ':id']) }}".replace(':id', memberId);
            console.log({
                memberId: dialogForMember.getMemberId(),
                action: dialogForMember.getActionName()
            });

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}";
                        reloadUrl(currentUrl, 'CurrentMembersBtn');
                    } else {
                        toastError(responseData.message);
                    }
                },
                function(error) {
                    toastError('Error making captain.', error);
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            );
        }

        async function disapproveMemberAction() {
            const memberId = dialogForMember.getMemberId();
            const url = "{{ route('participant.member.disapprove', ['id' => ':id']) }}".replace(':id', memberId);
            console.log({
                memberId: dialogForMember.getMemberId(),
                action: dialogForMember.getActionName()
            });

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}";
                        reloadUrl(currentUrl, 'PendingMembersBtn');
                    } else {
                        toastError(responseData.message)
                    }
                },
                function(error) {
                    toastError('Error disapproving member.', error);
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            );
        }

        async function capatainMemberAction() {
            const memberId = dialogForMember.getMemberId();
            const teamId = dialogForMember.getTeamId();
            const url = "{{ route('participant.member.captain', ['id' => ':id', 'memberId' => ':memberId']) }}"
                .replace(':memberId', memberId)
                .replace(':id', teamId);
            console.log({
                memberId: dialogForMember.getMemberId(),
                action: dialogForMember.getActionName()
            });

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}";
                        reloadUrl(currentUrl, 'CurrentMembersBtn');
                    } else {
                        toastError(responseData.message);
                    }
                },
                function(error) {
                    toastError('Error making captain.', error);
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            );
        }

        async function deleteCaptainAction() {
            const memberId = dialogForMember.getMemberId();
            const teamId = dialogForMember.getTeamId();
            const url = "{{ route('participant.member.deleteCaptain', ['id' => ':id', 'memberId' => ':memberId']) }}"
                .replace(':memberId', memberId)
                .replace(':id', teamId);
            console.log({
                memberId: dialogForMember.getMemberId(),
                action: dialogForMember.getActionName()
            });

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}";
                        reloadUrl(currentUrl, 'PendingMembersBtn');
                    } else {
                       toastError(responseData.message);
                    }
                },
                function(error) {
                    toastError('Error making captain.', error);
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            );
        }

        async function inviteMemberAction() {
            const memberId = dialogForMember.getMemberId();
            const teamId = dialogForMember.getTeamId();
            const url = "{{ route('participant.member.invite', ['id' => ':id', 'userId' => ':userId']) }}"
                .replace(':userId', memberId)
                .replace(':id', teamId);
            console.log({ memberId: dialogForMember.getMemberId(), action: dialogForMember.getActionName() });

            fetchData(
                url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}";
                        reloadUrl(currentUrl, 'PendingMembersBtn');
                    } else {
                       toastError(responseData.message);
                    }
                },
                function(error) {
                    toastError('Error inviting members.', error);
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            );
        }

        async function deleteInviteMemberAction() {
            const memberId = dialogForMember.getMemberId();
            const url = "{{ route('participant.member.deleteInvite', ['id' => ':id']) }}"
                .replace(':id', memberId);

            fetchData(
                url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}";
                        reloadUrl(currentUrl, 'PendingMembersBtn');
                    } else {
                        toastError(responseData.message);
                    }
                },
                function(error) {
                    toastError('Error deleting invite members.', error);
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            );
        }

        async function fetchParticipants(event) {
            let input = event.currentTarget;
            let currentUrl = "{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}";

            fetchData(
                url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}";
                        reloadUrl(currentUrl, 'NewMembersBtn');
                        window.location.replace(currentUrl);
                    } else {
                        toastError(responseData.message);
                    }
                },
                function(error) {
                    toastError('Error fetching participants.', error);
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            );
        }

        const searchInputs = document.querySelectorAll('.search_box input');
        const memberTables = document.querySelectorAll('.member-table');

        searchInputs.forEach((searchInput, index) => {
            searchInput.addEventListener("input", function() {
                const searchTerm = searchInput.value.toLowerCase();
                const memberRows = memberTables[index].querySelectorAll('tbody tr');

                memberRows.forEach(row => {
                    const playerName = row.querySelector('.player-info span')
                        .textContent.toLowerCase();

                    if (playerName.includes(searchTerm)) {
                        row.style.display = 'table-row';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });

        function redirectToProfilePage(userId) {
            window.location.href = "{{route('participant.profile.view', ['id' => ':id']) }}"
                .replace(':id', userId);
        }
    </script>
</body>
