<x-page-layout>
    <x-slot name="header">
        <x-app-header :homeUrl="route('user')" :title="$pageTitle ?? __('DSWD-PRISM')" :userName="Auth::user()->first_name .
            (Auth::user()->middle_name ? ' ' . Auth::user()->middle_name : '') .
            ' ' .
            Auth::user()->last_name" :recentActivities="$recentActivities ?? collect()" />
    </x-slot>

    <!-- Main Content -->
    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Header with Create Button -->
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Request Monitoring') }}
                </h2>
                <div>
                    <a href="{{ route('purchase-requests.create') }}"
                        class="bg-green-500 hover:bg-green-700 active:bg-green-900 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        <span>Add New PR</span>
                    </a>
                </div>
            </div>

            <!-- Search and Filter Controls -->
            <div class="bg-white overflow-visible shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2">
                    <form method="GET" action="{{ route('user.requests') }}"
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
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                            Search
                        </button>
                    </form>

                    <div class="flex items-center gap-2">
                        <div class="relative" x-data="{ open: false, activeTab: 'status' }">
                            <button @click="open = !open"
                                class="flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100 active:bg-gray-200">
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
                                            <a href="{{ route('user.requests', array_filter(['search' => request('search'), 'date_from' => request('date_from'), 'date_to' => request('date_to')])) }}"
                                                class="block px-4 py-2 text-gray-700 hover:bg-blue-100 rounded {{ !request('status') || request('status') == 'all' ? 'font-bold text-blue-600 bg-blue-50' : '' }}">
                                                All Statuses
                                            </a>
                                        </li>
                                        @php
                                            $statusDisplayMap = [
                                                'draft' => 'Draft',
                                                'pending' => 'Pending',
                                                'approved' => 'Approved',
                                                'rejected' => 'Rejected',
                                                'po_generated' => 'PO Generated',
                                                'failed' => 'Failed',
                                            ];
                                        @endphp
                                        @foreach ($statuses as $status)
                                            <li>
                                                <a href="{{ route('user.requests', array_filter(['status' => $status, 'search' => request('search'), 'date_from' => request('date_from'), 'date_to' => request('date_to')])) }}"
                                                    class="block px-4 py-2 text-gray-700 hover:bg-blue-100 rounded {{ request('status') == $status ? 'font-bold text-blue-600 bg-blue-50' : '' }}">
                                                    {{ $statusDisplayMap[$status] ?? ucfirst($status) }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <!-- Date Filter Content -->
                                <div x-show="activeTab === 'date'" class="p-4">
                                    <form method="GET" action="{{ route('user.requests') }}" class="space-y-4">
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
                                                class="flex-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg text-sm">
                                                Apply Filter
                                            </button>
                                            @if (request('date_from') || request('date_to'))
                                                <a href="{{ route('user.requests', array_filter(['search' => request('search'), 'status' => request('status')])) }}"
                                                    class="flex-1 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg text-sm text-center">
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
                                class="flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg font-semibold hover:bg-green-200 transition active:bg-green-300">
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="size-5 mr-3">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                                </svg>

                                Export
                                {{-- <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg> --}}
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

                        <a href="{{ route('user.requests') }}"
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 hover:bg-red-200 transition-colors">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear All
                        </a>
                    </div>
                @endif

                <!-- Purchase Request Table -->
                @if ($purchaseRequests->count() > 0)
                    <!-- Search Results Indicator -->
                    @if (request('search'))
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <span class="text-blue-800 font-medium">
                                        Search Results for "{{ request('search') }}"
                                    </span>
                                </div>
                                <div class="text-blue-600 text-sm">
                                    {{ $purchaseRequests->total() }}
                                    {{ $purchaseRequests->total() === 1 ? 'result' : 'results' }} found
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table id="purchase-requests-table" class="min-w-full divide-y divide-gray-200">
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
                                        Status
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($purchaseRequests as $pr)
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ ($purchaseRequests->currentPage() - 1) * $purchaseRequests->perPage() + $loop->iteration }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $pr->pr_number }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $pr->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $pr->status_color }}">
                                                {{ $pr->status_display }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            ₱ {{ number_format($pr->total, 2) }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <div class="flex space-x-2 justify-center">
                                                @php
                                                    $showEdit = !in_array($pr->status, [
                                                        'approved',
                                                        'po_generated',
                                                        'pending',
                                                        'rejected',
                                                        'completed',
                                                        'failed',
                                                    ]);
                                                @endphp
                                                <button onclick="openViewModal({{ $pr->id }})"
                                                    class="bg-blue-500 hover:bg-blue-700 active:bg-blue-900 text-white px-3 py-1 rounded text-sm font-medium
                {{ $showEdit ? '' : 'w-28' }}">
                                                    View
                                                </button>
                                                @if ($showEdit)
                                                    <button onclick="openEditModal({{ $pr->id }})"
                                                        class="bg-white hover:bg-gray-50 active:bg-gray-100 text-gray-700 border border-gray-300 px-3 py-1 rounded text-sm font-medium">
                                                        Edit
                                                    </button>
                                                @endif
                                            </div>
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
                                    <a href="{{ $purchaseRequests->previousPageUrl() }}"
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
                                    <a href="{{ $purchaseRequests->url(1) }}"
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
                                        <a href="{{ $purchaseRequests->url($page) }}"
                                            class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">{{ $page }}</a>
                                    @endif
                                @endfor

                                @if ($end < $purchaseRequests->lastPage())
                                    @if ($end < $purchaseRequests->lastPage() - 1)
                                        <span class="px-2 py-2 text-gray-400">...</span>
                                    @endif
                                    <a href="{{ $purchaseRequests->url($purchaseRequests->lastPage()) }}"
                                        class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">{{ $purchaseRequests->lastPage() }}</a>
                                @endif

                                @if ($purchaseRequests->hasMorePages())
                                    <a href="{{ $purchaseRequests->nextPageUrl() }}"
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
                        @if (request('search'))
                            <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No results found for
                                "{{ request('search') }}"</h3>
                            <p class="mt-1 text-sm text-gray-500">Try adjusting your search terms or browse all
                                purchase requests.</p>
                            <div class="mt-4">
                                <a href="{{ route('user.requests') }}"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    View All Requests
                                </a>
                            </div>
                        @else
                            <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No purchase requests found</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating your first purchase request.
                            </p>
                            <div class="mt-4">
                                <a href="{{ route('purchase-requests.create') }}"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Create your first PR
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
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
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">PR Number</label>
                        <p id="view-pr-number" class="mt-1 text-sm text-gray-900"></p>
                        <p id="view-pr-id" style="display:none;"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date</label>
                        <p id="view-pr-date" class="mt-1 text-sm text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Entity Name</label>
                        <p id="view-entity-name" class="mt-1 text-sm text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fund Cluster</label>
                        <p id="view-fund-cluster" class="mt-1 text-sm text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Office Section</label>
                        <p id="view-office-section" class="mt-1 text-sm text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <span id="view-status"
                            class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full"></span>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Items</label>
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
                        <label class="block text-sm font-medium text-gray-700">Delivery Address</label>
                        <p id="view-delivery-address" class="mt-1 text-sm text-gray-900"></p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Purpose</label>
                        <p id="view-purpose" class="mt-1 text-sm text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Requested By</label>
                        <p id="view-requested-by" class="mt-1 text-sm text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Delivery Period</label>
                        <p id="view-delivery-period" class="mt-1 text-sm text-gray-900"></p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <button id="submit-draft-btn" type="button"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg hidden"
                        onclick="submitDraftPR()">
                        Submit
                    </button>
                    <button id="withdraw-draft-btn" type="button"
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg hidden"
                        onclick="withdrawDraftPR()">
                        Withdraw
                    </button>
                    <button id="upload-btn" type="button"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg hidden"
                        onclick="redirectToUploadPR()">
                        Upload
                    </button>
                    <a id="download-btn" href="#" target="_blank"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hidden">
                        Download
                    </a>
                    <button id="print-btn" type="button"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg hidden"
                        onclick="openPrintView()">
                        Print
                    </button>
                    <button id="complete-btn" type="button"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg hidden"
                        onclick="markAsCompleted()">
                        Mark as Completed
                    </button>
                    <button type="button"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg"
                        onclick="closeViewModal()">
                        Close
                    </button>
                </div>
            </div>
        </x-modal>

        <!-- Edit Purchase Request Modal -->
        <x-modal name="edit-pr-modal" maxWidth="2xl">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Edit Purchase Request</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeEditModal()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="edit-pr-form" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="edit-entity-name" class="block text-sm font-medium text-gray-700">Entity
                                Name <span class="text-red-500">*</span></label>
                            <input type="text" name="entity_name" id="edit-entity-name"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="edit-fund-cluster" class="block text-sm font-medium text-gray-700">Fund
                                Cluster <span class="text-red-500">*</span></label>
                            <input type="text" name="fund_cluster" id="edit-fund-cluster"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="edit-office-section" class="block text-sm font-medium text-gray-700">Office
                                Section <span class="text-red-500">*</span></label>
                            <input type="text" name="office_section" id="edit-office-section"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="edit-date" class="block text-sm font-medium text-gray-700">Date <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="date" id="edit-date"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="edit-delivery-period" class="block text-sm font-medium text-gray-700">Delivery
                                Period <span class="text-red-500">*</span></label>
                            <input type="text" name="delivery_period" id="edit-delivery-period"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                    </div>

                    <!-- Item Details Section -->
                    <div class="border-t pt-4 mt-4">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Item Details</h4>
                        <div id="edit-items-container">
                            <!-- Items will be populated by JavaScript -->
                        </div>
                        <button type="button" id="edit-add-item-btn"
                            class="mt-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            + Add Another Item
                        </button>
                    </div>

                    <div class="grid grid-cols-1 gap-4 mt-4">
                        <div>
                            <label for="edit-delivery-address"
                                class="block text-sm font-medium text-gray-700">Delivery
                                Address <span class="text-red-500">*</span></label>
                            <textarea name="delivery_address" id="edit-delivery-address" rows="3"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                        </div>
                        <div>
                            <label for="edit-purpose" class="block text-sm font-medium text-gray-700">Purpose <span
                                    class="text-red-500">*</span></label>
                            <textarea name="purpose" id="edit-purpose" rows="3"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg"
                            onclick="closeEditModal()">
                            Cancel
                        </button>
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        <script>
            function showSuccessAlert(message) {
                console.log('showSuccessAlert called with:', message);

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

                // Add close button
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
                console.log('Top-centered success alert added to DOM');

                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 3000);
            }

            function showErrorAlert(message) {
                console.log('showErrorAlert called with:', message);

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

                // Add close button
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
                console.log('Top-centered error alert added to DOM');

                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 3000);
            }


            function openViewModal(prId) {
                console.log('Opening view modal for PR ID:', prId);

                // Fetch purchase request data
                fetch(`/purchase-requests/${prId}/data`)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        console.log('Received data:', data);

                        // Populate modal fields
                        document.getElementById('view-pr-number').textContent = data.pr_number;
                        document.getElementById('view-pr-date').textContent = data.date;
                        document.getElementById('view-entity-name').textContent = data.entity_name;
                        document.getElementById('view-fund-cluster').textContent = data.fund_cluster;
                        document.getElementById('view-office-section').textContent = data.office_section;
                        document.getElementById('view-delivery-address').textContent = data.delivery_address;
                        document.getElementById('view-purpose').textContent = data.purpose;
                        document.getElementById('view-requested-by').textContent = data.requested_by_name;
                        document.getElementById('view-delivery-period').textContent = data.delivery_period;
                        document.getElementById('view-pr-id').textContent = data.id;

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

                        // Set status with color
                        const statusDisplayMap = {
                            'draft': 'Draft',
                            'pending': 'Pending',
                            'approved': 'Approved',
                            'rejected': 'Rejected',
                            'po_generated': 'PO Generated',
                            'completed': 'Completed',
                            'failed': 'Failed',
                        };
                        const statusElement = document.getElementById('view-status');
                        statusElement.textContent = statusDisplayMap[data.status] || (data.status.charAt(0).toUpperCase() +
                            data.status.slice(1));
                        statusElement.className =
                            `mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full ${data.status_color}`;

                        // Show or hide the submit button
                        const submitBtn = document.getElementById('submit-draft-btn');
                        if (data.status === 'draft') {
                            submitBtn.classList.remove('hidden');
                            submitBtn.setAttribute('data-pr-id', prId);
                        } else {
                            submitBtn.classList.add('hidden');
                            submitBtn.removeAttribute('data-pr-id');
                        }
                        // Show or hide the withdraw button
                        const withdrawBtn = document.getElementById('withdraw-draft-btn');
                        if (withdrawBtn) {
                            if (data.status === 'pending') {
                                withdrawBtn.classList.remove('hidden');
                                withdrawBtn.setAttribute('data-pr-id', prId);
                            } else {
                                withdrawBtn.classList.add('hidden');
                                withdrawBtn.removeAttribute('data-pr-id');
                            }
                        }
                        // Show or hide the print button
                        const printBtn = document.getElementById('print-btn');
                        if (printBtn) {
                            if (data.status === 'approved' || data.status === 'po_generated' || data.status ===
                                'completed') {
                                printBtn.classList.remove('hidden');
                            } else {
                                printBtn.classList.add('hidden');
                            }
                        }
                        // Show or hide the complete button
                        const completeBtn = document.getElementById('complete-btn');
                        if (completeBtn) {
                            if (data.status === 'approved' || data.status === 'po_generated') {
                                completeBtn.classList.remove('hidden');
                                completeBtn.setAttribute('data-pr-id', prId);
                            } else {
                                completeBtn.classList.add('hidden');
                                completeBtn.removeAttribute('data-pr-id');
                            }
                        }
                        // Show or hide the upload button
                        const uploadBtn = document.getElementById('upload-btn');
                        const downloadBtn = document.getElementById('download-btn');
                        if (uploadBtn) {
                            if (['completed', 'po_generated', 'approved'].includes(data.status)) {
                                uploadBtn.classList.remove('hidden');
                                uploadBtn.setAttribute('data-pr-id', prId);

                                // Fetch uploaded document info for this PR
                                fetch(`/uploaded-documents/for-pr/${data.pr_number}`)
                                    .then(response => response.json())
                                    .then(docData => {
                                        if (docData.exists && docData.download_url) {
                                            downloadBtn.classList.remove('hidden');
                                            downloadBtn.href = docData.download_url;
                                        } else {
                                            downloadBtn.classList.add('hidden');
                                            downloadBtn.href = '#';
                                        }
                                    });
                            } else {
                                uploadBtn.classList.add('hidden');
                                uploadBtn.removeAttribute('data-pr-id');
                                downloadBtn.classList.add('hidden');
                                downloadBtn.href = '#';
                            }
                        }

                        // Open modal
                        window.dispatchEvent(new CustomEvent('open-modal', {
                            detail: 'view-pr-modal'
                        }));
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showErrorAlert('Error loading purchase request data');
                    });
            }

            function redirectToUploadPR() {
                const prNumber = document.getElementById('view-pr-number').textContent.trim();
                if (prNumber) {
                    window.location.href = `/uploaded-documents/upload?pr_number=${encodeURIComponent(prNumber)}`;
                }
            }

            function submitDraftPR() {
                const btn = document.getElementById('submit-draft-btn');
                const prId = btn.getAttribute('data-pr-id');
                if (!prId) return;

                if (!confirm('Submit this Purchase Request for review?')) return;

                fetch(`/purchase-requests/${prId}/submit`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({}),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showSuccessAlert('Purchase Request submitted successfully!');
                            closeViewModal();
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            showErrorAlert(data.message || 'Failed to submit Purchase Request.');
                        }
                    })
                    .catch(error => {
                        showErrorAlert('Error submitting Purchase Request.');
                        console.error(error);
                    });
            }

            function withdrawDraftPR() {
                const btn = document.getElementById('withdraw-draft-btn');
                const prId = btn.getAttribute('data-pr-id');
                if (!prId) return;

                if (!confirm('Withdraw this Purchase Request?')) return;

                fetch(`/purchase-requests/${prId}/withdraw`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({}),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showSuccessAlert('Purchase Request withdrawn successfully!');
                            closeViewModal();
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            showErrorAlert(data.message || 'Failed to withdraw Purchase Request.');
                        }
                    })
                    .catch(error => {
                        showErrorAlert('Error withdrawing Purchase Request.');
                        console.error(error);
                    });
            }

            // Function to open print view
            function openPrintView() {
                const prId = document.getElementById('view-pr-id').textContent.trim();
                if (prId) {
                    window.open(`/purchase-requests/${prId}/print`, '_blank');
                } else {
                    alert('PR ID not found.');
                }
            }

            function markAsCompleted() {
                const prId = document.getElementById('view-pr-id').textContent.trim();
                if (!prId) return;

                if (!confirm('Mark this Purchase Request as Completed?')) return;

                fetch(`/purchase-requests/${prId}/complete`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({}),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showSuccessAlert('Purchase Request marked as completed!');
                            closeViewModal();
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            showErrorAlert(data.message || 'Failed to mark as completed.');
                        }
                    })
                    .catch(error => {
                        showErrorAlert('Error marking as completed.');
                        console.error(error);
                    });
            }

            function closeViewModal() {
                window.dispatchEvent(new CustomEvent('close-modal', {
                    detail: 'view-pr-modal'
                }));
            }

            function openEditModal(prId) {
                console.log('Opening edit modal for PR ID:', prId);

                // Fetch purchase request data
                fetch(`/purchase-requests/${prId}/data`)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        console.log('Received data for edit:', data);

                        // Populate basic form fields
                        document.getElementById('edit-entity-name').value = data.entity_name;
                        document.getElementById('edit-fund-cluster').value = data.fund_cluster;
                        document.getElementById('edit-office-section').value = data.office_section;
                        document.getElementById('edit-date').value = data.date;
                        document.getElementById('edit-delivery-period').value = data.delivery_period;
                        document.getElementById('edit-delivery-address').value = data.delivery_address;
                        document.getElementById('edit-purpose').value = data.purpose;

                        // Populate items
                        const itemsContainer = document.getElementById('edit-items-container');
                        itemsContainer.innerHTML = '';

                        if (Array.isArray(data.items) && data.items.length > 0) {
                            data.items.forEach((item, index) => {
                                const itemDiv = createEditItemDiv(item, index);
                                itemsContainer.appendChild(itemDiv);
                            });
                        } else {
                            // Add one empty item if no items exist
                            const itemDiv = createEditItemDiv({}, 0);
                            itemsContainer.appendChild(itemDiv);
                        }

                        // Set form action
                        document.getElementById('edit-pr-form').action = `/purchase-requests/${prId}/update`;

                        // Setup add item button functionality
                        setupEditAddItemButton();

                        // Open modal
                        window.dispatchEvent(new CustomEvent('open-modal', {
                            detail: 'edit-pr-modal'
                        }));
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showErrorAlert('Error loading purchase request data');
                    });
            }

            function createEditItemDiv(item = {}, index = 0) {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'edit-item-fields border border-gray-200 rounded-lg p-4 mb-4';
                itemDiv.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Unit <span class="text-red-500">*</span></label>
                            <select name="unit[]" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">Select unit</option>
                                <option value="pcs" ${item.unit === 'pcs' ? 'selected' : ''}>pcs</option>
                                <option value="set" ${item.unit === 'set' ? 'selected' : ''}>set</option>
                                <option value="pair" ${item.unit === 'pair' ? 'selected' : ''}>pair</option>
                                <option value="dozen" ${item.unit === 'dozen' ? 'selected' : ''}>dozen</option>
                                <option value="lot" ${item.unit === 'lot' ? 'selected' : ''}>lot</option>
                                <option value="box" ${item.unit === 'box' ? 'selected' : ''}>box</option>
                                <option value="pack" ${item.unit === 'pack' ? 'selected' : ''}>pack</option>
                                <option value="carton" ${item.unit === 'carton' ? 'selected' : ''}>carton</option>
                                <option value="case" ${item.unit === 'case' ? 'selected' : ''}>case</option>
                                <option value="roll" ${item.unit === 'roll' ? 'selected' : ''}>roll</option>
                                <option value="ream" ${item.unit === 'ream' ? 'selected' : ''}>ream</option>
                                <option value="bundle" ${item.unit === 'bundle' ? 'selected' : ''}>bundle</option>
                                <option value="tube" ${item.unit === 'tube' ? 'selected' : ''}>tube</option>
                                <option value="bottle" ${item.unit === 'bottle' ? 'selected' : ''}>bottle</option>
                                <option value="can" ${item.unit === 'can' ? 'selected' : ''}>can</option>
                                <option value="jar" ${item.unit === 'jar' ? 'selected' : ''}>jar</option>
                                <option value="sachet" ${item.unit === 'sachet' ? 'selected' : ''}>sachet</option>
                                <option value="drum" ${item.unit === 'drum' ? 'selected' : ''}>drum</option>
                                <option value="barrel" ${item.unit === 'barrel' ? 'selected' : ''}>barrel</option>
                                <option value="bag" ${item.unit === 'bag' ? 'selected' : ''}>bag</option>
                                <option value="g" ${item.unit === 'g' ? 'selected' : ''}>g</option>
                                <option value="kg" ${item.unit === 'kg' ? 'selected' : ''}>kg</option>
                                <option value="lb" ${item.unit === 'lb' ? 'selected' : ''}>lb</option>
                                <option value="ton" ${item.unit === 'ton' ? 'selected' : ''}>ton</option>
                                <option value="ml" ${item.unit === 'ml' ? 'selected' : ''}>ml</option>
                                <option value="l" ${item.unit === 'l' ? 'selected' : ''}>L</option>
                                <option value="gal" ${item.unit === 'gal' ? 'selected' : ''}>gal</option>
                                <option value="mm" ${item.unit === 'mm' ? 'selected' : ''}>mm</option>
                                <option value="cm" ${item.unit === 'cm' ? 'selected' : ''}>cm</option>
                                <option value="m" ${item.unit === 'm' ? 'selected' : ''}>m</option>
                                <option value="km" ${item.unit === 'km' ? 'selected' : ''}>km</option>
                                <option value="sqm" ${item.unit === 'sqm' ? 'selected' : ''}>sqm</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Quantity <span class="text-red-500">*</span></label>
                            <input type="number" name="quantity[]" min="1" required value="${item.quantity || ''}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Unit Cost (₱) <span class="text-red-500">*</span></label>
                            <input type="number" name="unit_cost[]" min="0" step="0.01" required value="${item.unit_cost || ''}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div class="md:col-span-3 mt-2">
                            <label class="block text-sm font-medium text-gray-700">Item Description <span class="text-red-500">*</span></label>
                            <textarea name="item_description[]" rows="3" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">${item.item_description || ''}</textarea>
                        </div>
                        <div class="md:col-span-3 mt-2 flex justify-end">
                            <button type="button" class="remove-edit-item-btn bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                                Remove Item
                            </button>
                        </div>
                    </div>
                `;

                // Add remove functionality
                const removeBtn = itemDiv.querySelector('.remove-edit-item-btn');
                removeBtn.addEventListener('click', function() {
                    const itemsContainer = document.getElementById('edit-items-container');
                    if (itemsContainer.children.length > 1) {
                        itemDiv.remove();
                    } else {
                        showErrorAlert('At least one item is required');
                    }
                });

                return itemDiv;
            }

            function setupEditAddItemButton() {
                const addBtn = document.getElementById('edit-add-item-btn');
                // Remove any existing listeners
                const newAddBtn = addBtn.cloneNode(true);
                addBtn.parentNode.replaceChild(newAddBtn, addBtn);

                newAddBtn.addEventListener('click', function() {
                    const itemsContainer = document.getElementById('edit-items-container');
                    const newItemDiv = createEditItemDiv({}, itemsContainer.children.length);
                    itemsContainer.appendChild(newItemDiv);
                });
            }

            function closeEditModal() {
                window.dispatchEvent(new CustomEvent('close-modal', {
                    detail: 'edit-pr-modal'
                }));
            }

            // Handle form submission - only for edit form
            document.addEventListener('DOMContentLoaded', function() {
                const editForm = document.getElementById('edit-pr-form');
                if (editForm) {
                    editForm.addEventListener('submit', function(e) {
                        e.preventDefault();

                        // Clear previous error messages
                        clearValidationErrors();

                        const formData = new FormData(this);

                        fetch(this.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                }
                            })
                            .then(response => {
                                return response.text().then(text => {
                                    try {
                                        const jsonData = JSON.parse(text);
                                        return {
                                            success: true,
                                            data: jsonData
                                        };
                                    } catch (e) {
                                        return {
                                            success: false,
                                            html: text
                                        };
                                    }
                                });
                            })
                            .then(result => {
                                if (result.success) {
                                    if (result.data.success) {
                                        console.log('Success! Showing alert...'); // Add this debug line
                                        closeEditModal();
                                        showSuccessAlert('Purchase request updated successfully!');
                                        console.log('Alert should be visible now'); // Add this debug line
                                        setTimeout(() => {
                                                console.log('Reloading page...'); // Add this debug line
                                                window.location.reload();
                                            },
                                            3000
                                        ); // Increased to 3 seconds to give more time to see the alert
                                    } else {
                                        // Handle validation errors
                                        if (result.data.errors) {
                                            displayValidationErrors(result.data.errors);
                                        } else {
                                            showErrorAlert('Error updating purchase request: ' + (result
                                                .data.message || 'Unknown error'));
                                        }
                                    }
                                } else {
                                    // Parse HTML response for validation errors
                                    const errors = parseHtmlForErrors(result.html);
                                    if (errors) {
                                        displayValidationErrors(errors);
                                    } else {
                                        showErrorAlert(
                                            'Server returned HTML instead of JSON. Check console for details.'
                                        );
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Detailed error:', error);
                                showErrorAlert('Error updating purchase request: ' + error.message);
                            });
                    });
                }
            });

            // Function to clear all validation error messages
            function clearValidationErrors() {
                const errorElements = document.querySelectorAll('.validation-error');
                errorElements.forEach(element => element.remove());

                // Remove error styling from inputs
                const inputs = document.querySelectorAll('#edit-pr-form input, #edit-pr-form textarea');
                inputs.forEach(input => {
                    input.classList.remove('border-red-500');
                    input.classList.add('border-gray-300');
                });
            }

            // Function to display validation errors
            function displayValidationErrors(errors) {
                Object.keys(errors).forEach(fieldName => {
                    const field = document.querySelector(`[name="${fieldName}"]`);
                    if (field) {
                        // Add red border to the field
                        field.classList.remove('border-gray-300');
                        field.classList.add('border-red-500');

                        // Create error message element
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'validation-error text-red-600 text-sm mt-1';
                        errorDiv.textContent = errors[fieldName][0]; // Get first error message

                        // Insert error message after the field
                        field.parentNode.appendChild(errorDiv);
                    }
                });
            }

            // Function to parse HTML response for validation errors
            function parseHtmlForErrors(html) {
                // Look for common Laravel validation error patterns
                const errorMatches = html.match(/<li[^>]*>([^<]+)<\/li>/g);
                if (errorMatches) {
                    const errors = {};
                    errorMatches.forEach(match => {
                        const errorText = match.replace(/<[^>]*>/g, '');
                        // Try to extract field name from error message
                        const fieldMatch = errorText.match(/The (\w+)/i);
                        if (fieldMatch) {
                            const fieldName = fieldMatch[1].toLowerCase().replace(/\s+/g, '_');
                            if (!errors[fieldName]) {
                                errors[fieldName] = [];
                            }
                            errors[fieldName].push(errorText);
                        }
                    });
                    return Object.keys(errors).length > 0 ? errors : null;
                }
                return null;
            }

            function applyStatusFilter() {
                const status = document.getElementById('status-filter').value;
                const params = new URLSearchParams();
                if (status !== 'all') params.append('status', status);
                window.location.href = '{{ route('user.requests') }}' + (params.toString() ? '?' + params.toString() : '');
            }
        </script>

</x-page-layout>
