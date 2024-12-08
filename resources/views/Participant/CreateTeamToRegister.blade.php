<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event creation</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamCreate.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.scss">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/timeline.css') }}">
    @include('__CommonPartials.HeadIcon')
</head>

<body>
    @include('googletagmanager::body')
    @include('__CommonPartials.NavbarGoToSearchPage')
    <main>
        <input type="hidden" id="event_view_route" value="{{ route('participant.event.view', $id) }}">
        <div class="text-center" id="step-0">
            <div class="">
                
                <div class="text-center" id="step-0">
                    <div class="mt-4">
                        <u>
                            <h5>Create & Register Your Team</h5>
                        </u>
                        <br>
                        <p class="create-online-esports">
                            What will your team be called?
                        </p>
                        <br>
                        
                        <form id="formSubmit"
                            action="{{ route('participant.createTeamToJoinEvent.action', ['id' => $id]) }}"
                            method="POST">

                            @csrf
                            @if ($errors->any())
                                <div class="text-red">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (session()->has('errorMessage'))
                                <div class="text-red">
                                    {{ session()->get('errorMessage') }}
                                </div>
                            @endif

                             <div class="d-flex flex-column align-items-center justify-content-center">
                                <input type="text" value="" name="teamName" id="teamName"
                                    placeholder="Team name">
                                <input type="text" style="height: 100px;" value="" name="teamDescription"
                                    id="teamDescription" placeholder="Write your team description...">
                                <br> <br>
                                <input type="submit" onclick="" value="Create & Regjster">

                            </div>
                        </form>
                    </div>
                    <br><br>
                </div>
            </div>
        </div>
        
    </main>
    <script src="{{ asset('/assets/js/participant/CreateTeamToRegister.js') }}"></script>
    

</body>

</html>
