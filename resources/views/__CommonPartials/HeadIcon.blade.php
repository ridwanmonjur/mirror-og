
<!-- Poppins -->
<!-- Only preload the primary font weights that are used above the fold -->
<link 
    rel="preload" 
    href="https://fonts.gstatic.com/s/poppins/v20/pxiByp8kv8JHgFVrLGT9Z1xlFQ.woff2" 
    as="font" 
    type="font/woff2" 
    crossorigin
>
<link 
    rel="preload" 
    href="https://fonts.gstatic.com/s/nunito/v25/XRXV3I6Li01BKofINeaBXso.woff2" 
    as="font" 
    type="font/woff2" 
    crossorigin
>
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
