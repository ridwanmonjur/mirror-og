@include('Auth.Layout.HeadTag')


@section('signUpbody')

<img src="{{ asset('/assets/images/auth/logo.png') }}">
<header><u>Create an Account</u></header>
<form name="signup-form" id="signup-form" method="post" action="{{route('participant.signup.action')}}">
    @csrf
    <div class="flash-message">
        @include('Auth.Layout.Flash')
    </div>
    <div class="field">
        <label for="username" class="placeholder-moves-up-container">
            <input type="username" name="username" id="username" required="true" class="input-area">
            <span class="placeholder-moves-up">Username</span>
            <div class="field-error-message d-none"></div>
        </label>
    </div>

    <div class="field">
        <label for="email" class="placeholder-moves-up-container">
            <input type="email" name="email" id="email" required="true" class="input-area">
            <span class="placeholder-moves-up">Email address</span>
            <div class="field-error-message d-none"></div>
        </label>
    </div>

    <div class="field password">
        <label for="password" class="placeholder-moves-up-container">
            <input type="password" name="password" id="password" minlength="6" maxlength="24" required="true"
                class="input-area">
            <span class="placeholder-moves-up">Password</span>
            <i class="fa fa-eye" id="togglePassword" onclick="togglePassword()" style="cursor: pointer; margin-top: 10px"></i>
            <div class="field-error-message d-none"></div>
        </label>
    </div>

    <div class="field password">
        <label for="password" class="placeholder-moves-up-container">
            <input type="password" name="password" id="password" minlength="6" maxlength="24" required="true"
                class="input-area">
            <span class="placeholder-moves-up">Confirm Password</span>
            <i class="fa fa-eye" id="togglePassword" onclick="togglePassword()" style="cursor: pointer; margin-top: 10px"></i>
            <div class="field-error-message d-none"></div>
        </label>
    </div>

    <input type="submit" value="Register">

    <div class="sign-txt">By continuing, you agree to Splash's <a href="#">Terms of Use</a>. Read our <a
        href="#">Privacy Policy</a>.</div>

        <div class="section-bottoms">
            <p>Already have an account? <a href="{{ route('participant.signin.view') }}">Sign in</a></p>
        </div>

        </form>

        @endsection

        @include('Auth.Layout.SignUpBodyTag')
