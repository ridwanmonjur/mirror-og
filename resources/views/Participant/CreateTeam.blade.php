<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team formation</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamCreate.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @include('__CommonPartials.HeadIcon')

</head>

<body>
    @include('googletagmanager::body')
    <main>
        @include('__CommonPartials.NavbarGoToSearchPage')

        <div class="text-center" id="step-0">
            <div class="d-flex align-items-center justify-content-center ">
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
                            @csrf
                            @if ($errors->any())
                                <div class="text-red">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li><u>{{ $error }}</u></li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (session()->has('errorMessage'))
                                <div class="text-red">
                                    <u>{{ session()->get('errorMessage') }}</u>
                                </div>
                            @endif

                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <input type="text" value="" name="teamName" id="teamName"
                                    placeholder="Team name"
                                    value="{{ old('teamName') }}"
                                >
                                <input type="text" style="height: 100px;" value="" name="teamDescription"
                                    id="teamDescription" placeholder="Write your team description..."
                                    value="{{ old('teamDescription') }}"
                                >
                                <br> <br>
                                <input type="submit" onclick="" value="Create">

                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>



</body>

</html>
