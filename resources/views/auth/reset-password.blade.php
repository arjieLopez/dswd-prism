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
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />
            <ul id="password-requirements" class="mt-2 text-xs text-gray-500 list-disc list-inside space-y-1">
                <li id="pw-length" class="transition-colors">At least 8 characters</li>
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
                    passwordInput.addEventListener('input', function() {
                        const val = passwordInput.value;
                        // Length
                        if (val.length >= 8) {
                            reqLength.classList.add('text-green-600');
                            reqLength.classList.remove('text-gray-500');
                        } else {
                            reqLength.classList.remove('text-green-600');
                            reqLength.classList.add('text-gray-500');
                        }
                        // Upper & lower
                        if (/[A-Z]/.test(val) && /[a-z]/.test(val)) {
                            reqUpperLower.classList.add('text-green-600');
                            reqUpperLower.classList.remove('text-gray-500');
                        } else {
                            reqUpperLower.classList.remove('text-green-600');
                            reqUpperLower.classList.add('text-gray-500');
                        }
                        // Number
                        if (/[0-9]/.test(val)) {
                            reqNumber.classList.add('text-green-600');
                            reqNumber.classList.remove('text-gray-500');
                        } else {
                            reqNumber.classList.remove('text-green-600');
                            reqNumber.classList.add('text-gray-500');
                        }
                        // Special
                        if (/[!@#$%^&*]/.test(val)) {
                            reqSpecial.classList.add('text-green-600');
                            reqSpecial.classList.remove('text-gray-500');
                        } else {
                            reqSpecial.classList.remove('text-green-600');
                            reqSpecial.classList.add('text-gray-500');
                        }
                    });
                });
            </script>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />

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
                    const lengthMet = val.length >= 8;
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
        </script>
    </form>
</x-guest-layout>
