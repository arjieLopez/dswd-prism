<x-page-layout>
    <x-slot name="header">
        <a href="/admin">
            <img src="{{ asset('images/DSWD-Logo1.png') }}" alt="DSWD Logo" class="w-16">
        </a>
        <h2 class="p-4 font-bold text-xl text-gray-800 leading-tight tracking-wide">
            {{ __('DSWD-PRISM') }}
        </h2>
        <span class="flex-1"></span>
        <div class="p-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-7 h-7 inline-block align-middle">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
        </div>

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
                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>

        <h2 class="pr-4 font-semibold text-base text-gray-800 leading-tight">
            <div>{{ Auth::user()->name }}</div>
        </h2>
    </x-slot>

    {{-- Main Page --}}
    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Dashboard') }}
                </h2>
                {{-- <button
                    class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded-lg flex items-center transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Generate Report
                </button> --}}
            </div>
            <div class="grid grid-cols-3 row-span-2 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg col-span-2 row-span-2">
                    <div class="px-6 pt-6 font-semibold text-lg text-gray-900 tracking-wide">
                        {{ __('Monthly PRs and POs') }}
                    </div>
                    <div class="px-6 pt-6">
                        <canvas id="bar-chart"></canvas>

                        <script>
                            window.chartLabels = {!! json_encode($labels) !!};
                            window.chartPR = {!! json_encode($prData) !!};
                            window.chartPO = {!! json_encode($poData) !!};
                        </script>
                    </div>

                </div>
                <!-- Total PRs Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 flex flex-col justify-center">
                    <!-- h-64 p-6-->
                    <div class="px-8 py-6 flex items-center justify-between">
                        <div>
                            <div class="font-semibold text-gray-400 tracking-wide">
                                {{ __('Total PRs') }}
                            </div>
                            <div class="font-semibold text-2xl text-gray-700">
                                {{ $prCount }}
                            </div>
                            <div class="text-xs text-gray-400 tracking-wide">
                                <p><span
                                        class="{{ $prPercentageChange >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $prPercentageChange >= 0 ? '+' : '' }}{{ $prPercentageChange }}%</span>
                                    {{ __('from') }}</p>
                                <p>{{ __('last month') }}</p>
                            </div>
                        </div>
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center ml-4">
                            <i class="iconify w-8 h-8 text-blue-600" data-icon="mdi:file-document-outline"></i>
                        </div>
                    </div>

                    {{-- <div class="p-4 flex flex-col justify-center h-full">
                        <div class="flex items-start justify-between mb-6 h-full">
                            <div class="flex flex-col items-start justify-center h-full">
                                <div class="w-20 h-20 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                                <span class="text-lg font-medium text-gray-400 mb-1">{{ __('Total PRs') }}</span>
                                <span class="text-3xl font-extrabold text-gray-900 mb-1">{{ $totalPRs }}</span>
                                <span class="text-base text-gray-400">{{ __('vs last month') }}</span>
                            </div>
                            <div class="flex flex-col items-end justify-start h-full">
                                <span
                                    class="text-2xl font-bold {{ $prPercentageChange >= 0 ? 'text-green-600' : 'text-red-600' }} mb-2 mt-1">
                                    {{ $prPercentageChange >= 0 ? '+' : '' }}{{ $prPercentageChange }}%
                                </span>
                                <svg class="w-6 h-6 {{ $prPercentageChange >= 0 ? 'text-green-600' : 'text-red-600' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $prPercentageChange >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div> --}}

                </div>
                <!-- Total POs Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 flex flex-col justify-center">
                    <!--  h-64 p-6 -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-8 py-6 flex items-center justify-between">
                            <div>
                                <div class="font-semibold text-gray-400 tracking-wide">
                                    {{ __('Total POs') }}
                                </div>
                                <div class="font-semibold text-2xl text-gray-700">
                                    {{ $poCount }}
                                </div>
                                <div class="text-xs text-gray-400 tracking-wide">
                                    <p><span
                                            class="{{ $poPercentageChange >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $poPercentageChange >= 0 ? '+' : '' }}{{ $poPercentageChange }}%</span>
                                        {{ __('from') }}</p>
                                    <p>{{ __('last month') }}</p>
                                </div>
                            </div>
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center ml-4">
                                <i class="iconify w-8 h-8 text-green-600" data-icon="mdi:cart-outline"></i>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="p-4 flex flex-col justify-center h-full">
                        <div class="flex items-start justify-between mb-6 h-full">
                            <div class="flex flex-col items-start justify-center h-full">
                                <div class="w-20 h-20 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                                <span class="text-lg font-medium text-gray-400 mb-1">{{ __('Total POs') }}</span>
                                <span class="text-3xl font-extrabold text-gray-900 mb-1">{{ $totalPOs }}</span>
                                <span class="text-base text-gray-400">{{ __('vs last month') }}</span>
                            </div>
                            <div class="flex flex-col items-end justify-start h-full">
                                <span
                                    class="text-2xl font-bold {{ $poPercentageChange >= 0 ? 'text-green-600' : 'text-red-600' }} mb-2 mt-1">
                                    {{ $poPercentageChange >= 0 ? '+' : '' }}{{ $poPercentageChange }}%
                                </span>
                                <svg class="w-6 h-6 {{ $poPercentageChange >= 0 ? 'text-green-600' : 'text-red-600' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $poPercentageChange >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div> --}}
                </div>

            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-6 font-semibold text-lg text-gray-900 tracking-wide">
                    {{ __('Recent Activity') }}


                    <div class="px-6 pb-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Document') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Action') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Requesting Unit') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Status') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Date') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($recentActivities as $activity)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <div class="flex items-center">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $activity['type'] === 'PR' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }} mr-2">
                                                        {{ $activity['type'] }}
                                                    </span>
                                                    {{ $activity['document_number'] }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ $activity['action'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $activity['requesting_unit'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $activity['status_color'] }}">
                                                    {{ ucfirst(str_replace('_', ' ', $activity['status'])) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $activity['date']->diffForHumans() }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                                {{ __('No recent activities') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

</x-page-layout>
