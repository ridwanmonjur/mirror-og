<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event creation</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamCreate.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.css">
    <link href='https://cdn.jsdelivr.net/npm/sweetalert2@10.15.6/dist/sweetalert2.min.css'>
</head>

<body>
    @include('CommonLayout.Navbar')

    <div class="text-center" id="step-0">
        <div class="welcome">
            <u>
                <h2>Create Your Team</h2>
            </u>
            <br><br><br>
            <p class="create-online-esports">
                What will your team be called?
            </p>
            <br>
            <form action="{{ url('/participant/team-management') }}" method="POST">
                @csrf

                <!-- Display validation errors -->
                @if ($errors->any())
                <div class="alert alert-danger">
                <ul>
                @foreach ($errors->all() as $error)
                 <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
                    @endif
                    @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <input type="text" name="teamName" id="teamName" placeholder="Team Name">
        </div>
        
        <div><input type="submit" onclick="" value="Create Team"></div>
    </form>
    </div>
    @include('CommonLayout.BootstrapJs')

</body>
</html>
