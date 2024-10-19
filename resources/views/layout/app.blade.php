<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('__CommonPartials.HeadIcon')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])    
    <title>@yield('title', 'Driftwood')</title>
    @stack('styles')
    
</head>
<body class="d-flex flex-column min-vh-100">
    <header>
        @include('__CommonPartials.NavbarBeta')
    </header>

    <main class="flex-grow-1">
        @yield('content')
    </main>

    {{-- <footer>
        @include('partials.footer')
    </footer> --}}

    @stack('scripts')
    <script src="{{ asset('/assets/js/jsUtils.js') }}"></script>
</body>
</html>