@extends('layout.app')

@section('title', 'Driftwood - About Us')

@push('styles')
    <link href="{{ asset('/assets/css/open/About.css') }}" rel="stylesheet">
@endpush

@section('content')
    <header>
        @include('__CommonPartials.NavbarBeta')
    </header>
    <main style="padding: 5vh 10vw ;">
        <br><br>
        <div class="card">
            @if (isset($error))
                <div class="mb-2 mt-4 text-center">
                    <div class="text-danger mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor"
                            class="bi bi-x-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                            <path
                                d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                        </svg>
                    </div>
                    <h4 class="text-danger">Verification Failed</h4>
                    <div class=" rounded-pill  ">
                        {{ $error }}
                    </div>
                </div>
            @endif

            @if (isset($success))
                <div class="mb-2 mt-4  text-center">
                    @if ($success === "verified_now")
                        <div class="text-success mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor"
                                class="bi bi-check-circle" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                <path
                                    d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05" />
                            </svg>
                        </div>
                        <h4 class="text-success mt-2">Got it!</h4>
                        <div class="rounded-pill d-inline-block ">
                            </p>We've received your email address safe and sound. Now all you have to do is wait for an
                            invitation email from us. Keep an eye out! </p>

                            </p> If you need any support, ping us at <a class="text-primary" href="mailto:supportmain@driftwood.gg">supportmain@driftwood.gg</a> and we'll come to your aid.
                            </p>
                        </div>
                    @elseif ($success === "verified_already")
                        <div class="text-success mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor"
                                class="bi bi-check-circle" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                <path
                                    d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05" />
                            </svg>
                        </div>
                        <h4 class="text-success mt-2">Wait a minute...</h4>
                        <div class="rounded-pill d-inline-block ">
                            </p> <p>This email address <strong>{{$email}}</strong> has already been confirmed!</p>
                            <p>Submit another email address, or just wait for an invitation email for {{$email}}.</p>
                            <p style="margin-top: 20px;">If you need any support, ping us at 
                                <a class="text-primary" href="mailto:supportmain@driftwood.gg">supportmain@driftwood.gg</a> 
                                and we'll come to your aid.
                            </p>
                        </div>
                    @endif
                </div>
            @endif

            <div class="mt-3 d-flex justify-content-center">
                <a href="{{ route('public.closedBeta.view') }}" class="text-light btn btn-primary mx-auto rounded-pill">
                    Back to Driftwood
                </a>
            </div>
            <br> 
        </div>
        
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('/assets/js/open/About.js') }}"></script>
@endpush
