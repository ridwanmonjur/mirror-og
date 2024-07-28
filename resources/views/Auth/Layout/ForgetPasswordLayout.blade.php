@include('Auth.Layout.HeadTag')

@section('signUpbody')

<img src="{{ asset('/assets/images/auth/logo.png') }}">
<br>
<header><u>Forgotten Password?</u></header>
<br><br>
<p> Enter your email to reset your password</p>
<form name="forget-password-form" id="forget-password-form" method="post" action="{{route('user.forget.action')}}">
    @csrf
    <div class="flash-message">
        @include('Auth.Layout.Flash')
    </div>
   <div class="field">
        <label for="email" class="placeholder-moves-up-container">
            <input  autocomplete="off" type="email" name="email" id="email" required="true" class="input-area" oninput="movePlaceholderUp(this)">
            <span class="placeholder-moves-up">Email Address</span>
            <div class="field-error-message d-none"></div>
        </label>
    </div>
    <div class="field">
        <input id="submit" type="submit" value="Forgot Password" class="oceans-gaming-default-button oceans-gaming-green-button">
    <br>
    <br><br>
</form>

@endsection

@include('Auth.Layout.SignUpBodyTag')
