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
    @include('Participant.Layout.TeamHeadNoFileChange')

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
                {{ $rosterMembersProcessed['accepted']['count'] }} accepted roster members 
                from {{count($teamMembers)}} available team member(s).
            </p>
            @if (isset($teamMembers[0]))
                <table class="member-table">
                    <br>
                    <tbody>
                        @foreach ($teamMembers as $member)
                            <tr class="st" id="tr-{{$member->id}}">
                                <td class="colorless-col">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-gear gear-icon-btn" viewBox="0 0 16 16">
                                        <path
                                            d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0" />
                                        <path
                                            d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z" />
                                    </svg>                                    
                                <td>
                                <td class="coloured-cell">
                                    <div class="player-info">
                                        @if ($member->id == $captain->team_member_id)
                                            <div class="player-image"> </div>
                                        @endif
                                        <span>{{ $member->user->name }}</span>
                                    </div>
                                        <span>{{ $member->user->name }}</span>
                                    </div>
                                </td>
                                <td class="flag-cell coloured-cell">
                                    <img class="nationality-flag" src="{{ asset('/assets/images/china.png') }}"
                                        alt="User's flag">
                                </td>
                                <td class="flag-cell coloured-cell">
                                    {{ $rosterMembersKeyed[$member->id]->status }}
                                </td>
                                <td class="colorless-col">
                                    @if (in_array($rosterMembersKeyed[$member->id]->status, ['rejected', 'pending']))
                                        <button id="add-{{$member->id}}" class="gear-icon-btn" onclick="approveMember({{$rosterMembersKeyed[$member->id]->id}})">
                                            ✔
                                        </button>
                                    @endif
                                    @if (in_array($rosterMembersKeyed[$member->id]->status, ['accepted', 'pending']))
                                        <button id="remove-{{$member->id}}" class="gear-icon-btn" onclick="disapproveMember('{{$rosterMembersKeyed[$member->id]->id}}')">
                                            ✘
                                        </button>
                                    @endif
                                    <button id="captain-{{$member->id}}" class="gear-icon-btn invisible-until-hover" onclick="capatainMember('{{$rosterMembersKeyed[$member->id]->id}}')">
                                        <img height="30" width="30" src="{{asset('assets/images/participants/crown-straight.png')}}">
                                    </button>
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
            }
        }

        loadToast();

        let dialogForMember = new DialogForMember(); 

        function takeYesAction () {
            console.log({memberId: dialogForMember.getMemberId(), action: dialogForMember.getActionName()})

            if (dialogForMember.getActionName() == 'approve') {
                approveMemberAction();
            } else {
                disapproveMemberAction();
            }
        }

        function takeNoAction () {
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

        async function approveMemberAction() {
            console.log({memberId: dialogForMember.getMemberId(), action: dialogForMember.getActionName()})
            const memberId = dialogForMember.getMemberId();
            const url = "{{ route('participant.roster.approve', ['id' => ':id']) }}".replace(':id', memberId);

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                });
                const data = await response.json();
                
                if (data.success) {
                    let currentUrl = "{{route('participant.roster.manage', ['id' => $id, 'teamId'=> $selectTeam->id])}}";
                    currentUrl += (currentUrl.indexOf('?') !== -1 ? '&' : '?') + 'success=true';
                    window.location.replace(currentUrl);
                } else {
                    console.error('Error updating member status:', data.message);
                }
            } catch (error) {
                console.error('Error approving member:', error);
            }
        }

        async function disapproveMemberAction() {
            console.log({memberId: dialogForMember.getMemberId(), action: dialogForMember.getActionName()})

            const memberId = dialogForMember.getMemberId();

            const url = "{{ route('participant.roster.disapprove', ['id' => ':id']) }}".replace(':id', memberId);

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                });
                const data = await response.json();
                
                if (data.success) {
                    let currentUrl = "{{route('participant.roster.manage', ['id' => $id, 'teamId'=> $selectTeam->id])}}";
                    currentUrl += (currentUrl.indexOf('?') !== -1 ? '&' : '?') + 'success=true';
                    window.location.replace(currentUrl);
                } else {
                    console.error('Error updating member status:', data.message);
                }
            } catch (error) {
                console.error('Error approving member:', error);
            }
        }
    </script>
</body>
