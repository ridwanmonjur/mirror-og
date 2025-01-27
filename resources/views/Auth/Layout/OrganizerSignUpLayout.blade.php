@include('Auth.Layout.HeadTag')
@section('signUpbody')
<a href="{{route('public.landing.view')}}">
    <img class="mt-2  mb-1 motion-logo" src="{{ asset('/assets/images/driftwood logo.png') }}">
</a>
<h5 class="mt-2 mb-0">Create an <span class="text-primary">organizer account</span></h5>
<form 
    autocomplete="off" 
    readonly 
    name="organizer-signup-form" 
    id="organizer-signup-form" 
    method="post" 
    action="{{route('organizer.signup.action')}}">
    @csrf
    <div class="flash-message">
        @include('__CommonPartials.Flash')
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

    {{-- <div class="field">
        <label for="companyName" class="placeholder-moves-up-container">
            <input  autocomplete="off" type="text" name="companyName" id="companyName"  class="input-area" oninput="movePlaceholderUp(this)">
            <span class="placeholder-moves-up">Company Name</span>
            <div class="field-error-message d-none"></div>
        </label>
    </div>

    <div class="field">
        <label for="companyDescription" class="placeholder-moves-up-container">
            <input  autocomplete="off" type="text" name="companyDescription" id="companyDescription" class="input-area" oninput="movePlaceholderUp(this)">
            <span class="placeholder-moves-up">Company Description</span>
            <div class="field-error-message d-none"></div>
        </label>
    </div> --}}

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

    <div class="sign-txt">By continuing, you agree to Driftwood's <a href="#">Terms of Use</a>. Read our <a href="#">Privacy Policy</a>.</div>

    <div class="section-bottoms">
        <p class="py-0 my-0">Already have an account? <a href="{{ route('organizer.signin.view') }}">Sign in</a></p>
    </div>
    <div>
        <a  href="{{ route('participant.signup.view') }}">
            <button type="button" class="btn my-2 px-5 btn-secondary rounded-pill text-white btn-sm">Switch to participant</button>
        </a>
    </div>

</form>

@endsection

@include('Auth.Layout.SignUpBodyTag')


