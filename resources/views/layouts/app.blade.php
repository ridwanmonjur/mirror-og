<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournament Demo</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/common/tournament.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @include('__CommonPartials.HeadIcon')
    <style>

    </style>
</head>

<body>
    @yield('content')
    @livewireScripts
    <script src="{{ asset('/assets/js/jsUtils.js') }}"></script>
</body>
</html>
