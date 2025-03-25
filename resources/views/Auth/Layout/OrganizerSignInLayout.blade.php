@include('Auth.Layout.HeadTag')


@section('signInbody')

<div id="routeConfig" 
     data-google-login="{{ route('organizer.google.login') }}"
     data-steam-login="{{ route('organizer.steam.login') }}"
    class="d-none"
>
</div>
<a href="{{route('public.landing.view')}}">
    <img class="my-0 motion-logo mb-2" src="{{ asset('/assets/images/dw_logo.webp') }}">
</a>
<h5 class="mx-0 my-0">Sign in to your <span class="text-primary">organizer account</span></h5>
<form 
    autocomplete="off" 
    novalidate
    name="organizer-signin-form" 
    id="organizer-signin-form" 
    method="post" 
    action="{{route('organizer.signin.action')}}"
    onsubmit="return submitSignInUpForm(event);"
>
    @csrf
    <div class="flash-message">
        @include('includes.Flash')
       
    </div>
    @include('Auth.Layout.__SigninVerify')
    
    @include('Auth.Layout.__Signin')

    <div class="pass-txt mb-2 d-flex justify-content-between">
        <div class="form-group form-check">
            <label class="form-check-label" for="remember-me2" >
            
            Remember me</label>
            <input type="checkbox" class="form-check-input" checked name="remember-me" id="remember-me2">

        </div>
        <a href="{{ route('user.forget.view') }}">Forgot password?</a>
    </div>
    <input type="submit" value="Sign in">   

    <div class="sign-txt">By continuing, you agree to Driftwood's <a href="#" role="button">Terms of Use</a>. Read our <a
            href="#" role="button">Privacy Policy</a>.</div>
    <div class="section-or">
        <div class="straight-line"></div>
        <p class="straight-line-or px-4">        or        </p>
        <div class="straight-line"></div>
    </div>

    <button type="button" class="btn-login" onclick="redirectToGoogle();">
        <img class="image-login" src="{{ asset('/assets/images/auth/google.svg') }}" alt="">
        <p>Continue with Google</p>
    </button>
   
</form>

<div class="section-bottom">
    <p class="py-0 my-0">New to Driftwood? <a href="{{ route('organizer.signup.view') }}">Create an account</a></p>
</div>
<div>
    <a  href="{{ route('participant.signin.view') }}" class="btn my-2 px-5 btn-secondary rounded-pill text-white btn-sm">Switch to participant
    </a>
</div>

@endsection


@include('Auth.Layout.SignInBodyTag')


