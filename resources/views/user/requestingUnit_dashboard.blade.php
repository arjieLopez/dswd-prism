<x-page-layout>
    <x-slot name="header">
        <a href="/user">
            <img src="{{ asset('images/DSWD-Logo1.png') }}" alt="DSWD Logo" class="w-16">
        </a>
        <h2 class="p-4 font-bold text-xl text-gray-800 leading-tight tracking-wide">
            {{ __('DSWD-PRISM') }}
        </h2>
        <span class="flex-1"></span>
        {{-- Notification Start --}}
        <div class="p-4">
            <!-- Notification Bell with Indicator -->
            <div class="relative" x-data="{
                open: false,
                hasUnread: {{ $recentActivities->where('created_at', '>=', now()->subDays(1))->count() > 0 ? 'true' : 'false' }},
                markAsRead() {
                    this.hasUnread = false;
                    // Store in localStorage to persist across page reloads
                    localStorage.setItem('notificationsViewed', new Date().toISOString());
                }
            }" x-init="// Check if user has viewed notifications since last activity
            const lastViewed = localStorage.getItem('notificationsViewed');
            const lastActivity = '{{ $recentActivities->first() ? $recentActivities->first()->created_at->toISOString() : '' }}';
            if (lastViewed && lastActivity && new Date(lastViewed) > new Date(lastActivity)) {
                hasUnread = false;
            }">
                <button @click="open = !open; if(open && hasUnread) markAsRead();"
                    class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                    </svg>
                    <!-- Red indicator for new notifications - positioned at top-right -->
                    <span x-show="hasUnread" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-75"
                        class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center font-medium text-[10px] transform translate-x-1/2 -translate-y-1/2">
                        {{ $recentActivities->where('created_at', '>=', now()->subDays(1))->count() > 9 ? '9+' : $recentActivities->where('created_at', '>=', now()->subDays(1))->count() }}
                    </span>
                </button>

                <!-- Notification Dropdown -->
                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                    <div class="py-2">
                        <div class="px-4 py-2 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900">Recent Activities</h3>
                        </div>

                        @if ($recentActivities->count() > 0)
                            <div class="max-h-64 overflow-y-auto">
                                @foreach ($recentActivities as $activity)
                                    <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="w-8 h-8 rounded-full flex items-center justify-center {{ $activity->action_color }}">
                                                    <i class="iconify w-4 h-4"
                                                        data-icon="{{ $activity->action_icon }}"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $activity->description }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    @if ($activity->pr_number)
                                                        PR #{{ $activity->pr_number }}
                                                    @endif
                                                    @if ($activity->document_name)
                                                        - {{ $activity->document_name }}
                                                    @endif
                                                </p>
                                                <p class="text-xs text-gray-400">
                                                    {{ $activity->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="px-4 py-2 border-t border-gray-200">
                                <a href="{{ route('user.requests') }}"
                                    class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    View all activities →
                                </a>
                            </div>
                        @else
                            <div class="px-4 py-8 text-center">
                                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No recent activities</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {{-- Notification End --}}

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

    <!-- Main Body -->
    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Dashboard') }}
                </h2>
            </div>
            <div class="grid grid-cols-4 gap-6">
                <!-- Total PRs Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-8 py-6 flex items-center justify-between">
                        <div>
                            <div class="font-semibold text-gray-400 tracking-wide">
                                {{ __('Total PRs') }}
                            </div>
                            <div class="font-semibold text-2xl text-gray-700">
                                {{ $totalPRs }}
                            </div>
                            <div class="text-xs text-gray-400 tracking-wide">
                                <p><span
                                        class="{{ $totalPercentageChange >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $totalPercentageChange >= 0 ? '+' : '' }}{{ $totalPercentageChange }}%</span>
                                    {{ __('from') }}</p>
                                <p>{{ __('last month') }}</p>
                            </div>
                        </div>
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center ml-4">
                            <i class="iconify w-8 h-8 text-blue-600" data-icon="mdi:file-document-outline"></i>
                        </div>
                    </div>
                </div>

                <!-- Approved PRs Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-8 py-6 flex items-center justify-between">
                        <div>
                            <div class="font-semibold text-gray-400 tracking-wide">
                                {{ __('Approved PRs') }}
                            </div>
                            <div class="font-semibold text-2xl text-gray-700">
                                {{ $approvedPRs }}
                            </div>
                            <div class="text-xs text-gray-400 tracking-wide">
                                <p><span
                                        class="{{ $approvedPercentageChange >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $approvedPercentageChange >= 0 ? '+' : '' }}{{ $approvedPercentageChange }}%</span>
                                    {{ __('from') }}</p>
                                <p>{{ __('last month') }}</p>
                            </div>
                        </div>
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center ml-4">
                            <i class="iconify w-8 h-8 text-green-600" data-icon="mdi:check-circle-outline"></i>
                        </div>
                    </div>
                </div>

                <!-- Pending PRs Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-8 py-6 flex items-center justify-between">
                        <div>
                            <div class="font-semibold text-gray-400 tracking-wide">
                                {{ __('Pending PRs') }}
                            </div>
                            <div class="font-semibold text-2xl text-gray-700">
                                {{ $pendingPRs }}
                            </div>
                            <div class="text-xs text-gray-400 tracking-wide">
                                <p><span
                                        class="{{ $pendingPercentageChange >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $pendingPercentageChange >= 0 ? '+' : '' }}{{ $pendingPercentageChange }}%</span>
                                    {{ __('from') }}</p>
                                <p>{{ __('last month') }}</p>
                            </div>
                        </div>
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center ml-4">
                            <i class="iconify w-8 h-8 text-yellow-600" data-icon="mdi:clock-outline"></i>
                        </div>
                    </div>
                </div>

                <!-- Rejected PRs Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-8 py-6 flex items-center justify-between">
                        <div>
                            <div class="font-semibold text-gray-400 tracking-wide">
                                {{ __('Rejected PRs') }}
                            </div>
                            <div class="font-semibold text-2xl text-gray-700">
                                {{ $rejectedPRs }}
                            </div>
                            <div class="text-xs text-gray-400 tracking-wide">
                                <p><span
                                        class="{{ $rejectedPercentageChange >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $rejectedPercentageChange >= 0 ? '+' : '' }}{{ $rejectedPercentageChange }}%</span>
                                    {{ __('from') }}</p>
                                <p>{{ __('last month') }}</p>
                            </div>
                        </div>
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center ml-4">
                            <i class="iconify w-8 h-8 text-red-600" data-icon="mdi:close-circle-outline"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 pt-6 font-semibold text-lg text-gray-900 tracking-wide text-center">
                    {{ __('Monthly Purchase Request Status Overview') }}
                </div>
                <div class="px-6 py-6 font-semibold text-lg text-gray-900 tracking-wide">
                    <canvas id="prLineChart"></canvas>

                    <script>
                        window.prChartLabels = @json($labels);
                        window.userApproveChartData = @json($approvePR);
                        window.userPendingChartData = @json($pendingPR);
                        window.userRejectChartData = @json($rejectPR);
                    </script>
                </div>
            </div>
            {{-- <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-6 font-semibold text-lg text-gray-900 tracking-wide">
                    {{ __('Recent Activity') }}
                </div>
                <div class="px-6 pb-6">
                    @if ($recentActivities->count() > 0)
                        <div class="space-y-4">
                            @foreach ($recentActivities as $activity)
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-8 h-8 rounded-full flex items-center justify-center {{ $activity->action_color }}">
                                            <i class="iconify w-4 h-4" data-icon="{{ $activity->action_icon }}"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $activity->description }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            @if ($activity->pr_number)
                                                PR #{{ $activity->pr_number }}
                                            @endif
                                            @if ($activity->document_name)
                                                - {{ $activity->document_name }}
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No recent activities</p>
                        </div>
                    @endif
                </div>
            </div> --}}

        </div>
    </div>

</x-page-layout>
