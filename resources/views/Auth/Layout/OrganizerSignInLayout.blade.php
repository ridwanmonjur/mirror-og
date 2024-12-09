@include('Auth.Layout.HeadTag')


@section('signInbody')

<div id="routeConfig" 
     data-google-login="{{ route('organizer.google.login') }}"
     data-steam-login="{{ route('organizer.steam.login') }}"
    class="d-none"
>
</div>
<img class="mt-4 mb-2 motion-logo" src="{{ asset('/assets/images/driftwood logo.png') }}">
<h5 class="mt-2 mb-0"><u>Sign in to your organizer account</u></h5>
<form 
    autocomplete="off" 
    readonly 
    name="organizer-signin-form" 
    id="organizer-signin-form" 
    method="post" 
    action="{{route('organizer.signin.action')}}"
    onsubmit="submitForm(event);"
>
    @csrf
    <div class="flash-message">
    @include('__CommonPartials.Flash')
        @if(session('errorEmail'))
        Click
        <a style="font-weight: bold; text-decoration: underline;"
            href="{{ route('user.verify.resend', ['email' => session('errorEmail')]) }}">
            here</a>
        &nbsp;to resend verification email.
        @endif
    </div>
    <div class="field">
        <label for="email" class="placeholder-moves-up-container">
            <input autocomplete="off" type="email" name="email" id="email" required="true" class="input-area" oninput="movePlaceholderUp(this)">
            <span class="placeholder-moves-up">Email</span>
            <div class="field-error-message d-none"></div>
        </label>
    </div>

    <div class="field password">
        <label for="password" class="placeholder-moves-up-container">
            <input autocomplete="new-password" type="password" name="password" id="password" minlength="6" maxlength="24" required="true"
                class="input-area" oninput="movePlaceholderUp(this)">
            <span class="placeholder-moves-up">Password</span>
            <i class="fa fa-eye" id="togglePassword" onclick="togglePassword('password', 'togglePassword');" style="cursor: pointer; margin-top: 10px"></i>
            <div class="field-error-message d-none"></div>
        </label>
    </div>
    <div class="pass-txt mb-2">
        <div class="remember-checkbox">
            <input type="checkbox" class="form-check-input" name="" id="">
            <label class="text-checkbox">Remember me</label>
        </div>
        <a href="{{ route('user.forget.view') }}">Forgot password?</a>
    </div>
    <input type="submit" value="Sign in">

    <div class="sign-txt">By continuing, you agree to Driftwood's <a href="#">Terms of Use</a>. Read our <a
            href="#">Privacy Policy</a>.</div>
    <div class="section-or">
        <div class="straight-line"></div>
        <p>or</p>
        <div class="straight-line"></div>
    </div>

    <button type="button" class="btn-login" onclick="redirectToGoogle();">
        <img class="image-login" src="{{ asset('/assets/images/auth/google.svg') }}" alt="">
        <p>Continue with Google</p>
    </button>
    <button class="btn-login btn-steam" class="btn-login" onclick="redirectToSteam();">
        <img class="image-login" src="{{ asset('/assets/images/auth/steam.svg') }}" alt="">
        <p>Continue with Steam</p>
    </button>
</form>

<div class="section-bottom">
    <p>New to Driftwood? <a href="{{ route('organizer.signup.view') }}">Create an account</a></p>
</div>

<script src="{{ asset('/assets/js/organizer/signin.js') }}"></script>

@endsection


@include('Auth.Layout.SignInBodyTag')


