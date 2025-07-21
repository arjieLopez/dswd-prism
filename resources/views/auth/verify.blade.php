<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <div class="mb-4 text-sm text-gray-600">
        We have sent a code to your email, if you haven't received it, click <a class="underline"
            href="{{ route('verify.resend') }}">here</a>.
        @session('success')
            <p class="text-green-500 p-2 bg-green-50 font-semibold">{{ $value }}</p>
        @endsession
    </div>


    <form method="POST" action="{{ route('verify') }}">
        @csrf

        <div>
            <x-input-label for="twofactor_code" :value="__('Code')" />
            <x-text-input id="twofactor_code" class="block mt-1 w-full" type="text" name="twofactor_code"
                :value="old('twofactor_code')" autofocus />
            @error('twofactor_code')
                <x-input-error :messages="$message" class="mt-2" />
            @enderror
            @if (session('error'))
                <x-input-error :messages="session('error')" class="mt-2" />
            @endif
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="bg-sky-600 hover:bg-sky-700">
                {{ __('Submit') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
