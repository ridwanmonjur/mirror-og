@include('Auth.Layout.HeadTag')

@section('signUpbody')

<img src="{{ asset('/assets/images/auth/logo.png') }}">
<h5><u>Create an  organizer account</u></h5>
<form autocomplete="off" readonly name="organizer-signup-form" id="organizer-signup-form" method="post" action="{{route('organizer.signup.action')}}">
    @csrf
    <div class="flash-message">
        @include('Auth.Layout.Flash')
    </div>
    <div class="field">
        <label for="username" class="placeholder-moves-up-container">
            <input autocomplete="off" type="username" name="username" id="username" required="true" class="input-area" oninput="movePlaceholderUp(this)">
            <span class="placeholder-moves-up">Username</span>
            <div class="field-error-message d-none"></div>
        </label>
    </div>

    <div class="field">
        <label for="email" class="placeholder-moves-up-container">
            <input  autocomplete="off" type="email" name="email" id="email" required="true" class="input-area" oninput="movePlaceholderUp(this)">
            <span class="placeholder-moves-up">Email Address</span>
            <div class="field-error-message d-none"></div>
        </label>
    </div>

    <div class="field">
        <label for="companyName" class="placeholder-moves-up-container">
            <input  autocomplete="off" type="text" name="companyName" id="companyName" required="true" class="input-area" oninput="movePlaceholderUp(this)">
            <span class="placeholder-moves-up">Company Name</span>
            <div class="field-error-message d-none"></div>
        </label>
    </div>

    <div class="field">
        <label for="companyDescription" class="placeholder-moves-up-container">
            <input  autocomplete="off" type="text" name="companyDescription" id="companyDescription" required="true" class="input-area" oninput="movePlaceholderUp(this)">
            <span class="placeholder-moves-up">Company Description</span>
            <div class="field-error-message d-none"></div>
        </label>
    </div>

    <div class="field password">
        <label for="password" class="placeholder-moves-up-container">
            <input  autocomplete="off" type="password" name="password" id="password" minlength="6" maxlength="24" required="true" class="input-area" oninput="movePlaceholderUp(this)">
            <span class="placeholder-moves-up">Password</span>
            <i class="fa fa-eye" id="togglePassword" onclick="togglePassword('password', 'togglePassword')" style="cursor: pointer; margin-top: 10px"></i>
            <div class="field-error-message d-none"></div>
        </label>
    </div>

    <div class="field password">
        <label for="password" class="placeholder-moves-up-container">
            <input  autocomplete="new-password" type="password" name="confirmPassword" id="confirmPassword" minlength="6" maxlength="24" required="true" class="input-area" oninput="movePlaceholderUp(this)">
            <span class="placeholder-moves-up">Confirm Password</span>
            <i class="fa fa-eye" id="toggleConfirmPassword" onclick="togglePassword('confirmPassword', 'toggleConfirmPassword')" style="cursor: pointer; margin-top: 10px"></i>
            <div class="field-error-message d-none"></div>
        </label>
    </div>

    <input type="submit" value="Register">

    <div class="sign-txt">By continuing, you agree to Splash's <a href="#">Terms of Use</a>. Read our <a href="#">Privacy Policy</a>.</div>

    <div class="section-bottoms">
        <p>Already have an account? <a href="{{ route('organizer.signin.view') }}">Sign in</a></p>
    </div>

</form>

@endsection

@include('Auth.Layout.SignUpBodyTag')

<script>
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

    function togglePassword(fieldId, buttonId) {
        var passwordField = document.getElementById(fieldId);
        var toggleButton = document.getElementById(buttonId);

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleButton.className = 'fa fa-eye-slash';
        } else {
            passwordField.type = 'password';
            toggleButton.className = 'fa fa-eye';
        }
    }
</script>
