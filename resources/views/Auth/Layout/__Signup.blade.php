<div class="field">
    <label for="username" class="placeholder-moves-up-container">
        <input value="{{ old('username') }}" autocomplete="off" type="text" name="username" id="username" required="true"
            class="input-area" oninput="movePlaceholderUp(this)">
        <span class="placeholder-moves-up">Username</span>
        <div class="field-error-message {{ $errors->has('username') ? '' : 'd-none' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="text-red me-1 bi bi-exclamation-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                <path
                    d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
            </svg>
            {{ $errors->first('username') }}
        </div>
    </label>
</div>

<div class="field">
    <label for="email" class="placeholder-moves-up-container">
        <input value="{{ old('email') }}" autocomplete="off" type="email" name="email" id="email"
            required="true" class="input-area" oninput="movePlaceholderUp(this)">
        <span class="placeholder-moves-up">Email address</span>
        <div class="field-error-message {{ $errors->has('email') ? '' : 'd-none' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="text-red me-1 bi bi-exclamation-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                <path
                    d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
            </svg>
            {{ $errors->first('email') }}
        </div>
    </label>
</div>


<div class="field password">
    <label for="password" class="placeholder-moves-up-container">
        <input autocomplete="off" type="password" name="password" id="password" minlength="6" maxlength="24"
            required="true" class="input-area" oninput="movePlaceholderUp(this)">
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
        <div class="field-error-message {{ $errors->has('password') ? '' : 'd-none' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="text-red me-1 bi bi-exclamation-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                <path
                    d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
            </svg>
            {{ $errors->first('password') }}
        </div>
    </label>
</div>

<div class="field password">
    <label for="confirmPassword" class="placeholder-moves-up-container">
        <input autocomplete="new-password" type="password" name="confirmPassword" id="confirmPassword" required="true"
            class="input-area" oninput="movePlaceholderUp(this)">
        <span class="placeholder-moves-up">Confirm password</span>
        <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword')">
            <!-- Show Password Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="eye-icon" width="20" height="20"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
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
        <div class="field-error-message {{ $errors->has('confirmPassword') ? '' : 'd-none' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="text-red me-1 bi bi-exclamation-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                <path
                    d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
            </svg>
            {{ $errors->first('confirmPassword') }}
        </div>
    </label>
</div>
