<x-page-layout>
    <x-slot name="header">
        <x-app-header :homeUrl="route('user')" :title="$pageTitle ?? __('DSWD-PRISM')" :userName="Auth::user()->first_name .
            (Auth::user()->middle_name ? ' ' . Auth::user()->middle_name : '') .
            ' ' .
            Auth::user()->last_name" :recentActivities="$recentActivities ?? collect()" />
    </x-slot>

    <!-- Main Body -->
    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Dashboard') }}
                </h2>
            </div>
            <div class="grid grid-cols-5 gap-4">
                <!-- Total PRs Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 flex flex-col justify-center">
                    <div class="px-8 py-6 flex items-center justify-between">
                        <div>
                            <div class="w-20 font-semibold text-gray-400 tracking-wide">
                                {{ __('Draft') }}
                            </div>
                            <div class="font-semibold text-2xl text-gray-700">
                                {{ $draftPRs }}
                            </div>
                            <div class="text-xs text-gray-400 tracking-wide">
                                <p><span
                                        class="{{ $draftPercentageChange >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $draftPercentageChange >= 0 ? '+' : '' }}{{ $draftPercentageChange }}%</span>
                                    {{ __('from') }}</p>
                                <p>{{ __('last month') }}</p>
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center ml-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="2em" height="2em" viewBox="0 0 24 24">
                                <path fill="#4b5563"
                                    d="M8 16h8v2H8zm0-4h8v2H8zm6-10H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8zm4 18H6V4h7v5h5z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pending PRs Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 flex flex-col justify-center">
                    <div class="px-8 py-6 flex items-center justify-between">
                        <div>
                            <div class="w-20 font-semibold text-gray-400 tracking-wide">
                                {{ __('Pending') }}
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
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center ml-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="2em" height="2em" viewBox="0 0 24 24">
                                <path fill="#ca8a04"
                                    d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2M12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8s8 3.58 8 8s-3.58 8-8 8m.5-13H11v6l5.25 3.15l.75-1.23l-4.5-2.67z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Approved PRs Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 flex flex-col justify-center">
                    <div class="px-8 py-6 flex items-center justify-between">
                        <div>
                            <div class="w-20 font-semibold text-gray-400 tracking-wide">
                                {{ __('Approved') }}
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
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center ml-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="2em" height="2em" viewBox="0 0 24 24">
                                <path fill="#16a34a"
                                    d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8s8 3.59 8 8s-3.59 8-8 8m4.59-12.42L10 14.17l-2.59-2.58L6 13l4 4l8-8z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Rejected PRs Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 flex flex-col justify-center">
                    <div class="px-8 py-6 flex items-center justify-between">
                        <div>
                            <div class="w-20 font-semibold text-gray-400 tracking-wide">
                                {{ __('Rejected') }}
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
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center ml-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="2em" height="2em" viewBox="0 0 24 24">
                                <path fill="#dc2626"
                                    d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10s10-4.47 10-10S17.53 2 12 2m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8s8 3.59 8 8s-3.59 8-8 8m3.59-13L12 10.59L8.41 7L7 8.41L10.59 12L7 15.59L8.41 17L12 13.41L15.59 17L17 15.59L13.41 12L17 8.41z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Completed PRs Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 flex flex-col justify-center">
                    <div class="px-8 py-6 flex items-center justify-between">
                        <div>
                            <div class="w-20 font-semibold text-gray-400 tracking-wide">
                                {{ __('Completed') }}
                            </div>
                            <div class="font-semibold text-2xl text-gray-700">
                                {{ $completedPRs }}
                            </div>
                            <div class="text-xs text-gray-400 tracking-wide">
                                <p><span
                                        class="{{ $completedPercentageChange >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $completedPercentageChange >= 0 ? '+' : '' }}{{ $completedPercentageChange }}%</span>
                                    {{ __('from') }}</p>
                                <p>{{ __('last month') }}</p>
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center ml-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="2em" height="2em" viewBox="0 0 24 24">
                                <path fill="#2563eb"
                                    d="m18 9l-1.41-1.42L10 14.17l-2.59-2.58L6 13l4 4zm1-6h-4.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H5c-.14 0-.27.01-.4.04a2.01 2.01 0 0 0-1.44 1.19c-.1.23-.16.49-.16.77v14c0 .27.06.54.16.78s.25.45.43.64c.27.27.62.47 1.01.55c.13.02.26.03.4.03h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2m-7-.25c.41 0 .75.34.75.75s-.34.75-.75.75s-.75-.34-.75-.75s.34-.75.75-.75M19 19H5V5h14z" />
                            </svg>
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
                        window.userCompletedChartData = @json($completedPR);
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
