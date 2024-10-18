@include('Auth.Layout.HeadTag')

@section('signUpbody')

<img src="{{ asset('/assets/images/driftwood logo.png') }}">
<br>
<h5><u>Reset Password</u></h5>
<br>
<p> Enter your old password and your new password</p>
<form autocomplete="off" name="reset-password-form" id="reset-password-form" method="post" action="{{route('user.reset.action')}}">
    @csrf
    <div class="flash-message">
        @include('Auth.Layout.Flash')
    </div>
    <div class="field password">
        <label for="password" class="placeholder-moves-up-container">
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="password" name="password" id="password" minlength="6" maxlength="24" required="true"
                class="input-area">
            <span class="placeholder-moves-up">Password</span>
            <i class="fa fa-eye" id="togglePassword" onclick="togglePassword('password', 'togglePassword')" style="cursor: pointer; margin-top: 10px"></i>
            <div class="field-error-message d-none"></div>
        </label>
    </div>

    <div class="field password">
        <label for="password" class="placeholder-moves-up-container">
            <input type="password" name="password_confirmation" id="password_confirmation" minlength="6" maxlength="24" required="true"
                class="input-area">
            <span class="placeholder-moves-up">Confirm Password</span>
            <i class="fa fa-eye" id="toggleConfirmPassword" onclick="togglePassword('password_confirmation', 'togglePassword')" style="cursor: pointer; margin-top: 10px"></i>
            <div class="field-error-message d-none"></div>
        </label>
    </div>
    <div class="field">
        <input id="submit" type="submit" value="Reset Password" class="oceans-gaming-default-button oceans-gaming-green-button">
    <br>
    <br><br>
</form>
<script>
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
@endsection

@include('Auth.Layout.SignUpBodyTag')

