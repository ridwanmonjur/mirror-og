<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="view-transition" content="same-origin" />

    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/images/favicon/favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/favicon/apple-touch-icon.png') }}">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    {{-- fallback --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/favicon-96x96.png') }}" sizes="96x96">

    <link rel="manifest" href="{{ asset('assets/images/favicon/site.webmanifest') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Open Graph meta tags -->
    <meta property="og:title" content="Driftwood">
    <meta property="og:description" content="The best place for community esports">
    <meta property="og:image" content="{{ asset('assets/images/driftwood logo.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">

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