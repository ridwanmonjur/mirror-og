@extends('layout.app')

@section('title', 'Driftwood - Contact Us')
@section('body-class', 'contact')
@push('styles')
    <link 
        rel="preload" 
        href="https://fonts.gstatic.com/s/inter/v13/UcC73FwrK3iLTeHuS_fvQtMwCp50KnMa1ZL7.woff2" 
        as="font" 
        type="font/woff2" 
        crossorigin
    >
@endpush

@section('content')
    <header>
        @auth
            @include('__CommonPartials.NavbarGoToSearchPage')
        @endauth
        @guest
            @include('__CommonPartials.NavbarBeta')
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
                                        alt="" class="me-2" width="32" height="32" aria-hidden="true">
                                    {{ $option['title'] }}
                                </h2>
                                <p class="px-0 px-lg-5 text-muted mb-4">
                                    {!! $option['description'] !!}
                                </p>
                                <a href="mailto:{{ $option['email'] }}"
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
                    <a href="{{ $link }}" class="text-white mx-2">
                        <img src="{{ asset('/assets/images/landing page assets/' . $platform . '.png') }}"
                            alt="{{ $platform }}" width="40" height="40">
                    </a>
                @endforeach
            </div>
        </div>

    </main>
@endsection

