@include('Auth.Layout.HeadTag')


@section('signUpbody')
    <img class="mt-4  mb-2 motion-logo" src="{{ asset('/assets/images/driftwood logo.png') }}">
    <h5><u class="px-2">Create a participant account</u></h5>
    <form autocomplete="off" readonly name="signup-form" id="signup-form" method="post"
        action="{{ route('participant.signup.action') }}"
    >
        @csrf
        <div class="flash-message">
            @include('__CommonPartials.Flash')
        </div>
        <div class="field">
            <label for="username" class="placeholder-moves-up-container">
                <input autocomplete="off" type="username" name="username" id="username" required="true" class="input-area"
                    oninput="movePlaceholderUp(this)">
                <span class="placeholder-moves-up">Username</span>
                <div class="field-error-message d-none"></div>
            </label>
        </div>

        <div class="field">
            <label for="email" class="placeholder-moves-up-container">
                <input autocomplete="off"  type="email" name="email" id="email" required="true" class="input-area"
                    oninput="movePlaceholderUp(this)">
                <span class="placeholder-moves-up">Email address</span>
                <div class="field-error-message d-none"></div>
            </label>
        </div>

        <div class="field password">
            <label for="password" class="placeholder-moves-up-container">
                <input autocomplete="new-password"  type="password" name="password" id="password" minlength="6" maxlength="24" required="true"
                    class="input-area" oninput="movePlaceholderUp(this)">
                <span class="placeholder-moves-up">Password</span>
                <i class="fa fa-eye" id="togglePassword" onclick="togglePassword('password', 'togglePassword');"
                    style="cursor: pointer; margin-top: 10px"></i>
                <div class="field-error-message d-none"></div>
            </label>
        </div>

        <div class="field password">
            <label for="password" class="placeholder-moves-up-container">
                <input autocomplete="new-password"  type="password" name="confirmPassword" id="confirmPassword" minlength="6" maxlength="24"
                    required="true" class="input-area" oninput="movePlaceholderUp(this)">
                <span class="placeholder-moves-up">Confirm Password</span>
                <i class="fa fa-eye" id="toggleConfirmPassword"
                    onclick="togglePassword('confirmPassword', 'toggleConfirmPassword');"
                    style="cursor: pointer; margin-top: 10px"></i>
                <div class="field-error-message d-none"></div>
            </label>
        </div>

        <input type="submit" value="Register">

        <div class="sign-txt">By continuing, you agree to Driftwood's <a href="#">Terms of Use</a>. Read our <a
                href="#">Privacy Policy</a>.</div>

        <div class="section-bottoms">
            <p>Already have an account? <a href="{{ route('participant.signin.view') }}">Sign in</a></p>
        </div>

    </form>
@endsection

@include('Auth.Layout.SignUpBodyTag')
<script src="{{ asset('/assets/js/participant/signup.js') }}"></script>
