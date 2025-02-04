<div class="field">
    <label for="email" class="placeholder-moves-up-container">
        <input  novalidate  autocomplete="off" type="email" name="email" id="email" required="true" class="input-area" oninput="movePlaceholderUp(this)">
        <span class="placeholder-moves-up">Email</span>
        <div class="field-error-message d-none" id="email-error"></div>
    </label>
</div>

<div class="field password">
    <label for="password" class="placeholder-moves-up-container mb-2">
        <input  novalidate  autocomplete="new-password" type="password" name="password" id="password" minlength="6" maxlength="24" required="true"
            class="input-area" oninput="movePlaceholderUp(this)">
        <span class="placeholder-moves-up">Password</span>
        <i class="fa fa-eye" id="togglePassword" onclick="togglePassword('password', 'togglePassword')" style="cursor: pointer; margin-top: 10px"></i>
        <div class="field-error-message d-none" id="password-error"></div>
    </label>
</div>