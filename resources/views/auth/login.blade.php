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

                    <x-text-input id="password" class="block mt-1 mb-4 w-full" type="password" name="password" required
                        autocomplete="current-password" />

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
                        <x-primary-button class="bg-sky-600 hover:bg-sky-700 ms-3 ">
                            {{ __('Log in') }}
                        </x-primary-button>
                    </form>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
