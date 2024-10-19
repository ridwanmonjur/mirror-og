<!-- Favicon links -->
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">

<!-- Fallback favicon -->
<link rel="icon" href="{{ asset('assets/images/driftwood logo.png') }}" type="image/x-icon">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Open Graph meta tags -->
<meta property="og:title" content="Driftwood Gaming">
<meta property="og:description" content="Features the best competitions in the world.">
<meta property="og:image" content="{{ asset('assets/images/driftwood logo.png') }}">
<meta property="og:url" content="{{ url()->current() }}">