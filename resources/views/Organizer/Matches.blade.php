<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournament Matches 2</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/common/tournament.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/common/dynamic-select.css') }}">
    @vite(['resources/js/tippy.js', 'resources/sass/app.scss', 'resources/js/app.js'])
    <script src="{{ asset('/assets/js/dynamicSelect.js') }}"></script>
    @include('__CommonPartials.HeadIcon')
    <style>
    </style>
</head>

<body>
    <main>
        @include('__CommonPartials.NavbarGoToSearchPage')
        <input type="hidden" id="previousValues" value="{{json_encode($previousValues)}}">
        {{-- {{$id}}
        {{$eventType}} --}}
        <div id="">
            @include('Organizer.__ManageEventPartials.BracketUpdateList')
        </div>
        @livewireScripts
        <script src="{{ asset('/assets/js/jsUtils.js') }}"></script>
    <script src="{{ asset('/assets/js/shared/tournament.js') }}"></script>

    </main>
</body>
</html>
