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
                    {{-- add this between svg and fill if needed [xmlns="http://www.w3.org/2000/svg"] --}}
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
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
                        {{-- add this between svg and fill [xmlns="http://www.w3.org/2000/svg"] --}}
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="w-7 h-7">
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

    <!-- Main Content -->
    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Header with Create Button -->
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('My Purchase Request') }}
                </h2>
                <!-- Dropdown for Create Options -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        <span>Add New PR</span>
                        {{-- Icon for dropdown --}}
                        {{-- <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg> --}}
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                        <div class="py-1">
                            <a href="{{ route('purchase-requests.create') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                <svg class="w-4 h-4 mr-3 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Create PR Form
                            </a>
                            {{-- {{ route('purchase-requests.upload') }} --}}
                            <a href="{{ route('uploaded-documents.upload') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                <svg class="w-4 h-4 mr-3 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                    </path>
                                </svg>
                                Upload Scanned Copy
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Controls -->
            <div class="bg-white overflow-visible shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2">
                    {{-- <div class="flex items-center gap-2 w-full md:w-auto">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" placeholder="Search PRs..."
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full md:w-64">
                        </div>
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                            Search
                        </button>
                    </div> --}}
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ __('Request Monitoring') }}
                    </h3>


                    <div class="flex items-center gap-2">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
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
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200 p-2">
                                <ul>
                                    <li>
                                        <a href="{{ route('user.requests') }}"
                                            class="block px-4 py-2 text-gray-700 hover:bg-blue-100 {{ !request('status') || request('status') == 'all' ? 'font-bold text-blue-600' : '' }}">
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
                                            <a href="{{ route('user.requests', ['status' => $status]) }}"
                                                class="block px-4 py-2 text-gray-700 hover:bg-blue-100 {{ request('status') == $status ? 'font-bold text-blue-600' : '' }}">
                                                {{ $statusDisplayMap[$status] ?? ucfirst($status) }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="relative" id="export-dropdown-container">
                            <button type="button" id="export-btn"
                                class="flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg font-semibold hover:bg-green-200 transition">
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

                <!-- Purchase Request Table -->
                @if ($purchaseRequests->count() > 0)
                    <div class="overflow-x-auto">
                        <table id="purchase-requests-table" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
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
                                                    ]);
                                                @endphp
                                                <button onclick="openViewModal({{ $pr->id }})"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium
                {{ $showEdit ? '' : 'w-28' }}">
                                                    View
                                                </button>
                                                @if ($showEdit)
                                                    <button onclick="openEditModal({{ $pr->id }})"
                                                        class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 px-3 py-1 rounded text-sm font-medium">
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

                    <!-- Pagination - Only show when there are items -->
                    <div class="flex items-center justify-between mt-4">
                        <div class="text-sm text-gray-700">
                            Showing {{ $purchaseRequests->firstItem() ?? 0 }} to
                            {{ $purchaseRequests->lastItem() ?? 0 }}
                            of {{ $purchaseRequests->total() }} results
                        </div>
                        <div class="flex items-center space-x-2">
                            {{ $purchaseRequests->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No purchase requests found</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating your first purchase request.</p>
                        <div class="mt-4">
                            <a href="{{ route('purchase-requests.create') }}"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Create your first PR
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Uploaded Documents Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ __('Uploaded Documents') }}
                    </h3>
                    {{-- <a href="{{ route('uploaded-documents.upload') }}"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                            </path>
                        </svg>
                        <span>Upload New Document</span>
                    </a> --}}
                    <div class="flex items-center gap-2">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
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
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200 p-2">
                                <ul>
                                    <li>
                                        <a href="{{ route('user.requests', array_merge(request()->except('file_type'), ['file_type' => 'all'])) }}"
                                            class="block px-4 py-2 text-gray-700 hover:bg-blue-100 {{ !request('file_type') || request('file_type') == 'all' ? 'font-bold text-blue-600' : '' }}">
                                            All File Types
                                        </a>
                                    </li>
                                    @foreach ($fileTypes as $type)
                                        <li>
                                            <a href="{{ route('user.requests', array_merge(request()->except('file_type'), ['file_type' => $type])) }}"
                                                class="block px-4 py-2 text-gray-700 hover:bg-blue-100 {{ request('file_type') == $type ? 'font-bold text-blue-600' : '' }}">
                                                {{ strtoupper($type) }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="relative" id="export-dropdown-container-docs">
                            <button type="button" id="export-btn-docs"
                                class="flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg font-semibold hover:bg-green-200 transition">
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="size-5 mr-3">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                                </svg>
                                Export
                            </button>
                            <div id="export-dropdown-docs"
                                class="hidden absolute right-0 mt-2 w-36 bg-white border border-gray-200 rounded shadow-lg z-50">
                                <form id="export-xlsx-docs-form" method="POST"
                                    action="{{ route('uploaded-documents.export.xlsx') }}">
                                    @csrf
                                    <input type="hidden" name="file_type"
                                        value="{{ request('file_type', 'all') }}">
                                    <button type="submit"
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 w-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em"
                                            viewBox="0 0 16 16" class="mr-3">
                                            <path fill="currentColor" fill-rule="evenodd"
                                                d="M14 4.5V11h-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5zM7.86 14.841a1.13 1.13 0 0 0 .401.823q.195.162.479.252q.284.091.665.091q.507 0 .858-.158q.355-.158.54-.44a1.17 1.17 0 0 0 .187-.656q0-.336-.135-.56a1 1 0 0 0-.375-.357a2 2 0 0 0-.565-.21l-.621-.144a1 1 0 0 1-.405-.176a.37.37 0 0 1-.143-.299q0-.234.184-.384q.188-.152.513-.152q.214 0 .37.068a.6.6 0 0 1 .245.181a.56.56 0 0 1 .12.258h.75a1.1 1.1 0 0 0-.199-.566a1.2 1.2 0 0 0-.5-.41a1.8 1.8 0 0 0-.78-.152q-.44 0-.777.15q-.336.149-.527.421q-.19.273-.19.639q0 .302.123.524t.351.367q.229.143.54.213l.618.144q.31.073.462.193a.39.39 0 0 1 .153.326a.5.5 0 0 1-.085.29a.56.56 0 0 1-.255.193q-.168.07-.413.07q-.176 0-.32-.04a.8.8 0 0 1-.249-.115a.58.58 0 0 1-.255-.384zm-3.726-2.909h.893l-1.274 2.007l1.254 1.992h-.908l-.85-1.415h-.035l-.853 1.415H1.5l1.24-2.016l-1.228-1.983h.931l.832 1.438h.036zm1.923 3.325h1.697v.674H5.266v-3.999h.791zm7.636-3.325h.893l-1.274 2.007l1.254 1.992h-.908l-.85-1.415h-.035l-.853 1.415h-.861l1.24-2.016l-1.228-1.983h.931l.832 1.438h.036z" />
                                        </svg>
                                        Export as XLSX
                                    </button>
                                </form>
                                <form id="export-pdf-docs-form" method="POST"
                                    action="{{ route('uploaded-documents.export.pdf') }}">
                                    @csrf
                                    <input type="hidden" name="file_type"
                                        value="{{ request('file_type', 'all') }}">
                                    <button type="submit"
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 w-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em"
                                            viewBox="0 0 24 24" class="mr-3">
                                            <path fill="currentColor"
                                                d="M18.53 9L13 3.47a.75.75 0 0 0-.53-.22H8A2.75 2.75 0 0 0 5.25 6v12A2.75 2.75 0 0 0 8 20.75h8A2.75 2.75 0 0 0 18.75 18V9.5a.75.75 0 0 0-.22-.5m-5.28-3.19l2.94 2.94h-2.94ZM16 19.25H8A1.25 1.25 0 0 1 6.75 18V6A1.25 1.25 0 0 1 8 4.75h3.75V9.5a.76.76 0 0 0 .75.75h4.75V18A1.25 1.25 0 0 1 16 19.25" />
                                            <path fill="currentColor"
                                                d="M13.49 14.85a3.15 3.15 0 0 1-1.31-1.66a4.44 4.44 0 0 0 .19-2a.8.8 0 0 0-1.52-.19a5 5 0 0 0 .25 2.4A29 29 0 0 1 9.83 16c-.71.4-1.68 1-1.83 1.69c-.12.56.93 2 2.72-1.12a19 19 0 0 1 2.44-.72a4.7 4.7 0 0 0 2 .61a.82.82 0 0 0 .62-1.38c-.42-.43-1.67-.31-2.29-.23m-4.78 3a4.3 4.3 0 0 1 1.09-1.24c-.68 1.08-1.09 1.27-1.09 1.25Zm2.92-6.81c.26 0 .24 1.15.06 1.46a3.1 3.1 0 0 1-.06-1.45Zm-.87 4.88a15 15 0 0 0 .88-1.92a3.9 3.9 0 0 0 1.08 1.26a12.4 12.4 0 0 0-1.96.67Zm4.7-.18s-.18.22-1.33-.28c1.25-.08 1.46.21 1.33.29Z" />
                                        </svg>
                                        Export as PDF
                                    </button>
                                </form>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const exportBtn = document.getElementById('export-btn-docs');
                                    const exportDropdown = document.getElementById('export-dropdown-docs');
                                    exportBtn.addEventListener('click', function(e) {
                                        e.stopPropagation();
                                        exportDropdown.classList.toggle('hidden');
                                    });
                                    document.addEventListener('click', function(e) {
                                        if (!exportDropdown.contains(e.target) && e.target !== exportBtn) {
                                            exportDropdown.classList.add('hidden');
                                        }
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>

                @if ($uploadedDocuments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        PR Number
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        File Name
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        File Type
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        File Size
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Upload Date
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($uploadedDocuments as $document)
                                    <tr>
                                        <td
                                            class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center">
                                            {{ $document->pr_number }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ $document->original_filename }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ strtoupper($document->file_type) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ $document->file_size_formatted }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ $document->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <div class="flex space-x-2 justify-center">
                                                <a href="{{ route('uploaded-documents.download', $document) }}"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium">
                                                    Download
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('uploaded-documents.destroy', $document) }}"
                                                    class="inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this document?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="bg-white hover:bg-red-50 text-red-700 border border-red-300 px-3 py-1 rounded text-sm font-medium">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex items-center justify-between mt-4">
                        <div class="text-sm text-gray-700">
                            Showing {{ $uploadedDocuments->firstItem() ?? 0 }} to
                            {{ $uploadedDocuments->lastItem() ?? 0 }}
                            of {{ $uploadedDocuments->total() }} results
                        </div>
                        <div class="flex items-center space-x-2">
                            {{ $uploadedDocuments->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No documents uploaded</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by uploading your first document.</p>
                        <div class="mt-4">
                            <a href="{{ route('uploaded-documents.upload') }}"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Upload Document
                            </a>
                        </div>
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Unit</label>
                        <p id="view-unit" class="mt-1 text-sm text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Quantity</label>
                        <p id="view-quantity" class="mt-1 text-sm text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Unit Cost</label>
                        <p id="view-unit-cost" class="mt-1 text-sm text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Cost</label>
                        <p id="view-total-cost" class="mt-1 text-sm text-gray-900"></p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Item Description</label>
                        <p id="view-item-description" class="mt-1 text-sm text-gray-900"></p>
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
                    <button id="print-btn" type="button"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg hidden"
                        onclick="openPrintView()">
                        Print
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
                            <label for="edit-unit" class="block text-sm font-medium text-gray-700">Unit <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="unit" id="edit-unit"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="edit-quantity" class="block text-sm font-medium text-gray-700">Quantity <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="quantity" id="edit-quantity" min="1"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="edit-unit-cost" class="block text-sm font-medium text-gray-700">Unit Cost
                                <span class="text-red-500">*</span></label>
                            <input type="number" name="unit_cost" id="edit-unit-cost" step="0.01"
                                min="0"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="edit-delivery-period" class="block text-sm font-medium text-gray-700">Delivery
                                Period <span class="text-red-500">*</span></label>
                            <input type="text" name="delivery_period" id="edit-delivery-period"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div class="md:col-span-2">
                            <label for="edit-item-description" class="block text-sm font-medium text-gray-700">Item
                                Description <span class="text-red-500">*</span></label>
                            <textarea name="item_description" id="edit-item-description" rows="3"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label for="edit-delivery-address"
                                class="block text-sm font-medium text-gray-700">Delivery
                                Address <span class="text-red-500">*</span></label>
                            <textarea name="delivery_address" id="edit-delivery-address" rows="3"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                        </div>
                        <div class="md:col-span-2">
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
                // Fetch purchase request data
                fetch(`/purchase-requests/${prId}/data`)
                    .then(response => response.json())
                    .then(data => {
                        // Populate modal fields
                        document.getElementById('view-pr-number').textContent = data.pr_number;
                        document.getElementById('view-pr-date').textContent = data.date;
                        document.getElementById('view-entity-name').textContent = data.entity_name;
                        document.getElementById('view-fund-cluster').textContent = data.fund_cluster;
                        document.getElementById('view-office-section').textContent = data.office_section;
                        document.getElementById('view-unit').textContent = data.unit;
                        document.getElementById('view-quantity').textContent = data.quantity;
                        document.getElementById('view-unit-cost').textContent = '₱' + parseFloat(data.unit_cost)
                            .toLocaleString('en-US', {
                                minimumFractionDigits: 2
                            });
                        document.getElementById('view-total-cost').textContent = '₱' + parseFloat(data.total_cost)
                            .toLocaleString('en-US', {
                                minimumFractionDigits: 2
                            });
                        document.getElementById('view-item-description').textContent = data.item_description;
                        document.getElementById('view-delivery-address').textContent = data.delivery_address;
                        document.getElementById('view-purpose').textContent = data.purpose;
                        document.getElementById('view-requested-by').textContent = data.requested_by_name;
                        document.getElementById('view-delivery-period').textContent = data.delivery_period;

                        // Set status with color
                        const statusDisplayMap = {
                            'draft': 'Draft',
                            'pending': 'Pending',
                            'approved': 'Approved',
                            'rejected': 'Rejected',
                            'po_generated': 'PO Generated',
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
                            if (data.status === 'approved' || data.status === 'po_generated') {
                                printBtn.classList.remove('hidden');
                            } else {
                                printBtn.classList.add('hidden');
                            }
                        }

                        document.getElementById('view-pr-id').textContent = data.id;

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

            function closeViewModal() {
                window.dispatchEvent(new CustomEvent('close-modal', {
                    detail: 'view-pr-modal'
                }));
            }

            function openEditModal(prId) {
                // Fetch purchase request data
                fetch(`/purchase-requests/${prId}/data`)
                    .then(response => response.json())
                    .then(data => {
                        // Populate form fields
                        document.getElementById('edit-entity-name').value = data.entity_name;
                        document.getElementById('edit-fund-cluster').value = data.fund_cluster;
                        document.getElementById('edit-office-section').value = data.office_section;
                        document.getElementById('edit-date').value = data.date;
                        document.getElementById('edit-unit').value = data.unit;
                        document.getElementById('edit-quantity').value = data.quantity;
                        document.getElementById('edit-unit-cost').value = data.unit_cost;
                        document.getElementById('edit-delivery-period').value = data.delivery_period;
                        document.getElementById('edit-item-description').value = data.item_description;
                        document.getElementById('edit-delivery-address').value = data.delivery_address;
                        document.getElementById('edit-purpose').value = data.purpose;

                        // Set form action
                        document.getElementById('edit-pr-form').action = `/purchase-requests/${prId}/update`;

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
            // // Global error handling for edit form submission
            // // Uncomment this section if you want to handle the edit form submission with JavaScript
            // // This is an alternative to the inline form submission handler above.
            // document.addEventListener('DOMContentLoaded', function() {
            //     const editForm = document.getElementById('edit-pr-form');
            //     if (editForm) {
            //         editForm.addEventListener('submit', function(e) {
            //             e.preventDefault();

            //             const formData = new FormData(this);

            //             // Debug: Log the form action
            //             console.log('Form action:', this.action);
            //             console.log('Form data:', Object.fromEntries(formData));

            //             fetch(this.action, {
            //                     method: 'POST',
            //                     body: formData,
            //                     headers: {
            //                         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
            //                             .getAttribute('content')
            //                     }
            //                 })
            //                 .then(response => {
            //                     console.log('Response status:', response.status);
            //                     console.log('Response URL:', response.url);

            //                     // Check if response is JSON
            //                     const contentType = response.headers.get('content-type');
            //                     console.log('Content-Type:', contentType);

            //                     if (!response.ok) {
            //                         return response.text().then(text => {
            //                             console.log('Error response body:', text.substring(0,
            //                                 200)); // First 200 chars
            //                             throw new Error(
            //                                 `HTTP ${response.status}: ${text.substring(0, 100)}`
            //                             );
            //                         });
            //                     }

            //                     if (contentType && contentType.includes('application/json')) {
            //                         return response.json();
            //                     } else {
            //                         return response.text().then(text => {
            //                             console.log('Non-JSON response:', text.substring(0, 200));
            //                             throw new Error('Server returned HTML instead of JSON');
            //                         });
            //                     }
            //                 })
            //                 .then(data => {
            //                     console.log('Success response:', data);
            //                     if (data.success) {
            //                         closeEditModal();
            //                         alert('Purchase request updated successfully!');
            //                         window.location.reload();
            //                     } else {
            //                         alert('Error updating purchase request: ' + (data.message ||
            //                             'Unknown error'));
            //                     }
            //                 })
            //                 .catch(error => {
            //                     console.error('Detailed error:', error);
            //                     alert('Error updating purchase request: ' + error.message);
            //                 });
            //         });
            //     }
            // });
        </script>

</x-page-layout>
