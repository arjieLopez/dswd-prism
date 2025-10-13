<x-page-layout>
    <x-slot name="header">
        <x-app-header :homeUrl="route('staff')" :title="$pageTitle ?? __('DSWD-PRISM')" :userName="Auth::user()->first_name .
            (Auth::user()->middle_name ? ' ' . Auth::user()->middle_name : '') .
            ' ' .
            Auth::user()->last_name" :recentActivities="$recentActivities ?? collect()" />
    </x-slot>

    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('GSO Dashboard') }}
                </h2>
                <div id="filter-dropdown" class="relative" x-data="{ open: false, showDatePicker: false }"
                    @open-date-picker.window="open = false; setTimeout(() => showDatePicker = true, 100)">
                    <button @click="open = !open"
                        class="relative flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white
                               hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 hover:border-gray-400 hover:shadow-lg hover:scale-105
                               active:from-gray-100 active:to-gray-200 active:scale-95 active:shadow-inner
                               transition-all duration-200 ease-in-out transform
                               before:absolute before:inset-0 before:bg-gray-600 before:opacity-0 before:rounded-lg
                               hover:before:opacity-5 active:before:opacity-10 before:transition-opacity before:duration-200">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span class="text-gray-700 font-medium" id="selected-period-text">
                            @php
                                $filterType = request('filter_type', 'this_month');
                                $displayText = 'This Month';
                                if ($filterType === 'previous_month') {
                                    $displayText = 'Previous Month';
                                } elseif ($filterType === 'custom') {
                                    $dateFrom = request('date_from');
                                    $dateTo = request('date_to');
                                    if ($dateFrom && $dateTo) {
                                        $displayText =
                                            date('M d, Y', strtotime($dateFrom)) .
                                            ' - ' .
                                            date('M d, Y', strtotime($dateTo));
                                    } else {
                                        $displayText = 'Custom Period';
                                    }
                                }
                            @endphp
                            {{ $displayText }}
                        </span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50 py-1">

                        <button onclick="filterByPeriod('this_month')"
                            class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 {{ request('filter_type', 'this_month') === 'this_month' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700' }}">
                            This Month
                        </button>

                        <button onclick="filterByPeriod('previous_month')"
                            class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 {{ request('filter_type') === 'previous_month' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700' }}">
                            Previous Month
                        </button>

                        <button onclick="openCustomDatePicker()"
                            class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 {{ request('filter_type') === 'custom' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700' }}">
                            Custom Period
                        </button>
                    </div>

                    <!-- Custom Date Picker Modal -->
                    <div x-show="showDatePicker" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
                        @click="showDatePicker = false">

                        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
                            <div class="mt-3">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Select Custom Period</h3>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                                        <input type="date" id="date-from" value="{{ request('date_from') }}"
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                                        <input type="date" id="date-to" value="{{ request('date_to') }}"
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-2 mt-6">
                                    <button @click="showDatePicker = false"
                                        class="relative bg-gradient-to-r from-gray-500 to-gray-600 text-white font-bold py-2 px-4 rounded-lg
                                               hover:from-gray-600 hover:to-gray-700 hover:shadow-lg hover:scale-105
                                               active:from-gray-700 active:to-gray-800 active:scale-95 active:shadow-inner
                                               transition-all duration-200 ease-in-out transform
                                               before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                                               hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Cancel
                                    </button>
                                    <button onclick="applyCustomDates()"
                                        class="relative bg-gradient-to-r from-blue-500 to-blue-600 text-white font-bold py-2 px-4 rounded-lg
                                               hover:from-blue-600 hover:to-blue-700 hover:shadow-lg hover:scale-105
                                               active:from-blue-700 active:to-blue-800 active:scale-95 active:shadow-inner
                                               transition-all duration-200 ease-in-out transform
                                               before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                                               hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Apply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-4 gap-6">
                <!-- Pending PRs Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 flex flex-col justify-center">
                    <div class="px-8 py-6 flex items-center justify-between">
                        <div>
                            <div class="w-20 font-semibold text-gray-400 tracking-wide">
                                {{ __('Pending') }}
                            </div>
                            <div class="font-semibold text-2xl text-gray-700 my-2">
                                {{ $pendingPRs }}
                            </div>
                            <div class="text-xs text-gray-400 tracking-wide">
                                <p><span class="text-gray-600 font-bold">₱{{ number_format($pendingTotal, 2) }}</span>
                                </p>
                                <p>{{ __('total amount') }}</p>
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center ml-4">
                            <i class="iconify w-6 h-6 text-yellow-600" data-icon="mdi:clock-outline"></i>
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
                            <div class="font-semibold text-2xl text-gray-700 my-2">
                                {{ $approvedPRs }}
                            </div>
                            <div class="text-xs text-gray-400 tracking-wide">
                                <p><span class="text-gray-600 font-bold">₱{{ number_format($approvedTotal, 2) }}</span>
                                </p>
                                <p>{{ __('total amount') }}</p>
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center ml-4">
                            <i class="iconify w-6 h-6 text-green-600" data-icon="mdi:check-circle-outline"></i>
                        </div>
                    </div>
                </div>

                <!-- POs Generated Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 flex flex-col justify-center">
                    <div class="px-8 py-6 flex items-center justify-between">
                        <div>
                            <div class="w-20 font-semibold text-gray-400 tracking-wide">
                                {{ __('Generated') }}
                            </div>
                            <div class="font-semibold text-2xl text-gray-700 my-2">
                                {{ $poGenerated }}
                            </div>
                            <div class="text-xs text-gray-400 tracking-wide">
                                <p><span
                                        class="text-gray-600 font-bold">₱{{ number_format($poGeneratedTotal, 2) }}</span>
                                </p>
                                <p>{{ __('total amount') }}</p>
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center ml-4">
                            <i class="iconify w-6 h-6 text-blue-600" data-icon="mdi:cart-outline"></i>
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
                            <div class="font-semibold text-2xl text-gray-700 my-2">
                                {{ $completedPRs }}
                            </div>
                            <div class="text-xs text-gray-400 tracking-wide">
                                <p><span
                                        class="text-gray-600 font-bold">₱{{ number_format($completedTotal, 2) }}</span>
                                </p>
                                <p>{{ __('total amount') }}</p>
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center ml-4">
                            <i class="iconify w-6 h-6 text-purple-600" data-icon="mdi:check-all"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Purchase Requests Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ __('Purchase Requests Monitoring') }}
                    </h3>
                </div>

                @if ($completedPRsList->count() > 0)
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
                                        PO #
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Requested By
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Supplier
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date of Delivery
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date Completed
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Amount
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($completedPRsList as $index => $pr)
                                    @php
                                        // Get the completed date - use completed_at if available, otherwise updated_at for completed status
                                        $completedDate = null;
                                        if (isset($pr->completed_at) && $pr->completed_at) {
                                            $completedDate = $pr->completed_at;
                                        } elseif ($pr->status === 'completed' && $pr->updated_at) {
                                            $completedDate = $pr->updated_at;
                                        }

                                        // Only mark as overdue if:
                                        // 1. There's a delivery date
// 2. The delivery date is past due (before today)
// 3. Either there's no completion date OR the completion date is after the delivery date
                                        $isOverdue =
                                            $pr->date_of_delivery &&
                                            $pr->date_of_delivery->startOfDay()->lt(now()->startOfDay()) &&
                                            (!$completedDate ||
                                                $completedDate->startOfDay()->gt($pr->date_of_delivery->startOfDay()));

                                        $rowClass = $isOverdue ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50';
                                    @endphp
                                    <tr class="{{ $rowClass }} transition-colors duration-200">
                                        <td
                                            class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center font-medium">
                                            {{ ($completedPRsList->currentPage() - 1) * $completedPRsList->perPage() + $index + 1 }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $pr->po_number ?: 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $pr->user ? $pr->user->first_name . (($pr->user->middle_name ? ' ' . $pr->user->middle_name : '') . ' ' . $pr->user->last_name) : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $pr->supplier ? $pr->supplier->supplier_name : 'Not Assigned' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-center">
                                            @if ($pr->date_of_delivery)
                                                <span
                                                    class="{{ $isOverdue ? 'text-red-700 font-semibold' : 'text-gray-900' }}">
                                                    {{ $pr->date_of_delivery->format('M d, Y') }}
                                                </span>
                                            @else
                                                <span class="text-gray-500">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            @if (isset($pr->completed_at) && $pr->completed_at)
                                                {{ $pr->completed_at->format('M d, Y') }}
                                            @elseif($pr->status === 'completed')
                                                {{ $pr->updated_at ? $pr->updated_at->format('M d, Y') : 'N/A' }}
                                            @else
                                                <span class="text-gray-500 italic">Pending</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            ₱{{ number_format($pr->total, 2) }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-center">
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $pr->status_color }}">
                                                {{ $pr->status_display }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Custom Pagination - Only show when there are more than 5 items -->
                    @if ($completedPRsList->total() > 5)
                        <div class="flex justify-center mt-6">
                            <div class="flex items-center space-x-1">
                                @if ($completedPRsList->onFirstPage())
                                    <span class="px-3 py-2 text-gray-400 cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </span>
                                @else
                                    <a href="{{ $completedPRsList->appends(request()->query())->previousPageUrl() }}"
                                        class="px-3 py-2 text-gray-600 hover:text-blue-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </a>
                                @endif

                                @php
                                    $start = max(1, $completedPRsList->currentPage() - 2);
                                    $end = min($completedPRsList->lastPage(), $completedPRsList->currentPage() + 2);

                                    if ($end - $start < 4) {
                                        if ($start == 1) {
                                            $end = min($completedPRsList->lastPage(), $start + 4);
                                        } else {
                                            $start = max(1, $end - 4);
                                        }
                                    }
                                @endphp

                                @if ($start > 1)
                                    <a href="{{ $completedPRsList->appends(request()->query())->url(1) }}"
                                        class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">1</a>
                                    @if ($start > 2)
                                        <span class="px-2 py-2 text-gray-400">...</span>
                                    @endif
                                @endif

                                @for ($page = $start; $page <= $end; $page++)
                                    @if ($page == $completedPRsList->currentPage())
                                        <span
                                            class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md">{{ $page }}</span>
                                    @else
                                        <a href="{{ $completedPRsList->appends(request()->query())->url($page) }}"
                                            class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">{{ $page }}</a>
                                    @endif
                                @endfor

                                @if ($end < $completedPRsList->lastPage())
                                    @if ($end < $completedPRsList->lastPage() - 1)
                                        <span class="px-2 py-2 text-gray-400">...</span>
                                    @endif
                                    <a href="{{ $completedPRsList->appends(request()->query())->url($completedPRsList->lastPage()) }}"
                                        class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">{{ $completedPRsList->lastPage() }}</a>
                                @endif

                                @if ($completedPRsList->hasMorePages())
                                    <a href="{{ $completedPRsList->appends(request()->query())->nextPageUrl() }}"
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
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No completed or PO generated purchase
                            requests</h3>
                        <p class="mt-1 text-sm text-gray-500">Completed and PO generated purchase requests will appear
                            here.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function filterByPeriod(filterType) {
            const url = new URL(window.location);
            url.searchParams.set('filter_type', filterType);

            // Remove any existing date parameters when switching to predefined periods
            if (filterType !== 'custom') {
                url.searchParams.delete('date_from');
                url.searchParams.delete('date_to');
            }

            window.location.href = url.toString();
        }

        function openCustomDatePicker() {
            // Try direct Alpine approach
            const filterElement = document.getElementById('filter-dropdown');
            if (filterElement && filterElement._x_dataStack && filterElement._x_dataStack[0]) {
                filterElement._x_dataStack[0].open = false;
                setTimeout(() => {
                    filterElement._x_dataStack[0].showDatePicker = true;
                }, 100);
            } else {
                // Fallback: Use Alpine's event system
                window.dispatchEvent(new CustomEvent('open-date-picker'));
            }
        }

        function applyCustomDates() {
            const dateFrom = document.getElementById('date-from').value;
            const dateTo = document.getElementById('date-to').value;

            if (!dateFrom || !dateTo) {
                showErrorAlert('Please select both from and to dates');
                return;
            }

            if (new Date(dateFrom) > new Date(dateTo)) {
                showErrorAlert('From date cannot be later than to date');
                return;
            }

            // Close the modal
            const filterDropdown = document.getElementById('filter-dropdown');
            if (filterDropdown && filterDropdown._x_dataStack) {
                filterDropdown._x_dataStack[0].showDatePicker = false;
            }

            const url = new URL(window.location);
            url.searchParams.set('filter_type', 'custom');
            url.searchParams.set('date_from', dateFrom);
            url.searchParams.set('date_to', dateTo);

            window.location.href = url.toString();
        }

        // Standardized alert functions - consistent with all other pages
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
