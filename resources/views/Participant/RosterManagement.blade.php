<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roster Management</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/timeline.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/manage_team.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @include('__CommonPartials.HeadIcon')
</head>

<body
    @style(["min-height: 100vh;" => $isRedirect])
>
    @include('__CommonPartials.NavbarGoToSearchPage')
    <main 
        @style(["height: 95vh" => $isRedirect])
     class="main2">
        <input type="hidden" name="isRedirectInput" id="isRedirectInput" value="{{$isRedirect}}">

    @if ($isRedirect)
        <form method="POST" action="{{ route('participant.memberManage.action') }}">
            @csrf
            <input type="hidden" value="{{$id}}" name="eventId">
            <input type="hidden" value="{{$selectTeam->id}}" name="teamId">
            <input id="manageMemberButton" type="submit" class="d-none" value="Done">
            </div>
        </form>
        <a class="d-none" id="manageRosterUrl" href="{{route('participant.roster.manage', ['id' => $id, 'teamId' => $selectTeam->id, 'redirect' => 'true' ] ) }}"> </a>
        <a class="d-none" id="manageRegistrationUrl" href="{{route('participant.register.manage', ['id' => $selectTeam->id, 'eventId' => $id ] ) }}"> </a>
        <div class="time-line-box mx-auto" id="timeline-box">
            <div class="swiper-container text-center">
                <div class="swiper-wrapper">
                    <div class="swiper-slide swiper-slide__left" id="timeline-1">
                        <div class="timestamp" onclick="window.toastError('Cannot go back to \'Select Team\'.');"><span
                                class="cat">Select Team</span></div>
                        <div class="status__left" onclick="window.toastError('Cannot go back to \'Select Team\'.');">
                            <span><small></small></span></div>
                    </div>
                    <div class="swiper-slide" id="timeline-2">
                        <div class="timestamp" onclick="document.getElementById('manageMemberButton')?.click();"><span >Manage Members</span></div>
                        <div class="status" onclick="document.getElementById('manageMemberButton')?.click();">
                            <span><small></small></span></div>
                    </div>
                    <div class="swiper-slide" id="timeline-launch">
                        <div class="timestamp" onclick="document.getElementById('manageRosterUrl').click();"><span
                                class="date text-primary">Manage Roster</span></div>
                        <div class="status" onclick="document.getElementById('manageRosterUrl').click();">
                            <span><small class="bg-primary"></small></span></div>
                    </div>
                    <div class="swiper-slide swiper-slide__right" id="timeline-payment">
                        <div class="timestamp"
                            onclick="document.getElementById('manageRegistrationUrl').click();">
                            <span>Manage Registration</span></div>
                        <div class="status__right"
                            onclick="document.getElementById('manageRegistrationUrl').click();">
                            <span><small></small></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="breadcrumb-top">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a onclick="window.toastError('Cannot go back to \'Select Team\'.');">Select Team</a></li>
                    <li class="breadcrumb-item"><a class="text-primary" onclick="document.getElementById('manageMemberButton')?.click();">Manage Members</a></li>
                    <li class="breadcrumb-item"><a
                            onclick="document.getElementById('manageRosterUrl').click();">Manage Roster</a>
                    </li>
                    <li class="breadcrumb-item"><a
                            onclick="document.getElementById('manageRegistrationUrl').click();">Manage Registration</a></li>
                </ol>
            </nav>
        </div>
    @else
        @include('Participant.__Partials.TeamHead') 
        <br>
    @endif
    @php
        use Carbon\Carbon;
        $isCreator = $selectTeam->creator_id == $user->id;
    @endphp
        @if ($isRedirect) 
            <div class="text-center">
                <h5><u>Currrent Roster</u></h5>
                <p>Manage your current roster</p>
            </div>
        @endif
        <div class="mb-4 text-success mx-auto text-center">
            You have joined this event successfully!
            <a
                href="{{ route('participant.event.view', ['id' => $id]) }}">
                <button class="oceans-gaming-default-button oceans-gaming-default-button-link ms-2 me-1" type="submit" style="display: inline !important;">
                    <u> View Event </u>
                </button>
            </a>
            <a href={{ route('participant.home.view')}}>
                <button class="btn btn-link ms-0 me-2 d-inline" type="submit" >
                    Home Screen
                </button>
            </a>
        </div>
        @php
            $rosterMembersCount = $rosterMembers->count();
            $teamMembersCount = $teamMembers->count();
        @endphp
        <div>
            <p class="text-center mx-auto">Team {{ $selectTeam->teamName }} has
                {{ $rosterMembersCount }} accepted roster member{{bladePluralPrefix($rosterMembersCount)}}
                from {{$teamMembersCount}} available team member{{bladePluralPrefix($teamMembersCount)}}.
            </p>
            @if (isset($teamMembers[0]))
                <table class="member-table table-responsive ">
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
                                <td class="colorless-cell">
                                    <a href="{{route('public.team.view', ['id' => $member->id])}}"> 
                                         <svg class="gear-icon-btn"
                                            xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                            class="bi bi-eye-fill" viewBox="0 0 16 16">
                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                            <path
                                                d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                        </svg>
                                    </a>
                                </td>
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
                                    <img 
                                        width="45" height="45" 
                                        src="{{ bladeImageNull($member->user->userBanner) }}"
                                        class="rounded-circle me-1 random-color-circle object-fit-cover"
                                    >
                                    <span>{{ $member->user->name }}</span>
                                </td>
                                <td class="coloured-cell">
                                    <span>{{ $member->user->email }}</span>
                                </td>
                                <td class="flag-cell coloured-cell">
                                    <span>{{ $member->user->participant->region_emoji }} </span>
                                </td>
                                <td class="flag-cell coloured-cell">
                                    @if ($isRoster)
                                        Joined  {{is_null($roster->created_at) ? "": Carbon::parse($roster->created_at)->diffForHumans()}}
                                    @else
                                        Not in roster
                                    @endif
                                </td>
                                <td class="colorless-col pl-4">
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
                                <td  class="colorless-col px-0 mx-0">
                                    @if (!$captain || $member->id != $captain->team_member_id)
                                        <button id="captain-{{$member->id}}" class="gear-icon-btn invisible-until-hover mx-0 px-0" onclick="capatainMember({{$member->id}})">
                                            <img height="30" width="30" src="{{asset('assets/images/participants/crown-straight.png')}}">
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-red mx-auto">
                    You need accepted members to continue
                </p>
            @endif
        </div>
        @if ($isRedirect)
            <div class="d-flex box-width back-next mb-5">
                <button type="button"
                    class="btn border-dark rounded-pill py-2 px-4" onclick="document.getElementById('manageMemberButton')?.click();"> Back </button>
                <button type="button" 
                    class="btn btn-primary text-light rounded-pill py-2 px-4"
                    onclick="document.getElementById('manageRegistrationUrl')?.click();"> Next > </button>
            </div>
        @else
            <br><br><br><br><br><br>
        @endif
    </main>

    <script src="{{ asset('/assets/js/models/DialogForMember.js') }}"></script>
    
    <script>
        let dialogForMember = new DialogForMember();
        function scroll() {
            let successValue = localStorage.getItem('success');

            if (successValue == 'true') {
                
                document.querySelector('.main2').scrollIntoView();
            }
        }

        addOnLoad( ()=> { 
            window.loadMessage(); 
            scroll(); 
        });

        let actionMap = {
            'approve': approveMemberAction,
            'disapprove': disapproveMemberAction,
            'captain': capatainMemberAction,
            'deleteCaptain': deleteCaptainAction,
        };

        function reloadUrl(currentUrl, buttonName) {
            let isRedirect = document.getElementById("isRedirectInput")?.value;
            if (isRedirect) {
                document.getElementById('manageRosterUrl')?.click();
                return;
            }
            
            if (currentUrl.includes('?')) {
                currentUrl = currentUrl.split('?')[0];
            } 

            localStorage.setItem('success', 'true');
            localStorage.setItem('tab', buttonName);
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
            window.dialogOpen('Continue with approval?', takeYesAction, takeNoAction)
        }

           function disapproveMember(memberId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setActionName('disapprove')
            window.dialogOpen('Continue with disapproval?', takeYesAction, takeNoAction)
        }

        function capatainMember(memberId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setActionName('captain')
            window.dialogOpen('Are you sure you want to this user captain?', takeYesAction, takeNoAction)
        }

        function deleteCaptain(memberId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setActionName('deleteCaptain')
            window.dialogOpen('Are you sure you want to remove this user from captain?', takeYesAction, takeNoAction)
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
                        'team_member_id': memberId,
                        'team_id': {{ $joinEvent->team_id }}
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
            window.location.href = "{{route('public.participant.view', ['id' => ':id']) }}"
                .replace(':id', userId);
        }
    </script>
</body>
