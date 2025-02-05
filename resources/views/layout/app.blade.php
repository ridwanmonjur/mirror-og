<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('__CommonPartials.HeadIcon')
    @guest
        @vite(['resources/sass/betaapp.scss', 'resources/js/betaapp.js'])    
    @endguest
    @auth
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])    
    @endauth
    <title>@yield('title', 'Driftwood')</title>
    @stack('styles')
    

</head>
<body class="@yield('body-class')">
    @include('googletagmanager::body')
    @yield('content')
    @stack('scripts')
    
</body>
</html>