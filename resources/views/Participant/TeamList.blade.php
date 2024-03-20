<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Teams</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamList.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.css">
    <link rel="stylesheet" href="{{ asset('/assets/css/app.css') }}">
</head>

<body>
    @include('CommonLayout.NavbarforParticipant')
    <br><br> 
    <div class="d-flex justify-content-center"> 
        <button onclick="goToScreen();" type="button" class="btn oceans-gaming-default-button position-relative">
            Team Requests
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
             Pending Count
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
                                        style="background-image: url({{ $team->teamBanner ? '/storage' . '/'. $team->teamBanner: '/assets/images/fnatic.jpg' }} );"
                                    ></div>
                                    </label>
                                </div>
                                <h3 class="team-name" id="team-name">{{ $team->teamName }}</h3>
                                <br>
                                <p>Total Members:
                                    {{ $membersCount[$team->id] }}
                                </p>
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
                            <div class="circle-container">
                                <div id="uploaded-image" class="uploaded-image"></div>
                            </div>
                        </label>
                    </div>
                    <h3 class="team-name" id="team-name">No teams invited yet</h3>
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
