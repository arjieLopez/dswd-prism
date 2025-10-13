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
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full"
                autocomplete="new-password" />
            <ul id="profile-password-requirements" class="mt-2 text-xs text-gray-500 list-disc list-inside space-y-1">
                <li id="profile-pw-length" class="transition-colors">At least 8 characters</li>
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
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full" autocomplete="new-password" />
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
</section>
