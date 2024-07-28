<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Edit</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamCreate.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.css">
    @include('__CommonPartials.HeadIcon')
    
</head>

<body>
    @include('__CommonPartials.NavbarGoToSearchPage')
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
                                <input type="text" value="{{ $team ? $team->teamName : '' }}" name="teamName" id="teamName" placeholder="Team Name"
                                    onclick="clearPlaceholder(this)" onblur="restorePlaceholder(this)">
                                <input type="text" style="height: 100px;" value="{{ $team ? $team->teamDescription : '' }}" name="teamDescription"
                                    id="teamDescription" placeholder="Write your team description..." onclick="clearPlaceholder(this)"
                                    onblur="restorePlaceholder(this)">
                                <br> <br>
                                <input type="submit" onclick="" value="Edit">
                                
                            </div>

                    </form>
                </div>
            </div>
        </div>
    </main>

    

</body>

</html>
