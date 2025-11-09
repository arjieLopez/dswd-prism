<x-page-layout>
    <x-slot name="header">
        <x-app-header :homeUrl="route('admin')" :title="$pageTitle ?? __('DSWD-PRISM')" :userName="Auth::user()->first_name .
            (Auth::user()->middle_name ? ' ' . Auth::user()->middle_name : '') .
            ' ' .
            Auth::user()->last_name" :recentActivities="$recentActivities ?? collect()" />
    </x-slot>

    <!-- Main Content -->
    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Reports') }}
                </h2>
            </div>

            <div class="bg-white overflow-visible shadow-sm sm:rounded-lg p-6">
                <!-- Search, Filter, Export Controls -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2">
                    <form method="GET" action="{{ route('admin.reports') }}"
                        class="flex items-center gap-2 w-full md:w-auto">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" name="search" placeholder="Search by PR/PO number or department..."
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
                                            <a href="{{ route('admin.reports', array_filter(['search' => request('search'), 'date_from' => request('date_from'), 'date_to' => request('date_to')])) }}"
                                                class="block px-4 py-2 text-gray-700 hover:bg-blue-100 rounded {{ !request('status') || request('status') == 'all' ? 'font-bold text-blue-600 bg-blue-50' : '' }}">
                                                All Statuses
                                            </a>
                                        </li>
                                        @php
                                            $statusDisplayMap = [
                                                'approved' => 'Approved',
                                                'po_generated' => 'PO Generated',
                                                'completed' => 'Completed',
                                            ];
                                        @endphp
                                        @foreach ($statusDisplayMap as $status => $display)
                                            <li>
                                                <a href="{{ route('admin.reports', array_filter(['status' => $status, 'search' => request('search'), 'date_from' => request('date_from'), 'date_to' => request('date_to')])) }}"
                                                    class="block px-4 py-2 text-gray-700 hover:bg-blue-100 rounded {{ request('status') == $status ? 'font-bold text-blue-600 bg-blue-50' : '' }}">
                                                    {{ $display }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <!-- Date Filter Content -->
                                <div x-show="activeTab === 'date'" class="p-4">
                                    <form method="GET" action="{{ route('admin.reports') }}" class="space-y-4">
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
                                                Apply Filter
                                            </button>
                                            @if (request('date_from') || request('date_to'))
                                                <a href="{{ route('admin.reports', array_filter(['search' => request('search'), 'status' => request('status')])) }}"
                                                    class="relative flex-1 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-bold py-2 px-4 rounded-lg text-sm text-center
                                                           hover:from-gray-600 hover:to-gray-700 hover:shadow-lg hover:scale-105
                                                           active:from-gray-700 active:to-gray-800 active:scale-95 active:shadow-inner
                                                           transition-all duration-200 ease-in-out transform
                                                           before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                                                           hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
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

                        <a href="{{ route('admin.reports') }}"
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 hover:bg-red-200 transition-colors">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear All
                        </a>
                    </div>
                @endif

                <!-- Table -->
                @if ($reports->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        #</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Document Number</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Department</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php $counter = ($reports->currentPage() - 1) * $reports->perPage() + 1; @endphp
                                @foreach ($reports as $report)
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ $counter++ }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $report->type }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $report->document_number }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $report->department }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $report->status_color }}">{{ $report->status_display }}</span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            ₱{{ number_format($report->amount, 2) }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <button
                                                onclick="viewReport({{ $report->id }}, '{{ $report->type }}', '{{ $report->status }}')"
                                                class="relative bg-gradient-to-r from-blue-500 to-blue-600 text-white px-3 py-1 rounded-lg text-sm font-medium w-28
                                                       hover:from-blue-600 hover:to-blue-700 hover:shadow-lg hover:scale-105
                                                       active:from-blue-700 active:to-blue-800 active:scale-95 active:shadow-inner
                                                       transition-all duration-200 ease-in-out transform
                                                       before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                                                       hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
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
                @else
                    <div class="text-center py-12">
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
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No reports found</h3>
                            <p class="mt-1 text-sm text-gray-500">No reports are available for the selected criteria.
                            </p>
                        @endif
                    </div>
                @endif

                <!-- Custom Pagination - Only show when there are more than 10 items -->
                @if ($reports->total() > 10)
                    <div class="flex justify-center mt-6">
                        <div class="flex items-center space-x-1">
                            @if ($reports->onFirstPage())
                                <span class="px-3 py-2 text-gray-400 cursor-not-allowed">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </span>
                            @else
                                <a href="{{ $reports->appends(request()->query())->previousPageUrl() }}"
                                    class="px-3 py-2 text-gray-600 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </a>
                            @endif

                            @php
                                $start = max(1, $reports->currentPage() - 2);
                                $end = min($reports->lastPage(), $reports->currentPage() + 2);

                                if ($end - $start < 4) {
                                    if ($start == 1) {
                                        $end = min($reports->lastPage(), $start + 4);
                                    } else {
                                        $start = max(1, $end - 4);
                                    }
                                }
                            @endphp

                            @if ($start > 1)
                                <a href="{{ $reports->appends(request()->query())->url(1) }}"
                                    class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">1</a>
                                @if ($start > 2)
                                    <span class="px-2 py-2 text-gray-400">...</span>
                                @endif
                            @endif

                            @for ($page = $start; $page <= $end; $page++)
                                @if ($page == $reports->currentPage())
                                    <span
                                        class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md">{{ $page }}</span>
                                @else
                                    <a href="{{ $reports->appends(request()->query())->url($page) }}"
                                        class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">{{ $page }}</a>
                                @endif
                            @endfor

                            @if ($end < $reports->lastPage())
                                @if ($end < $reports->lastPage() - 1)
                                    <span class="px-2 py-2 text-gray-400">...</span>
                                @endif
                                <a href="{{ $reports->appends(request()->query())->url($reports->lastPage()) }}"
                                    class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">{{ $reports->lastPage() }}</a>
                            @endif

                            @if ($reports->hasMorePages())
                                <a href="{{ $reports->appends(request()->query())->nextPageUrl() }}"
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
            </div>

        </div>
    </div>

    <!-- View PR Modal -->
    <x-modal name="view-pr-modal" maxWidth="2xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Purchase Request Details</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeViewPRModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="view-pr-content">
                <!-- Content will be loaded by JS -->
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button"
                    class="relative bg-gradient-to-r from-gray-500 to-gray-600 text-white font-bold py-2 px-4 rounded-lg
                           hover:from-gray-600 hover:to-gray-700 hover:shadow-lg hover:scale-105
                           active:from-gray-700 active:to-gray-800 active:scale-95 active:shadow-inner
                           transition-all duration-200 ease-in-out transform
                           before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                           hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200"
                    onclick="closeViewPRModal()">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Close
                </button>
            </div>
        </div>
    </x-modal>

    <!-- View PO Modal -->
    <x-modal name="view-po-modal" maxWidth="2xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Purchase Order Details</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeViewPOModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="view-po-content">
                <!-- Content will be loaded by JS -->
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button"
                    class="relative bg-gradient-to-r from-gray-500 to-gray-600 text-white font-bold py-2 px-4 rounded-lg
                           hover:from-gray-600 hover:to-gray-700 hover:shadow-lg hover:scale-105
                           active:from-gray-700 active:to-gray-800 active:scale-95 active:shadow-inner
                           transition-all duration-200 ease-in-out transform
                           before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                           hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200"
                    onclick="closeViewPOModal()">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Close
                </button>
            </div>
        </div>
    </x-modal>

    <script>
        function viewReport(id, type, status) {
            console.log('Viewing report:', id, 'Type:', type, 'Status:', status);

            // Determine which modal to show based on type
            if (type === 'PO') {
                // Show PO modal
                openViewPOModal(id);
            } else if (type === 'PR') {
                // Show PR modal
                openViewPRModal(id);
            } else {
                console.error('Unknown type:', type);
            }
        }

        function openViewPRModal(prId) {
            fetch(`/admin/reports/pr/${prId}/data`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    let html = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">PR Number</label>
                            <p class="mt-1 text-sm text-gray-900">${data.pr_number ?? ''}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date</label>
                            <p class="mt-1 text-sm text-gray-900">${data.date ?? ''}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Entity Name</label>
                            <p class="mt-1 text-sm text-gray-900">${data.entity_name ?? ''}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fund Cluster</label>
                            <p class="mt-1 text-sm text-gray-900">${data.fund_cluster ?? ''}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Office Section</label>
                            <p class="mt-1 text-sm text-gray-900">${data.office_section ?? ''}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full ${data.status_color}">
                                ${data.status_display ?? ''}
                            </span>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Items</label>
                            <div style="max-height: 220px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 0.5rem;">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-2 py-1 text-left font-semibold">Unit</th>
                                            <th class="px-2 py-1 text-left font-semibold">Qty</th>
                                            <th class="px-2 py-1 text-left font-semibold">Unit Cost</th>
                                            <th class="px-2 py-1 text-left font-semibold">Total Cost</th>
                                            <th class="px-2 py-1 text-left font-semibold">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                    if (data.items && data.items.length > 0) {
                        let grandTotal = 0;
                        data.items.forEach((item, index) => {
                            const itemTotal = parseFloat(item.total_cost);
                            grandTotal += itemTotal;
                            html += `
                            <tr class="border-b">
                                <td class="px-2 py-1 text-xs">${item.unit}</td>
                                <td class="px-2 py-1 text-xs">${item.quantity}</td>
                                <td class="px-2 py-1 text-xs">₱${parseFloat(item.unit_cost).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                <td class="px-2 py-1 text-xs">₱${itemTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                <td class="px-2 py-1 text-xs" style="max-width: 200px; word-wrap: break-word;">${item.item_description}</td>
                            </tr>
                        `;
                        });

                        html += `
                        <tr class="bg-gray-50 border-t-2 border-gray-300">
                            <td class="px-2 py-1 font-semibold" colspan="3">Grand Total:</td>
                            <td class="px-2 py-1 font-semibold text-green-600">₱${grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                            <td class="px-2 py-1"></td>
                        </tr>
                    `;
                    } else {
                        html +=
                            '<tr><td colspan="5" class="px-2 py-4 text-center text-gray-500">No items found</td></tr>';
                    }

                    html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Delivery Address</label>
                            <p class="mt-1 text-sm text-gray-900">${data.delivery_address ?? ''}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Purpose</label>
                            <p class="mt-1 text-sm text-gray-900">${data.purpose ?? ''}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Requested By</label>
                            <p class="mt-1 text-sm text-gray-900">${data.requested_by_name ?? ''}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Delivery Period</label>
                            <p class="mt-1 text-sm text-gray-900">${data.delivery_period ?? ''}</p>
                        </div>
                    </div>
                `;

                    document.getElementById('view-pr-content').innerHTML = html;
                    window.dispatchEvent(new CustomEvent('open-modal', {
                        detail: 'view-pr-modal'
                    }));
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorAlert('Error loading PR details: ' + error.message);
                });
        }

        function openViewPOModal(poId) {
            fetch(`/admin/reports/po/${poId}/data`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    let html = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700">PR Number</label>
                            <p class="mt-1 text-sm text-gray-900">${data.pr_number ?? ''}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Date</label>
                            <p class="mt-1 text-sm text-gray-900">${data.po_generated_at ?? ''}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Supplier</label>
                            <p class="mt-1 text-sm text-gray-900">${data.supplier_name ?? ''}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">PO Number</label>
                            <p class="mt-1 text-sm text-gray-900">${data.po_number ?? ''}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Supplier Address</label>
                            <p class="mt-1 text-sm text-gray-900">${data.supplier_address ?? ''}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">TIN</label>
                            <p class="mt-1 text-sm text-gray-900">${data.supplier_tin ?? ''}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Mode of Procurement</label>
                            <p class="mt-1 text-sm text-gray-900">${data.mode_of_procurement ?? ''}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Place of Delivery</label>
                            <p class="mt-1 text-sm text-gray-900">${data.place_of_delivery ?? ''}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Delivery Term</label>
                            <p class="mt-1 text-sm text-gray-900">${data.delivery_term ?? ''}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Payment Term</label>
                            <p class="mt-1 text-sm text-gray-900">${data.payment_term ?? ''}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Date of Delivery</label>
                            <p class="mt-1 text-sm text-gray-900">${data.date_of_delivery ?? ''}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Requesting Unit</label>
                            <p class="mt-1 text-sm text-gray-900">${data.requesting_unit ?? ''}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Items</label>
                            <div style="max-height: 220px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 0.5rem;">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-2 py-1 text-left font-semibold">#</th>
                                            <th class="px-2 py-1 text-left font-semibold">Unit</th>
                                            <th class="px-2 py-1 text-left font-semibold">Qty</th>
                                            <th class="px-2 py-1 text-left font-semibold">Unit Cost</th>
                                            <th class="px-2 py-1 text-left font-semibold">Total Cost</th>
                                            <th class="px-2 py-1 text-left font-semibold">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                    if (data.items && data.items.length > 0) {
                        let grandTotal = 0;
                        data.items.forEach((item, index) => {
                            const itemTotal = parseFloat(item.total_cost);
                            grandTotal += itemTotal;
                            html += `
                            <tr class="border-b">
                                <td class="px-2 py-1 text-xs">${index + 1}</td>
                                <td class="px-2 py-1 text-xs">${item.unit}</td>
                                <td class="px-2 py-1 text-xs">${item.quantity}</td>
                                <td class="px-2 py-1 text-xs">₱${parseFloat(item.unit_cost).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                <td class="px-2 py-1 text-xs">₱${itemTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                <td class="px-2 py-1 text-xs" style="max-width: 200px; word-wrap: break-word;">${item.item_description}</td>
                            </tr>
                        `;
                        });

                        html += `
                        <tr class="bg-gray-50 border-t-2 border-gray-300">
                            <td class="px-2 py-1 font-semibold" colspan="5">Grand Total:</td>
                            <td class="px-2 py-1 font-semibold text-green-600">₱${grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        </tr>
                    `;
                    } else {
                        html +=
                            '<tr><td colspan="6" class="px-2 py-4 text-center text-gray-500">No items found</td></tr>';
                    }

                    html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700">Delivery Address</label>
                            <p class="mt-1 text-sm text-gray-900">${data.delivery_address ?? ''}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700">Purpose</label>
                            <p class="mt-1 text-sm text-gray-900">${data.purpose ?? ''}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Requested By</label>
                            <p class="mt-1 text-sm text-gray-900">${data.requested_by_name ?? ''}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Delivery Period</label>
                            <p class="mt-1 text-sm text-gray-900">${data.delivery_period ?? ''}</p>
                        </div>
                    </div>
                `;

                    document.getElementById('view-po-content').innerHTML = html;
                    window.dispatchEvent(new CustomEvent('open-modal', {
                        detail: 'view-po-modal'
                    }));
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorAlert('Error loading PO details: ' + error.message);
                });
        }

        function closeViewPRModal() {
            window.dispatchEvent(new CustomEvent('close-modal', {
                detail: 'view-pr-modal'
            }));
        }

        function closeViewPOModal() {
            window.dispatchEvent(new CustomEvent('close-modal', {
                detail: 'view-po-modal'
            }));
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
    </script>

</x-page-layout>
