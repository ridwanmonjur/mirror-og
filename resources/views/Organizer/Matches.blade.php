<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    @include('googletagmanager::head')
   <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournament Matches</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/common/viewEvent.css') }}">
    @vite([ 'resources/sass/app.scss', 'resources/js/app.js', 'resources/js/alpine/bracket.js'])
    @include('__CommonPartials.HeadIcon')
    <style>
    </style>
</head>

@php
    $userId = isset($user) ? $user->id : null; 
@endphp

<body>
    @include('googletagmanager::body')
 
    <main x-data="alpineDataComponent">
        @include('__CommonPartials.NavbarGoToSearchPage')
        <input type="hidden" id="eventId" value="{{$event->id}}">
        <input type="hidden" id="previousValues" value="{{json_encode($previousValues)}}">
        <input type="hidden" id="joinEventTeamId" value="{{$existingJoint?->team_id }}">
        <input type="hidden" id="userLevelEnums" value="{{json_encode($USER_ACCESS)}}">
        <input type="hidden" id="hidden_user_id" value="{{ $userId }}">
        <div class="px-4 py-4">
            @include('Organizer.__ManageEventPartials.BracketUpdateList')
        </div>
        
        <script src="{{ asset('/assets/js/shared/ViewEvent.js') }}"></script>
    </main>
</body>
</html>
