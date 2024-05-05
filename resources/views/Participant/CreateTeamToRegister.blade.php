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
        <br><br><br>
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
                        <form id="formSubmit"
                            action="{{ route('participant.createTeamToJoinEvent.action', ['id' => $id]) }}"
                            method="POST">

                            @include('Participant.CreateEditTeamPartials.FormErrorsSuccess')
                            @include('Participant.CreateEditTeamPartials.FormFields', [
                                'team' => null,
                                'buttonLabel' => 'Create & Register',
                            ])
                        </form>
                    </div>
                    <br><br>
                </div>
            </div>
        </div>
    </main>

    

</body>

</html>
