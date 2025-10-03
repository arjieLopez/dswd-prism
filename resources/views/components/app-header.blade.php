<div class="flex items-center max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8 h-full">
    <a href="{{ $homeUrl ?? '/' }}">
        <img src="{{ asset('images/DSWD-Logo1.png') }}" alt="DSWD Logo" class="w-16">
    </a>
    <h2 class="p-4 font-bold text-xl text-gray-800 leading-tight tracking-wide">
        {{ $title ?? __('DSWD-PRISM') }}
    </h2>
    <span class="flex-1"></span>
    <x-notification-bell :recentActivities="$recentActivities" />
    <div class="p-2">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button
                    class="inline-flex items-center px-2 py-2 border border-transparent rounded-full text-gray-900 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                    aria-label="User menu">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </button>
            </x-slot>
            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-dropdown-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
    <h2 class="pr-4 font-semibold text-base text-gray-800 leading-tight">
        <div>
            {{ $userName ?? Auth::user()->first_name . (Auth::user()->middle_name ? ' ' . Auth::user()->middle_name : '') . ' ' . Auth::user()->last_name }}
        </div>
    </h2>
</div>
