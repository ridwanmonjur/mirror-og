@include('Auth.Layout.HeadTag')

@section('signUpbody')
    <img class="mt-4  mb-2" src="{{ asset('/assets/images/driftwood logo.png') }}">
    <br>
    <h5><u>Reset Password</u></h5>
    <br>
    <p> Enter your password</p>
    <form autocomplete="off" name="reset-password-form" id="reset-password-form" method="post"
        action="{{ route('user.reset.action') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="flash-message">
            @include('__CommonPartials.Flash')
        </div>
        <div class="field password">
            <label for="password" class="placeholder-moves-up-container">
                <input autocomplete="new-password" type="password" name="password" id="password" minlength="6"
                    maxlength="24" required="true" class="input-area" oninput="movePlaceholderUp(this)">
                <span class="placeholder-moves-up">Password</span>
                <i class="fa fa-eye" id="togglePassword" onclick="togglePassword('password', 'togglePassword');"
                    style="cursor: pointer; margin-top: 10px"></i>
                <div class="field-error-message d-none"></div>
            </label>
        </div>

        <div class="field password">
            <label for="password" class="placeholder-moves-up-container">
                <input autocomplete="new-password" type="password" name="confirmPassword" id="confirmPassword"
                    minlength="6" maxlength="24" required="true" class="input-area" oninput="movePlaceholderUp(this)">
                <span class="placeholder-moves-up">Confirm Password</span>
                <i class="fa fa-eye" id="toggleConfirmPassword"
                    onclick="togglePassword('confirmPassword', 'toggleConfirmPassword');"
                    style="cursor: pointer; margin-top: 10px"></i>
                <div class="field-error-message d-none"></div>
            </label>
        </div>
        <div class="field">
            <input id="submit" type="submit" value="Reset Password"
                class="oceans-gaming-default-button oceans-gaming-green-button">
            <br>
            <br><br>
    </form>
    <script src="{{ asset('/assets/js/organizer/signup.js') }}"></script>

@endsection

@include('Auth.Layout.SignUpBodyTag')
