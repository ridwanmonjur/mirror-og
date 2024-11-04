<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Event Results</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/common/PlayerProfileStats.css') }}">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @include('__CommonPartials.HeadIcon')

</head>
@php
    use Carbon\Carbon;
    $userProfile = \App\Models\User::where('id', $id)->first();
@endphp

<body>
    @include('__CommonPartials.NavbarGoToSearchPage')
    <div id="routeConfig" data-profile-route="{{ route('public.participant.view', ['id' => $id]) }}">
    </div>
    <main class="ps-5">
        <br>
        <div class="ms-5">
            <u>
                <h3>
                    User profile stats
                </h3>
            </u>
        </div>

        @livewire('shared.profile.friends-follow-display', ['userId' => $id])
        <livewire:scripts />

        </div>
        <br>

        <br>
    </main>
    @stack('script')
    <script src="{{ asset('/assets/js/shared/ProfileStats.js') }}"></script>


    <script src="{{ asset('/assets/js/jsUtils.js') }}"></script>

</body>

</html>
