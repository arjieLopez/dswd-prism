<x-page-layout>
    <x-slot name="header">
        <x-app-header :homeUrl="route(
            auth()->user()->role == 'admin' ? 'admin' : (auth()->user()->role == 'staff' ? 'staff' : 'user'),
        )" :userName="Auth::user()->first_name .
            (Auth::user()->middle_name ? ' ' . Auth::user()->middle_name : '') .
            ' ' .
            Auth::user()->last_name" :recentActivities="$recentActivities ?? collect()" />
    </x-slot>
    <div class="w-full min-h-screen bg-gray-100 flex items-center justify-center py-8">
        <div class="w-full max-w-2xl mx-auto rounded-lg border border-gray-100 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="iconify w-5 h-5 text-blue-500" data-icon="mdi:bell"></i>
                    Notifications
                </h2>
                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center gap-1 px-2 py-1 rounded bg-gray-50 hover:bg-gray-100 text-gray-600 font-medium transition text-xs">
                    <i class="iconify w-4 h-4" data-icon="mdi:arrow-left"></i>
                    Back
                </a>
            </div>

            <!-- Filter Buttons -->
            <div class="mb-4 flex flex-wrap items-center gap-2 bg-gray-50 rounded px-3 py-2" x-data="{ showDate: false }">
                <form method="GET" action="{{ route('notifications.all') }}" class="flex gap-2">
                    <input type="hidden" name="filter_type" value="all">
                    <button type="submit"
                        class="relative bg-gradient-to-r {{ $filterType === 'all' ? 'from-blue-600 to-blue-700 text-white' : 'from-white to-blue-50 text-blue-600 border border-blue-600' }} px-3 py-1 rounded-lg text-xs font-medium
                   hover:from-blue-700 hover:to-blue-800 hover:shadow-lg hover:scale-105
                   active:from-blue-800 active:to-blue-900 active:scale-95 active:shadow-inner
                   transition-all duration-200 ease-in-out transform
                   before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                   hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200
                               {{ $filterType !== 'all' ? 'hover:text-white' : '' }}">All</button>
                </form>
                <form method="GET" action="{{ route('notifications.all') }}" class="flex gap-2">
                    <input type="hidden" name="filter_type" value="today">
                    <button type="submit"
                        class="relative bg-gradient-to-r {{ $filterType === 'today' ? 'from-blue-600 to-blue-700 text-white' : 'from-white to-blue-50 text-blue-600 border border-blue-600' }} px-3 py-1 rounded-lg text-xs font-medium
                   hover:from-blue-700 hover:to-blue-800 hover:shadow-lg hover:scale-105
                   active:from-blue-800 active:to-blue-900 active:scale-95 active:shadow-inner
                   transition-all duration-200 ease-in-out transform
                   before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                   hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200
                               {{ $filterType !== 'today' ? 'hover:text-white' : '' }}">Today</button>
                </form>
                <form method="GET" action="{{ route('notifications.all') }}" class="flex gap-2">
                    <input type="hidden" name="filter_type" value="this_week">
                    <button type="submit"
                        class="relative bg-gradient-to-r {{ $filterType === 'this_week' ? 'from-blue-600 to-blue-700 text-white' : 'from-white to-blue-50 text-blue-600 border border-blue-600' }} px-3 py-1 rounded-lg text-xs font-medium
                   hover:from-blue-700 hover:to-blue-800 hover:shadow-lg hover:scale-105
                   active:from-blue-800 active:to-blue-900 active:scale-95 active:shadow-inner
                   transition-all duration-200 ease-in-out transform
                   before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                   hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200
                   {{ $filterType !== 'this_week' ? 'hover:text-white' : '' }}">This
                        Week</button>
                </form>
                <form method="GET" action="{{ route('notifications.all') }}" class="flex gap-2">
                    <input type="hidden" name="filter_type" value="earlier">
                    <button type="submit"
                        class="relative bg-gradient-to-r {{ $filterType === 'earlier' ? 'from-blue-600 to-blue-700 text-white' : 'from-white to-blue-50 text-blue-600 border border-blue-600' }} px-3 py-1 rounded-lg text-xs font-medium
                   hover:from-blue-700 hover:to-blue-800 hover:shadow-lg hover:scale-105
                   active:from-blue-800 active:to-blue-900 active:scale-95 active:shadow-inner
                   transition-all duration-200 ease-in-out transform
                   before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                   hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200
                               {{ $filterType !== 'earlier' ? 'hover:text-white' : '' }}">Earlier</button>
                </form>
                <!-- Custom Date Range Dropdown -->
                <div class="relative">
                    <button type="button" @click="showDate = !showDate"
                        class="relative bg-gradient-to-r {{ $filterType === 'custom' ? 'from-blue-600 to-blue-700 text-white' : 'from-white to-blue-50 text-blue-600 border border-blue-600' }} px-3 py-1 rounded-lg text-xs font-medium flex items-center gap-1
                   hover:from-blue-700 hover:to-blue-800 hover:shadow-lg hover:scale-105
                   active:from-blue-800 active:to-blue-900 active:scale-95 active:shadow-inner
                   transition-all duration-200 ease-in-out transform
                   before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                   hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200
                   {{ $filterType !== 'custom' ? 'hover:text-white' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        @if ($filterType === 'custom' && $dateFrom && $dateTo)
                            {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} -
                            {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
                        @else
                            Custom Date
                        @endif
                    </button>
                    <div x-show="showDate" @click.away="showDate = false" x-transition
                        class="absolute left-0 mt-2 w-72 bg-white rounded-md shadow-lg z-50 border border-gray-200 p-4">
                        <form method="GET" action="{{ route('notifications.all') }}" class="space-y-3">
                            <input type="hidden" name="filter_type" value="custom">
                            <label for="date_from" class="block text-xs text-gray-600 font-medium">From:</label>
                            <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:ring focus:ring-blue-200 focus:border-blue-300">
                            <label for="date_to" class="block text-xs text-gray-600 font-medium">To:</label>
                            <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:ring focus:ring-blue-200 focus:border-blue-300">
                            <div class="flex gap-2">
                                <button type="submit"
                                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded bg-blue-600 text-white text-xs font-semibold shadow hover:bg-blue-700 transition w-full">
                                    <i class="iconify w-4 h-4" data-icon="mdi:filter"></i> Apply Filter
                                </button>
                                @if ($dateFrom && $dateTo)
                                    <a href="{{ route('notifications.all') }}"
                                        class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded bg-gray-600 text-white text-xs font-semibold shadow hover:bg-gray-700 transition w-full">
                                        <i class="iconify w-4 h-4" data-icon="mdi:delete"></i> Clear Dates
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
                {{-- Reset button removed as requested --}}
            </div>
            @if ($allActivities->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach ($allActivities as $activity)
                        <div class="py-2 flex items-start space-x-3 group hover:bg-gray-50 transition rounded">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-7 h-7 rounded-full flex items-center justify-center {{ $activity->action_color }}">
                                    <i class="iconify w-4 h-4" data-icon="{{ $activity->action_icon }}"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 mb-0.5">{{ $activity->description }}</p>
                                <p class="text-xs text-gray-500 mb-0.5">
                                    @if ($activity->pr_number)
                                        <span
                                            class="inline-block px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-medium">PR
                                            #{{ $activity->pr_number }}</span>
                                    @endif
                                    @if ($activity->document_name)
                                        <span
                                            class="inline-block px-2 py-0.5 rounded bg-gray-50 text-gray-700 font-medium">{{ $activity->document_name }}</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-400">{{ $activity->created_at->format('M j, Y g:i A') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if ($allActivities->total() > 10)
                    <div class="mt-4 flex justify-center">
                        <div class="flex items-center space-x-1">
                            @if ($allActivities->onFirstPage())
                                <span class="px-3 py-2 text-gray-400 cursor-not-allowed">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </span>
                            @else
                                <a href="{{ $allActivities->appends(request()->query())->previousPageUrl() }}"
                                    class="px-3 py-2 text-gray-600 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </a>
                            @endif

                            @php
                                $start = max(1, $allActivities->currentPage() - 2);
                                $end = min($allActivities->lastPage(), $allActivities->currentPage() + 2);
                                if ($end - $start < 4) {
                                    if ($start == 1) {
                                        $end = min($allActivities->lastPage(), $start + 4);
                                    } else {
                                        $start = max(1, $end - 4);
                                    }
                                }
                            @endphp

                            @if ($start > 1)
                                <a href="{{ $allActivities->appends(request()->query())->url(1) }}"
                                    class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">1</a>
                                @if ($start > 2)
                                    <span class="px-2 py-2 text-gray-400">...</span>
                                @endif
                            @endif

                            @for ($page = $start; $page <= $end; $page++)
                                @if ($page == $allActivities->currentPage())
                                    <span
                                        class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md">{{ $page }}</span>
                                @else
                                    <a href="{{ $allActivities->appends(request()->query())->url($page) }}"
                                        class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">{{ $page }}</a>
                                @endif
                            @endfor

                            @if ($end < $allActivities->lastPage())
                                @if ($end < $allActivities->lastPage() - 1)
                                    <span class="px-2 py-2 text-gray-400">...</span>
                                @endif
                                <a href="{{ $allActivities->appends(request()->query())->url($allActivities->lastPage()) }}"
                                    class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">{{ $allActivities->lastPage() }}</a>
                            @endif

                            @if ($allActivities->hasMorePages())
                                <a href="{{ $allActivities->appends(request()->query())->nextPageUrl() }}"
                                    class="px-3 py-2 text-gray-600 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            @else
                                <span class="px-3 py-2 text-gray-400 cursor-not-allowed">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                <div class="py-10 text-center">
                    <svg class="mx-auto h-8 w-8 text-gray-200" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <p class="mt-3 text-sm text-gray-400 font-medium">No notifications found.</p>
                </div>
            @endif
        </div>
    </div>

</x-page-layout>
