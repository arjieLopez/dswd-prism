<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800">Reset Your Password</h2>
        <p class="text-gray-500 mt-2">Enter your new password below to reset your account.</p>
        <!-- Minimalist password requirements will be placed under the password field -->
    </div>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)"
                required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pr-10" type="password" name="password" required
                    autocomplete="new-password" />
                <button type="button" onclick="togglePasswordVisibility('password')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg id="password_eye_icon" class="w-5 h-5" fill="none" stroke="currentColor"
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
            <div id="password-strength" class="mt-2 hidden">
                <div class="flex items-center gap-2">
                    <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div id="strength-bar" class="h-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <span id="strength-text" class="text-xs font-medium"></span>
                </div>
            </div>

            <ul id="password-requirements" class="mt-2 text-xs text-gray-500 list-disc list-inside space-y-1">
                <li id="pw-length" class="transition-colors">At least 12 characters</li>
                <li id="pw-upper-lower" class="transition-colors">Uppercase &amp; lowercase letters</li>
                <li id="pw-number" class="transition-colors">At least one number</li>
                <li id="pw-special" class="transition-colors">At least one special character (!@#$%^&amp;*)</li>
            </ul>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const passwordInput = document.getElementById('password');
                    const reqLength = document.getElementById('pw-length');
                    const reqUpperLower = document.getElementById('pw-upper-lower');
                    const reqNumber = document.getElementById('pw-number');
                    const reqSpecial = document.getElementById('pw-special');
                    const strengthIndicator = document.getElementById('password-strength');
                    const strengthBar = document.getElementById('strength-bar');
                    const strengthText = document.getElementById('strength-text');

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
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <div class="relative">
                <x-text-input id="password_confirmation" class="block mt-1 w-full pr-10" type="password"
                    name="password_confirmation" required autocomplete="new-password" />
                <button type="button" onclick="togglePasswordVisibility('password_confirmation')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg id="password_confirmation_eye_icon" class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button id="reset-save-btn">
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const passwordInput = document.getElementById('password');
                const saveBtn = document.getElementById('reset-save-btn');
                const reqLength = document.getElementById('pw-length');
                const reqUpperLower = document.getElementById('pw-upper-lower');
                const reqNumber = document.getElementById('pw-number');
                const reqSpecial = document.getElementById('pw-special');

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
</x-guest-layout>
