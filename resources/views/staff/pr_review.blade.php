@php
    use Illuminate\Support\Str;
@endphp

<x-page-layout>
    <x-slot name="header">
        <x-app-header :homeUrl="route('staff')" :title="$pageTitle ?? __('DSWD-PRISM')" :userName="Auth::user()->first_name .
            (Auth::user()->middle_name ? ' ' . Auth::user()->middle_name : '') .
            ' ' .
            Auth::user()->last_name" :recentActivities="$recentActivities ?? collect()" />
    </x-slot>

    <!-- Main Content -->
    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Submitted Purchase Request') }}
                </h2>
            </div>

            <!-- Search and Filter Controls -->
            <div class="bg-white overflow-visible shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2">
                    <form method="GET" action="{{ route('staff.pr_review') }}"
                        class="flex items-center gap-2 w-full md:w-auto">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" name="search" placeholder="Search PRs..."
                                value="{{ request('search') }}"
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full md:w-64 placeholder-gray-400">
                        </div>
                        <!-- Preserve other query parameters -->
                        @if (request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        @if (request('date_from'))
                            <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                        @endif
                        @if (request('date_to'))
                            <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                        @endif
                        <button type="submit"
                            class="relative bg-gradient-to-r from-blue-500 to-blue-600 text-white font-bold py-2 px-4 rounded-lg
                                   hover:from-blue-600 hover:to-blue-700 hover:shadow-lg hover:scale-105
                                   active:from-blue-700 active:to-blue-800 active:scale-95 active:shadow-inner
                                   transition-all duration-200 ease-in-out transform
                                   before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                                   hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Search
                        </button>
                    </form>

                    <div class="flex items-center gap-2">
                        <div class="relative" x-data="{ open: false, activeTab: 'status' }">
                            <button @click="open = !open"
                                class="relative flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white
                                       hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 hover:border-gray-400 hover:shadow-lg hover:scale-105
                                       active:from-gray-100 active:to-gray-200 active:scale-95 active:shadow-inner
                                       transition-all duration-200 ease-in-out transform
                                       before:absolute before:inset-0 before:bg-gray-600 before:opacity-0 before:rounded-lg
                                       hover:before:opacity-5 active:before:opacity-10 before:transition-opacity before:duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 6h18M4 14h16M4 18h16">
                                    </path>
                                </svg>
                                Filter
                            </button>
                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-50 border border-gray-200">

                                <!-- Filter Tabs -->
                                <div class="flex border-b border-gray-200">
                                    <button @click="activeTab = 'status'"
                                        :class="activeTab === 'status' ? 'bg-blue-50 text-blue-700 border-blue-500' :
                                            'text-gray-500 hover:text-gray-700'"
                                        class="flex-1 px-4 py-3 text-sm font-medium border-b-2 border-transparent">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                            </path>
                                        </svg>
                                        Status
                                    </button>
                                    <button @click="activeTab = 'date'"
                                        :class="activeTab === 'date' ? 'bg-blue-50 text-blue-700 border-blue-500' :
                                            'text-gray-500 hover:text-gray-700'"
                                        class="flex-1 px-4 py-3 text-sm font-medium border-b-2 border-transparent">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        Date Range
                                    </button>
                                </div>

                                <!-- Status Filter Content -->
                                <div x-show="activeTab === 'status'" class="p-2">
                                    <ul>
                                        <li>
                                            <a href="{{ route('staff.pr_review', array_filter(['search' => request('search'), 'date_from' => request('date_from'), 'date_to' => request('date_to')])) }}"
                                                class="block px-4 py-2 text-gray-700 hover:bg-blue-100 rounded {{ !request('status') || request('status') == 'all' ? 'font-bold text-blue-600 bg-blue-50' : '' }}">
                                                All Statuses
                                            </a>
                                        </li>
                                        @php
                                            $statusDisplayMap = [
                                                'pending' => 'Pending',
                                                'approved' => 'Approved',
                                                'rejected' => 'Rejected',
                                                'po_generated' => 'PO Generated',
                                                'completed' => 'Completed',
                                            ];
                                        @endphp
                                        @foreach ($statuses as $status)
                                            <li>
                                                <a href="{{ route('staff.pr_review', array_filter(['status' => $status->name, 'search' => request('search'), 'date_from' => request('date_from'), 'date_to' => request('date_to')])) }}"
                                                    class="block px-4 py-2 text-gray-700 hover:bg-blue-100 rounded {{ request('status') == $status->name ? 'font-bold text-blue-600 bg-blue-50' : '' }}">
                                                    {{ $status->display_name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <!-- Date Filter Content -->
                                <div x-show="activeTab === 'date'" class="p-4">
                                    <form method="GET" action="{{ route('staff.pr_review') }}" class="space-y-4">
                                        <!-- Preserve existing parameters -->
                                        @if (request('search'))
                                            <input type="hidden" name="search" value="{{ request('search') }}">
                                        @endif
                                        @if (request('status'))
                                            <input type="hidden" name="status" value="{{ request('status') }}">
                                        @endif

                                        <div class="space-y-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">From
                                                    Date</label>
                                                <input type="date" name="date_from"
                                                    value="{{ request('date_from') }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">To
                                                    Date</label>
                                                <input type="date" name="date_to"
                                                    value="{{ request('date_to') }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                            </div>
                                        </div>

                                        <div class="flex gap-2 pt-2">
                                            <button type="submit"
                                                class="relative flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-bold py-2 px-4 rounded-lg text-sm
                                                       hover:from-blue-600 hover:to-blue-700 hover:shadow-lg hover:scale-105
                                                       active:from-blue-700 active:to-blue-800 active:scale-95 active:shadow-inner
                                                       transition-all duration-200 ease-in-out transform
                                                       before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                                                       hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                                                </svg>
                                                Apply Filter
                                            </button>
                                            @if (request('date_from') || request('date_to'))
                                                <a href="{{ route('staff.pr_review', array_filter(['search' => request('search'), 'status' => request('status')])) }}"
                                                    class="relative flex-1 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-bold py-2 px-4 rounded-lg text-sm text-center
                                                           hover:from-gray-600 hover:to-gray-700 hover:shadow-lg hover:scale-105
                                                           active:from-gray-700 active:to-gray-800 active:scale-95 active:shadow-inner
                                                           transition-all duration-200 ease-in-out transform inline-block
                                                           before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                                                           hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                                                    <svg class="w-3 h-3 inline mr-1" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                    Clear Dates
                                                </a>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="relative" id="export-dropdown-container">
                            <button type="button" id="export-btn"
                                class="relative flex items-center px-4 py-2 bg-gradient-to-r from-green-100 to-green-200 text-green-700 rounded-lg font-semibold
                                       hover:from-green-500 hover:to-green-600 hover:text-white hover:shadow-lg hover:scale-105
                                       active:from-green-600 active:to-green-700 active:scale-95 active:shadow-inner
                                       transition-all duration-200 ease-in-out transform
                                       before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                                       hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="size-5 mr-3">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                                </svg>
                                Export
                            </button>
                            <div id="export-dropdown"
                                class="hidden absolute right-0 mt-2 w-36 bg-white border border-gray-200 rounded shadow-lg z-50">
                                <button type="button"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                    id="export-xlsx">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em"
                                        viewBox="0 0 16 16" class="mr-3">
                                        <path fill="currentColor" fill-rule="evenodd"
                                            d="M14 4.5V11h-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5zM7.86 14.841a1.13 1.13 0 0 0 .401.823q.195.162.479.252q.284.091.665.091q.507 0 .858-.158q.355-.158.54-.44a1.17 1.17 0 0 0 .187-.656q0-.336-.135-.56a1 1 0 0 0-.375-.357a2 2 0 0 0-.565-.21l-.621-.144a1 1 0 0 1-.405-.176a.37.37 0 0 1-.143-.299q0-.234.184-.384q.188-.152.513-.152q.214 0 .37.068a.6.6 0 0 1 .245.181a.56.56 0 0 1 .12.258h.75a1.1 1.1 0 0 0-.199-.566a1.2 1.2 0 0 0-.5-.41a1.8 1.8 0 0 0-.78-.152q-.44 0-.777.15q-.336.149-.527.421q-.19.273-.19.639q0 .302.123.524t.351.367q.229.143.54.213l.618.144q.31.073.462.193a.39.39 0 0 1 .153.326a.5.5 0 0 1-.085.29a.56.56 0 0 1-.255.193q-.168.07-.413.07q-.176 0-.32-.04a.8.8 0 0 1-.249-.115a.58.58 0 0 1-.255-.384zm-3.726-2.909h.893l-1.274 2.007l1.254 1.992h-.908l-.85-1.415h-.035l-.853 1.415H1.5l1.24-2.016l-1.228-1.983h.931l.832 1.438h.036zm1.923 3.325h1.697v.674H5.266v-3.999h.791zm7.636-3.325h.893l-1.274 2.007l1.254 1.992h-.908l-.85-1.415h-.035l-.853 1.415h-.861l1.24-2.016l-1.228-1.983h.931l.832 1.438h.036z" />
                                    </svg>
                                    Export as XLSX</button>
                                <button type="button"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                    id="export-pdf">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em"
                                        viewBox="0 0 24 24" class="mr-3">
                                        <path fill="currentColor"
                                            d="M18.53 9L13 3.47a.75.75 0 0 0-.53-.22H8A2.75 2.75 0 0 0 5.25 6v12A2.75 2.75 0 0 0 8 20.75h8A2.75 2.75 0 0 0 18.75 18V9.5a.75.75 0 0 0-.22-.5m-5.28-3.19l2.94 2.94h-2.94ZM16 19.25H8A1.25 1.25 0 0 1 6.75 18V6A1.25 1.25 0 0 1 8 4.75h3.75V9.5a.76.76 0 0 0 .75.75h4.75V18A1.25 1.25 0 0 1 16 19.25" />
                                        <path fill="currentColor"
                                            d="M13.49 14.85a3.15 3.15 0 0 1-1.31-1.66a4.44 4.44 0 0 0 .19-2a.8.8 0 0 0-1.52-.19a5 5 0 0 0 .25 2.4A29 29 0 0 1 9.83 16c-.71.4-1.68 1-1.83 1.69c-.12.56.93 2 2.72-1.12a19 19 0 0 1 2.44-.72a4.7 4.7 0 0 0 2 .61a.82.82 0 0 0 .62-1.38c-.42-.43-1.67-.31-2.29-.23m-4.78 3a4.3 4.3 0 0 1 1.09-1.24c-.68 1.08-1.09 1.27-1.09 1.25Zm2.92-6.81c.26 0 .24 1.15.06 1.46a3.1 3.1 0 0 1-.06-1.45Zm-.87 4.88a15 15 0 0 0 .88-1.92a3.9 3.9 0 0 0 1.08 1.26a12.4 12.4 0 0 0-1.96.67Zm4.7-.18s-.18.22-1.33-.28c1.25-.08 1.46.21 1.33.29Z" />
                                    </svg>
                                    Export as PDF</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Filters Indicator -->
                @if (request('search') || request('status') || request('date_from') || request('date_to'))
                    <div
                        class="flex flex-wrap items-center gap-2 mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <span class="text-sm font-medium text-blue-700">Active Filters:</span>

                        @if (request('search'))
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Search: "{{ request('search') }}"
                            </span>
                        @endif

                        @if (request('status') && request('status') !== 'all')
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                    </path>
                                </svg>
                                Status: {{ ucfirst(str_replace('_', ' ', request('status'))) }}
                            </span>
                        @endif

                        @if (request('date_from') || request('date_to'))
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                Date:
                                @if (request('date_from') && request('date_to'))
                                    {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }} -
                                    {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                                @elseif (request('date_from'))
                                    From {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }}
                                @else
                                    Until {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                                @endif
                            </span>
                        @endif

                        <a href="{{ route('staff.pr_review') }}"
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 hover:bg-red-200 transition-colors">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear All
                        </a>
                    </div>
                @endif

                <!-- Purchase Requests Table -->
                @if ($purchaseRequests->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        #
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        PR Number
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date Created
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Requesting Unit
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Amount
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($purchaseRequests as $index => $pr)
                                    <tr>
                                        <td
                                            class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center font-medium">
                                            {{ ($purchaseRequests->currentPage() - 1) * $purchaseRequests->perPage() + $index + 1 }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $pr->pr_number }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $pr->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $pr->user->first_name }}{{ $pr->user->middle_name ? ' ' . $pr->user->middle_name : '' }}
                                            {{ $pr->user->last_name }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            ₱{{ number_format($pr->total ?? 0, 2) }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $pr->status_color }}">
                                                {{ $pr->status_display }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <button onclick="openViewModal({{ $pr->id }})"
                                                class="relative bg-gradient-to-r from-blue-500 to-blue-600 text-white px-3 py-1 rounded-lg text-sm font-medium w-28
                                                       hover:from-blue-600 hover:to-blue-700 hover:shadow-lg hover:scale-105
                                                       active:from-blue-700 active:to-blue-800 active:scale-95 active:shadow-inner
                                                       transition-all duration-200 ease-in-out transform
                                                       before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                                                       hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                </svg>
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Custom Pagination - Only show when there are more than 10 items -->
                    @if ($purchaseRequests->total() > 10)
                        <div class="flex justify-center mt-6">
                            <div class="flex items-center space-x-1">
                                @if ($purchaseRequests->onFirstPage())
                                    <span class="px-3 py-2 text-gray-400 cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </span>
                                @else
                                    <a href="{{ $purchaseRequests->appends(request()->query())->previousPageUrl() }}"
                                        class="px-3 py-2 text-gray-600 hover:text-blue-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </a>
                                @endif

                                @php
                                    $start = max(1, $purchaseRequests->currentPage() - 2);
                                    $end = min($purchaseRequests->lastPage(), $purchaseRequests->currentPage() + 2);

                                    if ($end - $start < 4) {
                                        if ($start == 1) {
                                            $end = min($purchaseRequests->lastPage(), $start + 4);
                                        } else {
                                            $start = max(1, $end - 4);
                                        }
                                    }
                                @endphp

                                @if ($start > 1)
                                    <a href="{{ $purchaseRequests->appends(request()->query())->url(1) }}"
                                        class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">1</a>
                                    @if ($start > 2)
                                        <span class="px-2 py-2 text-gray-400">...</span>
                                    @endif
                                @endif

                                @for ($page = $start; $page <= $end; $page++)
                                    @if ($page == $purchaseRequests->currentPage())
                                        <span
                                            class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md">{{ $page }}</span>
                                    @else
                                        <a href="{{ $purchaseRequests->appends(request()->query())->url($page) }}"
                                            class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">{{ $page }}</a>
                                    @endif
                                @endfor

                                @if ($end < $purchaseRequests->lastPage())
                                    @if ($end < $purchaseRequests->lastPage() - 1)
                                        <span class="px-2 py-2 text-gray-400">...</span>
                                    @endif
                                    <a href="{{ $purchaseRequests->appends(request()->query())->url($purchaseRequests->lastPage()) }}"
                                        class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">{{ $purchaseRequests->lastPage() }}</a>
                                @endif

                                @if ($purchaseRequests->hasMorePages())
                                    <a href="{{ $purchaseRequests->appends(request()->query())->nextPageUrl() }}"
                                        class="px-3 py-2 text-gray-600 hover:text-blue-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                @else
                                    <span class="px-3 py-2 text-gray-400 cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        @if (request('search') || request('status') || request('date_from') || request('date_to'))
                            <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No results
                                found{{ request('search') ? ' for "' . request('search') . '"' : '' }}</h3>
                            <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                        @else
                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No purchase requests found</h3>
                            <p class="mt-1 text-sm text-gray-500">No purchase requests are available for review.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- View Purchase Request Modal -->
    <x-modal name="view-pr-modal" maxWidth="2xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Purchase Request Details</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeViewModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700">PR Number</label>
                    <p id="view-pr-number" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700">Date</label>
                    <p id="view-pr-date" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700">Requesting Unit</label>
                    <p id="view-requesting-unit" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700">Entity Name</label>
                    <p id="view-entity-name" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700">Fund Cluster</label>
                    <p id="view-fund-cluster" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700">Office Section</label>
                    <p id="view-office-section" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700">Status</label>
                    <span id="view-status"
                        class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full"></span>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Items</label>
                    <div
                        style="max-height: 220px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 0.5rem;">
                        <table class="min-w-full divide-y divide-gray-200 text-sm" id="view-items-table">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-2 py-1 text-left font-semibold">Unit</th>
                                    <th class="px-2 py-1 text-left font-semibold">Qty</th>
                                    <th class="px-2 py-1 text-left font-semibold">Unit Cost</th>
                                    <th class="px-2 py-1 text-left font-semibold">Total Cost</th>
                                    <th class="px-2 py-1 text-left font-semibold">Description</th>
                                </tr>
                            </thead>
                            <tbody id="view-items-table-body">
                                <!-- Items will be populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700">Delivery Address</label>
                    <p id="view-delivery-address" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700">Purpose</label>
                    <p id="view-purpose" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700">Requested By</label>
                    <p id="view-requested-by" class="mt-1 text-sm text-gray-900"></p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700">Delivery Period</label>
                    <p id="view-delivery-period" class="mt-1 text-sm text-gray-900"></p>
                </div>
            </div>

            <div class="mt-6 flex justify-end items-center space-x-2">
                <!-- Approve button - will be shown/hidden based on status -->
                <button type="button" id="modal-approve-btn"
                    class="hidden relative bg-gradient-to-r from-green-500 to-green-600 text-white font-bold py-2 px-4 rounded-lg
                           hover:from-green-600 hover:to-green-700 hover:shadow-lg hover:scale-105
                           active:from-green-700 active:to-green-800 active:scale-95 active:shadow-inner
                           transition-all duration-200 ease-in-out transform
                           before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                           hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200"
                    onclick="approveFromModal()">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                        </path>
                    </svg>
                    Approve
                </button>
                <!-- Reject button - will be shown/hidden based on status -->
                <button type="button" id="modal-reject-btn"
                    class="hidden relative bg-gradient-to-r from-red-500 to-red-600 text-white font-bold py-2 px-4 rounded-lg
                           hover:from-red-600 hover:to-red-700 hover:shadow-lg hover:scale-105
                           active:from-red-700 active:to-red-800 active:scale-95 active:shadow-inner
                           transition-all duration-200 ease-in-out transform
                           before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                           hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200"
                    onclick="rejectFromModal()">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Reject
                </button>
                <!-- Generate PO button - will be shown/hidden based on status -->
                <button type="button" id="modal-generate-po-btn"
                    class="hidden relative bg-gradient-to-r from-green-600 to-green-700 text-white font-bold py-2 px-4 rounded-lg
                           hover:from-green-700 hover:to-green-800 hover:shadow-lg hover:scale-105
                           active:from-green-800 active:to-green-900 active:scale-95 active:shadow-inner
                           transition-all duration-200 ease-in-out transform
                           before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                           hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200"
                    onclick="generatePOFromModal()">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Generate PO
                </button>
                <button type="button"
                    class="relative bg-gradient-to-r from-gray-500 to-gray-600 text-white font-bold py-2 px-4 rounded-lg
                           hover:from-gray-600 hover:to-gray-700 hover:shadow-lg hover:scale-105
                           active:from-gray-700 active:to-gray-800 active:scale-95 active:shadow-inner
                           transition-all duration-200 ease-in-out transform
                           before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                           hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200"
                    onclick="closeViewModal()">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Close
                </button>
            </div>
        </div>
    </x-modal>

    <!-- Rejection Reason Modal -->
    <x-modal name="reject-reason-modal">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Purchase Request</h3>

            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-3">Are you sure you want to reject this purchase request? Please
                    provide a reason for rejection.</p>

                <label for="rejection-reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Rejection Reason <span class="text-red-500">*</span>
                </label>
                <textarea id="rejection-reason" name="rejection_reason" rows="4"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400"
                    placeholder="Please provide a detailed reason for rejecting this purchase request..." required></textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button"
                    class="relative bg-gradient-to-r from-gray-500 to-gray-600 text-white font-bold py-2 px-4 rounded-lg
                           hover:from-gray-600 hover:to-gray-700 hover:shadow-lg hover:scale-105
                           active:from-gray-700 active:to-gray-800 active:scale-95 active:shadow-inner
                           transition-all duration-200 ease-in-out transform
                           before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                           hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200"
                    onclick="closeRejectModal()">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancel
                </button>
                <button type="button" id="confirm-reject-btn"
                    class="relative bg-gradient-to-r from-red-600 to-red-700 text-white font-bold py-2 px-4 rounded-lg
                           hover:from-red-700 hover:to-red-800 hover:shadow-lg hover:scale-105
                           active:from-red-800 active:to-red-900 active:scale-95 active:shadow-inner
                           transition-all duration-200 ease-in-out transform
                           before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                           hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200"
                    onclick="confirmReject()">
                    Reject Request
                </button>
            </div>
        </div>
    </x-modal>

    <script>
        function openViewModal(prId) {
            console.log('Opening modal for PR ID:', prId);

            fetch(`/staff/pr-review/${prId}/data`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received data:', data);

                    // Populate basic fields
                    document.getElementById('view-pr-number').textContent = data.pr_number;
                    document.getElementById('view-pr-date').textContent = data.date;
                    document.getElementById('view-requesting-unit').textContent = data.requesting_unit;
                    document.getElementById('view-entity-name').textContent = data.entity_name;
                    document.getElementById('view-fund-cluster').textContent = data.fund_cluster;
                    document.getElementById('view-office-section').textContent = data.office_section;
                    document.getElementById('view-delivery-address').textContent = data.delivery_address;
                    document.getElementById('view-purpose').textContent = data.purpose;
                    document.getElementById('view-requested-by').textContent = data.requested_by_name;
                    document.getElementById('view-delivery-period').textContent = data.delivery_period;

                    // Populate items table
                    const itemsBody = document.getElementById('view-items-table-body');
                    itemsBody.innerHTML = '';
                    if (Array.isArray(data.items) && data.items.length > 0) {
                        let grandTotal = 0;
                        data.items.forEach(item => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-2 py-1">${item.unit}</td>
                                <td class="px-2 py-1">${item.quantity}</td>
                                <td class="px-2 py-1">₱${parseFloat(item.unit_cost).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                <td class="px-2 py-1">₱${parseFloat(item.total_cost).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                <td class="px-2 py-1">${item.item_description}</td>
                            `;
                            itemsBody.appendChild(row);
                            grandTotal += parseFloat(item.total_cost);
                        });

                        // Add grand total row
                        const totalRow = document.createElement('tr');
                        totalRow.className = 'bg-gray-50 border-t-2 border-gray-300';
                        totalRow.innerHTML = `
                            <td class="px-2 py-1 font-semibold" colspan="3">Grand Total:</td>
                            <td class="px-2 py-1 font-semibold text-green-600">₱${grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                            <td class="px-2 py-1"></td>
                        `;
                        itemsBody.appendChild(totalRow);
                    } else {
                        const row = document.createElement('tr');
                        row.innerHTML =
                            `<td colspan="5" class="px-2 py-1 text-center text-gray-500">No items found</td>`;
                        itemsBody.appendChild(row);
                    }

                    // Set status
                    const statusElement = document.getElementById('view-status');
                    statusElement.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                    statusElement.className =
                        `mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full ${data.status_color}`;

                    // Show/hide approve and reject buttons based on status
                    const approveBtn = document.getElementById('modal-approve-btn');
                    const rejectBtn = document.getElementById('modal-reject-btn');
                    const generatePOBtn = document.getElementById('modal-generate-po-btn');

                    if (data.status === 'pending') {
                        approveBtn.classList.remove('hidden');
                        rejectBtn.classList.remove('hidden');
                        generatePOBtn.classList.add('hidden');
                        // Store the PR ID for the modal buttons
                        approveBtn.setAttribute('data-pr-id', prId);
                        rejectBtn.setAttribute('data-pr-id', prId);
                    } else if (data.status === 'approved') {
                        approveBtn.classList.add('hidden');
                        rejectBtn.classList.add('hidden');
                        generatePOBtn.classList.remove('hidden');
                        // Store the PR ID for the generate PO button
                        generatePOBtn.setAttribute('data-pr-id', prId);
                    } else {
                        approveBtn.classList.add('hidden');
                        rejectBtn.classList.add('hidden');
                        generatePOBtn.classList.add('hidden');
                    }

                    // Show the modal
                    window.dispatchEvent(new CustomEvent('open-modal', {
                        detail: 'view-pr-modal'
                    }));
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorAlert('Error loading purchase request details: ' + error.message);
                });
        }

        function closeViewModal() {
            window.dispatchEvent(new CustomEvent('close-modal', {
                detail: 'view-pr-modal'
            }));
        }

        function approveFromModal() {
            const approveBtn = document.getElementById('modal-approve-btn');
            const prId = approveBtn.getAttribute('data-pr-id');
            if (prId) {
                approvePR(prId);
            }
        }

        function rejectFromModal() {
            const rejectBtn = document.getElementById('modal-reject-btn');
            const prId = rejectBtn.getAttribute('data-pr-id');
            if (prId) {
                // Store the PR ID for use in the rejection modal
                document.getElementById('confirm-reject-btn').setAttribute('data-pr-id', prId);

                // Close the view modal and open the rejection reason modal
                closeViewModal();
                setTimeout(() => {
                    window.dispatchEvent(new CustomEvent('open-modal', {
                        detail: 'reject-reason-modal'
                    }));
                }, 100);
            }
        }

        function approvePR(prId) {
            showModernConfirmation(
                'Are you sure you want to approve this purchase request? This action will move it to the next stage of processing.',
                () => {
                    fetch(`/staff/pr-review/${prId}/approve`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Content-Type': 'application/json',
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showSuccessAlert('Purchase Request approved successfully!');
                                location.reload();
                            } else {
                                showErrorAlert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showErrorAlert('Error approving purchase request');
                        });
                }
            );
        }

        function closeRejectModal() {
            // Clear the rejection reason
            document.getElementById('rejection-reason').value = '';

            // Close the modal
            window.dispatchEvent(new CustomEvent('close-modal', {
                detail: 'reject-reason-modal'
            }));
        }

        function confirmReject() {
            const prId = document.getElementById('confirm-reject-btn').getAttribute('data-pr-id');
            const reason = document.getElementById('rejection-reason').value.trim();

            if (!reason) {
                showErrorAlert('Please provide a reason for rejection.');
                return;
            }

            rejectPR(prId, reason);
        }

        function rejectPR(prId, reason = null) {
            fetch(`/staff/pr-review/${prId}/reject`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        reason: reason
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccessAlert('Purchase Request rejected successfully!');
                        closeRejectModal();
                        location.reload();
                    } else {
                        showErrorAlert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorAlert('Error rejecting purchase request');
                });
        }

        function generatePOFromModal() {
            const generatePOBtn = document.getElementById('modal-generate-po-btn');
            const prId = generatePOBtn.getAttribute('data-pr-id');
            if (prId) {
                generatePO(prId);
            }
        }

        function generatePO(prId) {
            showModernConfirmation(
                'Are you sure you want to generate a Purchase Order for this PR?',
                function() {
                    fetch(`/staff/po-generation/${prId}/generate-po`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Content-Type': 'application/json',
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                closeViewModal();
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    location.reload();
                                }
                            } else {
                                showErrorAlert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showErrorAlert('Error generating PO');
                        });
                }
            );
        }

        function showSuccessAlert(message) {
            const alertDiv = document.createElement('div');
            alertDiv.style.cssText = `
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%);
                background: #10B981;
                color: white;
                padding: 16px 20px;
                border-radius: 8px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
                z-index: 99999;
                font-weight: 500;
                font-size: 16px;
                text-align: center;
                min-width: 300px;
                max-width: 400px;
            `;
            alertDiv.textContent = message;

            const closeBtn = document.createElement('button');
            closeBtn.textContent = '×';
            closeBtn.style.cssText = `
                position: absolute;
                top: 5px;
                right: 10px;
                background: none;
                border: none;
                color: white;
                font-size: 18px;
                cursor: pointer;
                line-height: 1;
            `;
            closeBtn.onclick = () => alertDiv.remove();
            alertDiv.appendChild(closeBtn);

            document.body.appendChild(alertDiv);

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 3000);
        }

        function showErrorAlert(message) {
            const alertDiv = document.createElement('div');
            alertDiv.style.cssText = `
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%);
                background: #EF4444;
                color: white;
                padding: 16px 20px;
                border-radius: 8px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
                z-index: 99999;
                font-weight: 500;
                font-size: 16px;
                text-align: center;
                min-width: 300px;
                max-width: 400px;
            `;
            alertDiv.textContent = message;

            const closeBtn = document.createElement('button');
            closeBtn.textContent = '×';
            closeBtn.style.cssText = `
                position: absolute;
                top: 5px;
                right: 10px;
                background: none;
                border: none;
                color: white;
                font-size: 18px;
                cursor: pointer;
                line-height: 1;
            `;
            closeBtn.onclick = () => alertDiv.remove();
            alertDiv.appendChild(closeBtn);

            document.body.appendChild(alertDiv);

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 3000);
        }

        function showModernConfirmation(message, onConfirm) {
            const confirmDiv = document.createElement('div');
            confirmDiv.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.6);
                backdrop-filter: blur(4px);
                z-index: 99999;
                display: flex;
                align-items: center;
                justify-content: center;
                animation: fadeIn 0.2s ease-out;
            `;

            const modalDiv = document.createElement('div');
            modalDiv.style.cssText = `
                background: white;
                padding: 32px 28px;
                border-radius: 16px;
                box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
                max-width: 440px;
                width: 90%;
                text-align: center;
                animation: slideIn 0.3s ease-out;
                transform-origin: center center;
            `;

            modalDiv.innerHTML = `
                <div style="margin-bottom: 20px;">
                    <div style="width: 64px; height: 64px; margin: 0 auto 16px; background: linear-gradient(135deg, #FEF3C7, #F59E0B); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 32px; height: 32px; color: #D97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 600; color: #1F2937;">Confirm Action</h3>
                    <p style="margin: 0; color: #6B7280; font-size: 15px; line-height: 1.5;">${message}</p>
                </div>
                <div style="display: flex; gap: 12px; justify-content: center;">
                    <button id="modern-confirm-yes" style="
                        padding: 12px 24px;
                        background: linear-gradient(135deg, #EF4444, #DC2626);
                        color: white;
                        border: none;
                        border-radius: 8px;
                        cursor: pointer;
                        font-weight: 600;
                        font-size: 14px;
                        transition: all 0.2s ease;
                        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
                    " onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(239, 68, 68, 0.4)'"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(239, 68, 68, 0.3)'">
                        Yes, Confirm
                    </button>
                    <button id="modern-confirm-no" style="
                        padding: 12px 24px;
                        background: linear-gradient(135deg, #6B7280, #4B5563);
                        color: white;
                        border: none;
                        border-radius: 8px;
                        cursor: pointer;
                        font-weight: 600;
                        font-size: 14px;
                        transition: all 0.2s ease;
                        box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
                    " onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(107, 114, 128, 0.4)'"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(107, 114, 128, 0.3)'">
                        Cancel
                    </button>
                </div>
            `;

            confirmDiv.appendChild(modalDiv);
            document.body.appendChild(confirmDiv);

            // Add CSS animations
            const style = document.createElement('style');
            style.textContent = `
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                @keyframes slideIn {
                    from { 
                        opacity: 0; 
                        transform: scale(0.8) translateY(-20px); 
                    }
                    to { 
                        opacity: 1; 
                        transform: scale(1) translateY(0); 
                    }
                }
            `;
            document.head.appendChild(style);

            // Event listeners
            document.getElementById('modern-confirm-yes').onclick = () => {
                confirmDiv.remove();
                onConfirm();
            };

            document.getElementById('modern-confirm-no').onclick = () => {
                confirmDiv.style.animation = 'fadeOut 0.2s ease-in';
                setTimeout(() => confirmDiv.remove(), 200);
            };

            confirmDiv.onclick = (e) => {
                if (e.target === confirmDiv) {
                    confirmDiv.style.animation = 'fadeOut 0.2s ease-in';
                    setTimeout(() => confirmDiv.remove(), 200);
                }
            };

            // Cleanup style when modal closes
            setTimeout(() => {
                if (style.parentNode) {
                    style.remove();
                }
            }, 5000);
        }
    </script>

</x-page-layout>
