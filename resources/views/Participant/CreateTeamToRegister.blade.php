<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event creation</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamCreate.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.css">
    <link href='https://cdn.jsdelivr.net/npm/sweetalert2@10.15.6/dist/sweetalert2.min.css'>
    <link rel="stylesheet" href="{{ asset('/assets/css/app.css') }}">
</head>

<body>
    @include('CommonLayout.NavbarforParticipant')
    <main>
        <div class="text-center" id="step-0">
            <div class="">
                <div class="text-center" id="step-0">
                    <div class="">
                        <u>
                            <h2>Create & Register Your Team</h2>
                        </u>
                        <br>
                        <p class="create-online-esports">
                            What will your team be called?
                        </p>
                        <br>
                        <form
                            id="formSubmit"
                            action="{{ route('participant.createTeamToJoinEvent.action', ['id' => $id]) }}"
                            method="POST">
                            
                            @include('Participant.CreateEditTeam.FormErrorsSuccess')
                            @include('Participant.CreateEditTeam.FormFields', [
                                'team' => $team, 'buttonLabel' => 'Edit yout team'
                            ])
                        </form>
                    </div>
                    <br><br>
                    <div><input form="formSubmit" type="submit" value="Create & Register"></div>
                </div>
            </div>
        </div>
    </main>

    @include('CommonLayout.BootstrapV5Js')

</body>

</html>
