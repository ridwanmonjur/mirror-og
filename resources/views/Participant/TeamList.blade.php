<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Teams</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamList.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.css">
    
</head>

<body>
    @include('CommonLayout.NavbarGoToSearchPage')
    <br><br> 
    <div class="d-flex justify-content-center"> 
        <button onclick="goToScreen();" type="button" class="btn oceans-gaming-default-button position-relative">
            Team Requests
        </button>
    </div>
    <main>
        @if ($count > 0)
            @foreach ($teamList as $team)
                <a style="cursor:pointer;" href="/participant/team/{{ $team['id'] }}/manage">
                    <div class="wrapper">
                        <div class="team-section">
                            <div class="upload-container">
                                <div class="circle-container" style="cursor: pointer;">
                                    <div id="uploaded-image" class="uploaded-image"
                                        style="background-image: url({{ $team->teamBanner ? '/storage' . '/'. $team->teamBanner: '/assets/images/animations/empty-exclamation.gif' }} );"
                                    ></div>
                                    </label>
                                </div>
                                <h3 class="team-name" id="team-name">{{ $team->teamName }}</h3>
                                <br>
                                <p>Total Members:
                                    @if (isset($membersCount[$team->id]))
                                        {{ $membersCount[$team->id] }}
                                    @else {{ 0 }}
                                    @endif
                                </p>
                                @if ($team->creator_id == $user->id)
                                <small><i>Created by you</i></small>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        @else
            <div class="wrapper">
                <div class="team-section">
                    <div class="upload-container">
                        <label for="image-upload" class="upload-label">
                            <img                       
                                src="{{asset('assets/images/animation/empty-exclamation.gif') }}"
                                width="150"
                                height="150"
                            >
                        </label>
                    </div>
                    <h3 class="team-name" id="team-name">No teams yet</h3>
                    <br>
                </div>
            </div>
        @endif
        <br>
        <br>
    </main>

    @include('CommonLayout.BootstrapV5Js')

    <script>
        function goToScreen() {
            window.location.href = "{{route('participant.request.view')}}";
        }
    </script>
</body>

</html>
