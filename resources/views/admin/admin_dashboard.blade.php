<x-page-layout>
    <x-slot name="header">
        <x-app-header :homeUrl="route('admin')" :title="$pageTitle ?? __('DSWD-PRISM')" :userName="Auth::user()->first_name .
            (Auth::user()->middle_name ? ' ' . Auth::user()->middle_name : '') .
            ' ' .
            Auth::user()->last_name" :recentActivities="$recentActivities ?? collect()" />
    </x-slot>

    {{-- Main Page --}}
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
                                    alert('Please select both from and to dates');
                                    return;
                                }

                                if (new Date(dateFrom) > new Date(dateTo)) {
                                    alert('From date cannot be later than to date');
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
                            <div class="font-semibold text-2xl text-gray-700 my-2">
                                {{ $prCount }}
                            </div>
                            <div class="text-xs text-gray-400 tracking-wide">
                                <p><span class="text-gray-600 font-bold">₱{{ number_format($prTotal, 2) }}</span></p>
                                <p>{{ __('total amount') }}</p>
                            </div>
                        </div>
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center ml-4">
                            <i class="iconify w-8 h-8 text-blue-600" data-icon="mdi:file-document-outline"></i>
                        </div>
                    </div>
                </div>
                <!-- Total POs Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 flex flex-col justify-center">
                    <!--  h-64 p-6 -->
                    <div class="px-8 py-6 flex items-center justify-between">
                        <div>
                            <div class="font-semibold text-gray-400 tracking-wide">
                                {{ __('Total POs') }}
                            </div>
                            <div class="font-semibold text-2xl text-gray-700 my-2">
                                {{ $poCount }}
                            </div>
                            <div class="text-xs text-gray-400 tracking-wide">
                                <p><span class="text-gray-600 font-bold">₱{{ number_format($poTotal, 2) }}</span></p>
                                <p>{{ __('total amount') }}</p>
                            </div>
                        </div>
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center ml-4">
                            <i class="iconify w-8 h-8 text-green-600" data-icon="mdi:cart-outline"></i>
                        </div>
                    </div>
                </div>

            </div>

        </div>

</x-page-layout>
