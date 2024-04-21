<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event creation</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamCreate.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.css">
    <link href='https://cdn.jsdelivr.net/npm/sweetalert2@10.15.6/dist/sweetalert2.min.css'>
    
</head>

<body>
    @include('CommonPartials.NavbarGoToSearchPage')
    <main>
        <div class="text-center" id="step-0">
            <div class="welcome">
                <u></u>
                <p class="create-online-esports"> </p>
                <br>
                <div class="text-center" id="step-0">
                    <div class="welcome">
                        <u>
                            <h2>Create Your Team</h2>
                        </u>
                        <br><br><br>
                        <p class="create-online-esports">
                            What will your team be called?
                        </p>
                        <form action="{{ url('/participant/team/create') }}" method="POST">
                            @include('Participant.CreateEditTeamLayout.FormErrorsSuccess')
                            @include('Participant.CreateEditTeamLayout.FormFields', [
                                'team' => null, 'buttonLabel' => 'Create'
                            ])
                        </form>
                </div>
            </div>
        </div>
    </main>

    @include('CommonPartials.BootstrapV5Js')

</body>

</html>
