<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <div class="relative">
                <x-text-input id="update_password_current_password" name="current_password" type="password"
                    class="mt-1 block w-full pr-10" autocomplete="current-password" />
                <button type="button" onclick="togglePasswordVisibility('update_password_current_password')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg id="update_password_current_password_eye_icon" class="w-5 h-5" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <div class="relative">
                <x-text-input id="update_password_password" name="password" type="password"
                    class="mt-1 block w-full pr-10" autocomplete="new-password" />
                <button type="button" onclick="togglePasswordVisibility('update_password_password')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg id="update_password_password_eye_icon" class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Password Strength Indicator -->
            <div id="profile-password-strength" class="mt-2 hidden">
                <div class="flex items-center gap-2">
                    <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div id="profile-strength-bar" class="h-full transition-all duration-300" style="width: 0%">
                        </div>
                    </div>
                    <span id="profile-strength-text" class="text-xs font-medium"></span>
                </div>
            </div>

            <ul id="profile-password-requirements" class="mt-2 text-xs text-gray-500 list-disc list-inside space-y-1">
                <li id="profile-pw-length" class="transition-colors">At least 12 characters</li>
                <li id="profile-pw-upper-lower" class="transition-colors">Uppercase &amp; lowercase letters</li>
                <li id="profile-pw-number" class="transition-colors">At least one number</li>
                <li id="profile-pw-special" class="transition-colors">At least one special character (!@#$%^&amp;*)</li>
            </ul>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const passwordInput = document.getElementById('update_password_password');
                    const reqLength = document.getElementById('profile-pw-length');
                    const reqUpperLower = document.getElementById('profile-pw-upper-lower');
                    const reqNumber = document.getElementById('profile-pw-number');
                    const reqSpecial = document.getElementById('profile-pw-special');
                    const strengthIndicator = document.getElementById('profile-password-strength');
                    const strengthBar = document.getElementById('profile-strength-bar');
                    const strengthText = document.getElementById('profile-strength-text');

                    passwordInput.addEventListener('input', function() {
                        const val = passwordInput.value;

                        // Show/hide strength indicator
                        if (val.length > 0) {
                            strengthIndicator.classList.remove('hidden');
                        } else {
                            strengthIndicator.classList.add('hidden');
                        }

                        // Calculate password strength
                        let strength = 0;
                        let strengthLabel = '';
                        let strengthColor = '';

                        // Length check
                        const lengthMet = val.length >= 12;
                        if (lengthMet) {
                            reqLength.classList.add('text-green-600');
                            reqLength.classList.remove('text-gray-500');
                            strength += 25;
                        } else {
                            reqLength.classList.remove('text-green-600');
                            reqLength.classList.add('text-gray-500');
                            if (val.length >= 8) strength += 15;
                        }

                        // Upper & lower case
                        const upperLowerMet = /[A-Z]/.test(val) && /[a-z]/.test(val);
                        if (upperLowerMet) {
                            reqUpperLower.classList.add('text-green-600');
                            reqUpperLower.classList.remove('text-gray-500');
                            strength += 25;
                        } else {
                            reqUpperLower.classList.remove('text-green-600');
                            reqUpperLower.classList.add('text-gray-500');
                        }

                        // Number
                        const numberMet = /[0-9]/.test(val);
                        if (numberMet) {
                            reqNumber.classList.add('text-green-600');
                            reqNumber.classList.remove('text-gray-500');
                            strength += 25;
                        } else {
                            reqNumber.classList.remove('text-green-600');
                            reqNumber.classList.add('text-gray-500');
                        }

                        // Special character
                        const specialMet = /[!@#$%^&*]/.test(val);
                        if (specialMet) {
                            reqSpecial.classList.add('text-green-600');
                            reqSpecial.classList.remove('text-gray-500');
                            strength += 25;
                        } else {
                            reqSpecial.classList.remove('text-green-600');
                            reqSpecial.classList.add('text-gray-500');
                        }

                        // Set strength level
                        if (strength <= 25) {
                            strengthLabel = 'Weak';
                            strengthColor = 'bg-red-500';
                        } else if (strength <= 50) {
                            strengthLabel = 'Fair';
                            strengthColor = 'bg-orange-500';
                        } else if (strength <= 75) {
                            strengthLabel = 'Good';
                            strengthColor = 'bg-yellow-500';
                        } else {
                            strengthLabel = 'Strong';
                            strengthColor = 'bg-green-500';
                        }

                        // Update strength bar
                        strengthBar.style.width = strength + '%';
                        strengthBar.className = 'h-full transition-all duration-300 ' + strengthColor;
                        strengthText.textContent = strengthLabel;
                        strengthText.className = 'text-xs font-medium ' + strengthColor.replace('bg-', 'text-');
                    });
                });
            </script>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <div class="relative">
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                    class="mt-1 block w-full pr-10" autocomplete="new-password" />
                <button type="button" onclick="togglePasswordVisibility('update_password_password_confirmation')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg id="update_password_password_confirmation_eye_icon" class="w-5 h-5" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button id="profile-save-btn">{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @endif
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const passwordInput = document.getElementById('update_password_password');
                const saveBtn = document.getElementById('profile-save-btn');
                const reqLength = document.getElementById('profile-pw-length');
                const reqUpperLower = document.getElementById('profile-pw-upper-lower');
                const reqNumber = document.getElementById('profile-pw-number');
                const reqSpecial = document.getElementById('profile-pw-special');

                function checkRequirements() {
                    const val = passwordInput.value;
                    const lengthMet = val.length >= 12;
                    const upperLowerMet = /[A-Z]/.test(val) && /[a-z]/.test(val);
                    const numberMet = /[0-9]/.test(val);
                    const specialMet = /[!@#$%^&*]/.test(val);
                    if (lengthMet && upperLowerMet && numberMet && specialMet) {
                        saveBtn.removeAttribute('disabled');
                        saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    } else {
                        saveBtn.setAttribute('disabled', 'disabled');
                        saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                }
                passwordInput.addEventListener('input', checkRequirements);
                checkRequirements(); // Initial state
            });

            // Password visibility toggle function
            window.togglePasswordVisibility = function(inputId) {
                const input = document.getElementById(inputId);
                const icon = document.getElementById(inputId + '_eye_icon');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                    `;
                } else {
                    input.type = 'password';
                    icon.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    `;
                }
            };
        </script>
    </form>
</section>
