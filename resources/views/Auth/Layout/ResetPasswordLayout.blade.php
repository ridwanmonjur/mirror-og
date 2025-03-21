@include('Auth.Layout.HeadTag')

@section('signUpbody')
    <a href="{{route('public.landing.view')}}">
        <img class="mt-4  mb-2 motion-logo" src="{{ asset('/assets/images/driftwood logo.png') }}">
    </a>
    <br>
    <h5><u>Reset Password</u></h5>
    <br>
    <p> Enter your password</p>
    <form autocomplete="off" name="reset-password-form" id="reset-password-form" method="post"
        action="{{ route('user.reset.action') }}"
        >
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="flash-message">
            @include('includes.Flash')
        </div>
        <div class="field password">
            <label for="password" class="placeholder-moves-up-container">
                <input autocomplete="new-password" type="password" name="password" id="password" minlength="6"
                    maxlength="24" required="true" class="input-area" >
                <span class="placeholder-moves-up">Password</span>
                   <button type="button" class="toggle-password" onclick="togglePassword('password')">
            <!-- Show Password Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="eye-icon" width="20" height="20" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                <circle cx="12" cy="12" r="3" />
            </svg>
            <!-- Hide Password Icon (initially d-none) -->
            <svg xmlns="http://www.w3.org/2000/svg" class="eye-off-icon d-none" width="20" height="20"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path
                    d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                <line x1="1" y1="1" x2="23" y2="23" />
            </svg>
        </button>
                <div class="field-error-message d-none"></div>
            </label>
        </div>

        <div class="field password">
            <label for="password" class="placeholder-moves-up-container">
                <input autocomplete="new-password" type="password" name="confirmPassword" id="confirmPassword"
                    minlength="6" maxlength="24" required="true" class="input-area" >
                <span class="placeholder-moves-up">Confirm password</span>
                   <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword')">
            <!-- Show Password Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="eye-icon" width="20" height="20" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                <circle cx="12" cy="12" r="3" />
            </svg>
            <!-- Hide Password Icon (initially d-none) -->
            <svg xmlns="http://www.w3.org/2000/svg" class="eye-off-icon d-none" width="20" height="20"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path
                    d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                <line x1="1" y1="1" x2="23" y2="23" />
            </svg>
        </button>
                <div class="field-error-message d-none"></div>
            </label>
        </div>
        <div class="field">
            <input id="submit" type="submit" value="Reset Password"
                class="oceans-gaming-default-button oceans-gaming-green-button">
            <br>
            <br><br>
    </form>

@endsection

@include('Auth.Layout.SignUpBodyTag')
