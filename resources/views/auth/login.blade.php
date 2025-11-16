<x-guest-layout>
    <div class="flex items-center justify-center">
        <div class="flex flex-col items-center w-full">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div>
                <h1 class="text-center text-2xl font-semibold mb-6">DSWD-PRISM Login</h1>
            </div>
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                        required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <div class="relative">
                        <x-text-input id="password" class="block mt-1 mb-4 w-full pr-10" type="password"
                            name="password" required autocomplete="current-password" />
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
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <!-- <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div> -->

                <div class="flex items-center justify-end mt-6">
                    @if (Route::has('password.request'))
                        <a class="text-sm text-sky-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <input type="hidden" name="recaptcha_token" id="recaptcha_token">
                    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.key') }}"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const form = document.querySelector('form[action="{{ route('login') }}"]');
                            form.addEventListener('submit', function(event) {
                                event.preventDefault();
                                grecaptcha.ready(function() {
                                    grecaptcha.execute('{{ config('services.recaptcha.key') }}', {
                                        action: 'login'
                                    }).then(function(token) {
                                        document.getElementById('recaptcha_token').value = token;
                                        form.submit();
                                    });
                                });
                            });
                        });
                    </script>

                    <form id="myForm" method="POST">
                        <x-primary-button class="ms-3 ">
                            {{ __('Log in') }}
                        </x-primary-button>
                    </form>
                </div>
            </form>

            <script>
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
        </div>
    </div>
</x-guest-layout>
