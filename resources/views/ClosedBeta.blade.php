@extends('layout.app')

@section('title', 'Driftwood - Community Esports')

@push('styles')
    <link href="{{ asset('/assets/css/open/ClosedBeta.css') }}" rel="stylesheet">
@endpush

@section('content')
    <header>
        @include('__CommonPartials.NavbarBeta')
    </header>

    <!-- Main Content -->
    <main >
        <!-- Hero Section -->
        <section class="hero-section pt-5 pb-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-4 mb-md-2">
                        <h2 class="display-6 ps-2 ps-lg-5 fw-lighter lh-base text-white mb-4">
                            The best place<br>for community esports.
                        </h2>
                    </div>
                    <div class="col-md-6">
                        <p class="text-white mb-4">
                            We're currently accepting closed beta users. If you're interested to join the closed beta, just submit your email address and wait for an invitation via email.
                        </p>
                        <form id="emailForm" class="d-flex">
                            @csrf
                            <input type="email" class="ps-5 rounded-pill py-2 me-2 form-control" placeholder="enter your email address" id="emailInput" name="email">
                            <button id="submitButton" type="submit" class="mx-auto submit-button submit-button-border text-white py-2 rounded-pill ps-3 pe-4" data-url="{{ route('public.interest.action') }}">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Description Section with Background Image -->
        <section class="description-section py-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 mb-4 mb-md-0 px-3 px-lg-5">
                        <p class="text-dark mb-3">
                            Driftwood is a passion project made by esports lovers.
                        </p>
                        <p class="text-dark mb-3">
                            Our goal is to grow and develop our local amateur esports scene, and to make esports more accessible for everyone.
                        </p>
                        <p class="text-dark mb-4">
                            We hope to see you on Driftwood!
                        </p>
                    </div>
                    <div class="col-md-6 px-3 px-lg-5">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-bg-yellow p-3 rounded-circle me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path></svg>
                            </div>
                            <p class="text-dark mb-0">
                                <span class="fw-bold" style="color: #ff914d;">PLAY</span> as a team and compete against other teams to be the champion.
                            </p>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-bg-emerald p-3 rounded-circle me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                            </div>
                            <p class="text-dark mb-0">
                                <span class="fw-bold " style="color: #00bf63;">MEET</span> like-minded players and form communities around games and esports.
                            </p>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="icon-bg-purple p-3 rounded-circle me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white"><path d="M18 8a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v.93a2 2 0 0 1-.59 1.42L3.59 12a2 2 0 0 0 0 2.83l1.82 1.82A2 2 0 0 1 6 18.07V19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-.93a2 2 0 0 1 .59-1.42L20.41 15a2 2 0 0 0 0-2.83l-1.82-1.82A2 2 0 0 1 18 8.93V8Z"></path><circle cx="12" cy="12" r="2"></circle></svg>
                            </div>
                            <p class="text-dark mb-0">
                                <span class="fw-bold " style="color: #5e17eb;">CHILL</span> with friends and discover the fun in esports together.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
       
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('/assets/js/open/ClosedBeta.js') }}"></script>
@endpush