<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team formation</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamCreate.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @include('includes.HeadIcon')

</head>

<body>
    @include('googletagmanager::body')
    <main>
        @include('includes.Navbar')

        <div class="text-center" id="step-0" >
            <div class="d-flex align-items-center justify-content-center " >
                <div class="text-center" id="step-0" >
                    <div class="welcome">
                        <br><br>
                        <u>
                            <h2>Create Your Team</h2>
                        </u>
                        <br><br>
                        <p class="create-online-esports">
                            What will your team be called?
                        </p>
                        <br><br><br>
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
                                    maxlength="25"
                                    required
                                    oninput="if(this.value.length > 25) { toastError('Team name cannot exceed 25 characters'); this.value = this.value.substring(0, 25); }"

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
