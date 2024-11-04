<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('__CommonPartials.HeadIcon')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])    
    <title>@yield('title', 'Driftwood')</title>
    @stack('styles')
    

</head>
<body class="">
    @include('googletagmanager::body')
    @yield('content')
    @stack('scripts')
    <script src="{{ asset('/assets/js/jsUtils.js') }}"></script>
</body>
</html>