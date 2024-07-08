<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event creation</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamCreate.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.css">
    
</head>

<body>
    @include('CommonPartials.NavbarGoToSearchPage')
    <main>
        <div class="text-center" id="step-0">
            <div class="welcome">
                <u>
                </u>
                <p class="create-online-esports"></p>
                <br>
                <div class="text-center" id="step-0">
                    <div class="welcome">
                        <u>
                            <h2>Edit Your Team</h2>
                        </u>
                        <br><br><br>
                        <p class="create-online-esports">
                            What will your team be called?
                        </p>
                        <br>
                        <form action="{{ route('participant.team.editStore', ['id' => $team->id]) }}" method="POST">
                            @include('Participant.__CreateEditTeamPartials.FormErrorsSuccess')
                            @include('Participant.__CreateEditTeamPartials.FormFields', [
                                'team' => $team, 'buttonLabel' => 'Edit'
                            ])
                    </form>
                </div>
            </div>
        </div>
    </main>

    

</body>

</html>
