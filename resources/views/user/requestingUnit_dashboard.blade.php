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
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Dashboard') }}
                </h2>
                <div id="filter-dropdown" class="relative" x-data="{ open: false, showDatePicker: false }"
                    @open-date-picker.window="open = false; setTimeout(() => showDatePicker = true, 100)">
                    <button @click="open = !open"
                        class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                        Cancel
                                    </button>
                                    <button onclick="applyCustomDates()"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                        Apply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                            <div class="font-semibold text-2xl text-gray-700 my-2">
                                {{ $draftPRs }}
                            </div>
                            <div class="text-xs text-gray-400 tracking-wide">
                                <p><span class="text-gray-600 font-bold">₱{{ number_format($draftTotal, 2) }}</span></p>
                                <p>{{ __('total amount') }}</p>
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
                            <div class="font-semibold text-2xl text-gray-700 my-2">
                                {{ $approvedPRs }}
                            </div>
                            <div class="text-xs text-gray-400 tracking-wide">
                                <p><span
                                        class="text-gray-600 font-bold">₱{{ number_format($approvedTotal, 2) }}</span>
                                </p>
                                <p>{{ __('total amount') }}</p>
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center ml-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="2em" height="2em"
                                viewBox="0 0 24 24">
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
                            <div class="font-semibold text-2xl text-gray-700 my-2">
                                {{ $rejectedPRs }}
                            </div>
                            <div class="text-xs text-gray-400 tracking-wide">
                                <p><span
                                        class="text-gray-600 font-bold">₱{{ number_format($rejectedTotal, 2) }}</span>
                                </p>
                                <p>{{ __('total amount') }}</p>
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center ml-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="2em" height="2em"
                                viewBox="0 0 24 24">
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
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center ml-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="2em" height="2em"
                                viewBox="0 0 24 24">
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
                </div>
            </div>

        </div>
    </div>

</x-page-layout>
