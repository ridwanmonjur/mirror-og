@extends('layout.app')

@section('title', 'Driftwood - Contact Us')
@section('body-class', 'contact')
@push('styles')
    <!-- Meta tags for SEO -->
    <meta name="description" content="Contact Driftwood - Get support for issues, ask questions, or discuss business inquiries. We're here to help the esports community. Email us at supportmain@driftwood.gg or handshake@driftwood.gg">
    <meta name="keywords" content="contact driftwood, esports support, esports help, gaming platform support, driftwood contact, esports business inquiries">

    <!-- Open Graph tags -->
    <meta property="og:title" content="Contact Driftwood - Esports Community Support">
    <meta property="og:description" content="Get in touch with Driftwood. Need support? Have questions? Let's chat about esports and gaming community needs.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://driftwood.gg/contact">
    <meta property="og:image" content="https://driftwood.gg/images/assets/images/dw_logo.webp">

    <!-- Twitter Card tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Contact Driftwood - Community Support">
    <meta name="twitter:description" content="Get support, ask questions, or discuss business opportunities with Driftwood esports platform.">
    <meta property="twitter:image" content="https://driftwood.gg/assets/images/dw_logo.webp">
      <!-- Structured Data for Contact Page -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "ContactPage",
            "name": "Contact Driftwood",
            "url": "https://driftwood.gg/contact",
            "description": "Contact page for Driftwood esports community platform",
            "publisher": {
                "@type": "Organization",
                "name": "Driftwood",
                "logo": {
                "@type": "ImageObject",
                "url": "https://driftwood.gg/assets/images/dw_logo.webp"
                }
            }
        }
    </script>

    <!-- Organization Schema with Contact Points -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "Driftwood",
            "url": "https://driftwood.gg",
            "contactPoint": [
                {
                "@type": "ContactPoint",
                "email": "supportmain@driftwood.gg",
                "contactType": "customer support",
                "availableLanguage": "English"
                },
                {
                "@type": "ContactPoint",
                "email": "handshake@driftwood.gg",
                "contactType": "sales",
                "availableLanguage": "English"
                }
            ],
            "sameAs": [
                "https://facebook.com/DriftwoodEsports",
                "https://twitter.com/DriftwoodEsports",
                "https://instagram.com/DriftwoodEsports",
                "https://discord.gg/driftwood"
            ]
        }
    </script>
@endpush

@section('content')
    <header>
        @auth
            @include('includes.Navbar.NavbarGoToSearchPage')
        @endauth
        @guest
            @include('includes.Navbar.NavbarBeta')
        @endguest
    </header>
    <!-- Contact & About Us Boxes and Social Media Data-->
    @php
        $contactOptions = [
            [
                'icon' => 'support.png',
                'title' => 'Need support?',
                'description' => <<<HTML
                    If you're <span class="text-primary">facing an issue</span> with Driftwood, <span class="text-primary">ping for support</span> and we'll be there.
                    HTML,
                'email' => 'supportmain@driftwood.gg',
                'btnClass' => 'bg-support-btn',
            ],
            [
                'icon' => 'help.png',
                'title' => 'Got a question?',
                'description' => <<<HTML
                    For <span class="text-success">any general inquiries</span> or <span class="text-success">business matters</span>, shoot us an email and let's chat.
                    HTML,
                'email' => 'handshake@driftwood.gg',
                'btnClass' => 'bg-general-btn',
            ],
        ];

        $socialLinks = [
            'fb' => '#',
            'x' => '#',
            'insta' => '#',
            'dc' => '#',
        ];
    @endphp
    <main class="py-5 d-flex justify-content-between flex-column" style="min-height: 95vh;">
        <div class="mx-auto">
            <!-- Heading -->
            <div class="g-4">
                <h1 class="display-4 text-center text-white mb-3">Contact us</h1>
                <p class="lead text-white mb-5 mx-3 text-center">
                    Talk to us - we're here to help! What do you need?
                </p>
            </div>
            <!-- Contact and About Us Box -->
            <div class="d-flex justify-content-center mx-4">
                <div class="row ">
                    <div class="d-none d-lg-block col-lg-2"> </div>
                    @foreach ($contactOptions as $key => $option)
                        <div @class([
                                "col-md-6 col-lg-4 mb-3 mx-0 mx-lg-3"
                            ]) 
                            style="view-transition-name: contact-{{ $key }};" 
                            >
                            <div class="bg-white shadow p-4 mx-auto text-center h-100"
                                style="border-radius: 30px; max-width: min(80%, 400px;)">
                                <h2 class="fs-3 pt-4 pb-3 mb-3">
                                    <img src="{{ asset('/assets/images/landing page assets/' . $option['icon']) }}"
                                        alt="{{ $option['title'] }} Image" class="me-2" width="32" height="32" aria-hidden="true">
                                    {{ $option['title'] }}
                                </h2>
                                <p class="px-0 px-lg-5 text-muted mb-4">
                                    {!! $option['description'] !!}
                                </p>
                                <a href="mailto:{{ $option['email'] }}"
                                    title="{{ $option['email'] }} Link"
                                    style="view-transition-name: link-{{ $key }}"
                                    class="btn mt-2 mb-4 {{ $option['btnClass'] }} text-white rounded-pill px-4 py-2">
                                    {{ $option['email'] }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                     <div class="d-none d-lg-block col-lg-2"> </div>
                </div>
            </div>
        </div>
        <!-- Social Media Links -->
        <div >
            <div class=" text-center">
                @foreach ($socialLinks as $platform => $link)
                    <a href="{{ $link }}" class="text-white mx-2" title="{{ $platform }} Link">
                        <img src="{{ asset('/assets/images/landing page assets/' . $platform . '.png') }}"
                            alt="{{ $platform }} Image" width="40" height="40" loading="lazy"  >
                    </a>
                @endforeach
            </div>
        </div>

    </main>
  
@endsection

