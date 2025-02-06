@include('Auth.Layout.HeadTag')

@section('signUpbody')
<a href="{{route('public.landing.view')}}">
    <img class="mt-4 mb-2 motion-logo" src="{{ asset('/assets/images/driftwood logo.png') }}">
</a>
<header><u>Forgotten Password?</u></header>
<br>
<p> Enter your email to reset your password</p>
<form name="forget-password-form" id="forget-password-form" method="post" action="{{route('user.forget.action')}}">
    @csrf
    <div class="flash-message">
        @include('__CommonPartials.Flash')
    </div>
   <div class="field">
        <label for="email" class="placeholder-moves-up-container">
            <input  autocomplete="off" type="email" name="email" id="email" required="true" class="input-area" >
            <span class="placeholder-moves-up">Email address</span>
            <div class="field-error-message d-none"></div>
        </label>
    </div>
    <br>
    <div class="field">
        <input id="submit" type="submit" value="Forgot Password" class="oceans-gaming-default-button oceans-gaming-green-button">
    <br><br>
</form>

@endsection

@include('Auth.Layout.SignUpBodyTag')
