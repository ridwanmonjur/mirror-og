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

    <main>
        @if($count > 0)
            @foreach ($teamList as $team)
                <a href="/participant/team/{{ $team['id'] }}/manage">
                    <div class="wrapper">
                        <div class="team-section">
                            <div class="upload-container">
                                <label for="image-upload" class="upload-label">
                                    <div class="circle-container">
                                        <div id="uploaded-image" class="uploaded-image"></div>
                                    </div>
                                </label>
                                <input type="file" id="image-upload" accept="image/*" style="display: none;">
                            </div>
                            <h3 class="team-name" id="team-name">{{ $team->teamName }}</h3>
                            <br>
                            <p>Total Members:
                                {{ empty($usernamesCountByTeam[$team->id]) ? 1 : $usernamesCountByTeam[$team->id] }} </p>
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
                        <input type="file" id="image-upload" accept="image/*" style="display: none;">
                    </div>
                    <h3 class="team-name" id="team-name">{{ $team->teamName }}</h3>
                    <br>
                </div>
            </div>
        @endif
    </main>

    @include('CommonLayout.BootstrapV5Js')

</body>

</html>
