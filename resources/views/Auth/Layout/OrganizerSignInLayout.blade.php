@include('Auth.Layout.HeadTag')


@section('signInbody')


<img src="{{ asset('/assets/images/auth/logo.png') }}">
<header><u>Sign in to your organizer account</u></header>
<form autocomplete="off" readonly name="organizer-signin-form" id="organizer-signin-form" method="post" action="{{route('organizer.signin.action')}}">
    @csrf
    <div class="flash-message">
    @include('Auth.Layout.Flash')
        @if(session('errorEmail'))
        Click
        <a style="font-weight: bold; text-decoration: underline;"
            href="{{ route('user.verify.resend', ['email' => session('errorEmail')]) }}">
            here</a>
        &nbsp;to resend verification email.
        @endif
    </div>
    <br>
    <div class="field">
        <label for="email" class="placeholder-moves-up-container">
            <input type="email" name="email" id="email" required="true" class="input-area" oninput="movePlaceholderUp(this)">
            <span class="placeholder-moves-up">Email</span>
            <div class="field-error-message d-none"></div>
        </label>
    </div>

    <div class="field password">
        <label for="password" class="placeholder-moves-up-container">
            <input type="password" name="password" id="password" minlength="6" maxlength="24" required="true"
                class="input-area" oninput="movePlaceholderUp(this)">
            <span class="placeholder-moves-up">Password</span>
            <i class="fa fa-eye" id="togglePassword" onclick="togglePassword()" style="cursor: pointer; margin-top: 10px"></i>
            <div class="field-error-message d-none"></div>
        </label>
    </div>
    <div class="pass-txt">
        <div class="remember-checkbox">
            <input type="checkbox" name="" id="">
            <label class="text-checkbox">Remember me</label>
        </div>
        <a href="{{ route('user.forget.view') }}">Forgot password?</a>
    </div>
    <input type="submit" value="Sign in">

    <div class="sign-txt">By continuing, you agree to Splash's <a href="#">Terms of Use</a>. Read our <a
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
    <p>New to Splash? <a href="{{ route('organizer.signup.view') }}">Create an account</a></p>
</div>
<script>
    function redirectToGoogle() {
        window.location.href = "{{ route('organizer.google.login') }}";
    }

    function redirectToSteam() {
        window.location.href = "{{ route('organizer.steam.login') }}";
    }

    function movePlaceholderUp(input) {
        const label = input.parentElement;
        const placeholder = label.querySelector('.placeholder-moves-up');
        if (input.value !== '') {
            placeholder.style.top = '0px';
            placeholder.style.fontSize = '12px';
        } else {
            placeholder.style.top = '';
            placeholder.style.fontSize = '';
        }
    }
</script>

@endsection


@include('Auth.Layout.SignInBodyTag')


