<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


        <!-- CSRF Token -->
        {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}

        <link rel="icon" type="image/png" href="https://myanmaryellowpages.biz/files/wi1oaylrkpnxm5ugnd4b/active_gym.jpg"/>

        <title>Laravel Ecommerce | @yield('title', '')</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

        

        <link href="https://fonts.googleapis.com/css?family=Abel|Lato|Open+Sans&display=swap" rel="stylesheet">
        
        <script defer src="https://use.fontawesome.com/releases/v5.8.2/js/all.js" integrity="sha384-DJ25uNYET2XCl5ZF++U8eNxPWqcKohUUBUpKGlNLMchM7q4Wjg2CUpjHLaL8yYPH" crossorigin="anonymous"></script>


        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/laravel-ecommerce.css') }}">
        

        @yield('extra-css')
    </head>


<body class="@yield('body-class', '')">
    @include('partials.nav')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    @yield('extra-js')
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

</body>
</html>
