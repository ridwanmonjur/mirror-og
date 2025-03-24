@include('Auth.Layout.HeadTag')


@section('signInbody')

<div id="routeConfig" 
     data-google-login="{{ route('participant.google.login') }}"
     data-steam-login="{{ route('participant.steam.login') }}"
     class="d-none"
>
</div>
<a href="{{route('public.landing.view')}}">
    <img class="my-0 motion-logo mb-2" src="{{ asset('/assets/images/dw_logo.webp') }}">
</a>
<h5 class="px-2 my-0">Sign in to your <span class="text-primary">participant account</span></h5>
<form 
    autocomplete="off" 
    readonly 
    novalidate
    name="signin-form" 
    id="signin-form" 
    method="post" 
    onsubmit="submitSignInUpForm(event);"
    action="{{route('participant.signin.action', 
        [
            'intended' => request()->get('intended'),
        ]
    )}}"
>
    @csrf
    <div class="flash-message">
        @if(session('errorEmail'))
            Click
            <a
                style="font-weight: bold; text-decoration: underline;"
                href="{{ route('user.verify.resend', ['email' => session('errorEmail')]) }}">
                here
            </a>
            to resend verification email.
        @endif
        @include('includes.Flash')
    </div>

    @include('Auth.Layout.__Signin')

    <div class="pass-txt mb-2 d-flex justify-content-between">
        <div class="form-check">
            <label class="form-check-label" for="remember-me">
                <input type="checkbox" class="form-check-input" name="remember-me" checked>
                Remember me
            </label>
        </div>
        <a href="{{ route('user.forget.view') }}">Forgot password?</a>
    </div>
    <input type="submit" value="Sign in">

    <div class="sign-txt">By continuing, you agree to Driftwood's <a href="#" role="button">Terms of Use</a>. Read our <a
            href="#" role="button">Privacy Policy</a>.</div>
    <div class="section-or">
        <div class="straight-line"></div>
        <p>or</p>
        <div class="straight-line"></div>
    </div>

    <button type="button" class="btn-login" onclick="redirectToGoogle();">
        <img class="image-login" src="{{ asset('/assets/images/auth/google.svg') }}" alt="">
        <p>Continue with Google</p>
    </button>
   
</form>

<div class="section-bottom">
    <p class="py-0 my-0">New to Driftwood? <a href="{{ route('participant.signup.view') }}">Create an account</a></p>
</div>
<div>
    <a  href="{{ route('organizer.signin.view') }}" class="btn my-2 px-5 btn-secondary rounded-pill text-white btn-sm">Switch to organizer
    </a>
</div>

@endsection


@include('Auth.Layout.SignInBodyTag')


