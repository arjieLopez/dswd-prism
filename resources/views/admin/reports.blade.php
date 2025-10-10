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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Search, Filter, Export Controls -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2">
                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <input type="text" placeholder="Search"
                            class="w-full md:w-64 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 placeholder-gray-400">
                        <button
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">Search</button>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 6h18M4 14h16M4 18h16">
                                </path>
                            </svg>
                            Filter
                        </button>
                        <button
                            class="flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg font-semibold hover:bg-green-200 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            Export
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Document
                                    Number</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php $counter = ($reports->currentPage() - 1) * $reports->perPage() + 1; @endphp
                            @forelse ($reports as $report)
                                <tr>
                                    <td class="px-4 py-2 text-center">{{ $counter++ }}</td>
                                    <td class="px-4 py-2">{{ $report->type }}</td>
                                    <td class="px-4 py-2">{{ $report->document_number }}</td>
                                    <td class="px-4 py-2">{{ $report->department }}</td>
                                    <td class="px-4 py-2">
                                        @if ($report->type === 'PO')
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                PO Generated
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $report->status_color }}">
                                                {{ $report->status_display }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">₱ {{ number_format($report->amount, 2) }}</td>
                                    <td class="px-4 py-2">
                                        <button onclick="viewReport({{ $report->id }})"
                                            class="bg-blue-500 text-white px-4 py-1 rounded-full font-semibold hover:bg-blue-600 transition">
                                            View
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        No reports found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Custom Pagination - Only show when there are more than 5 items -->
                @if ($reports->total() > 5)
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
                                <a href="{{ $reports->previousPageUrl() }}"
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
                                <a href="{{ $reports->url(1) }}"
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
                                    <a href="{{ $reports->url($page) }}"
                                        class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">{{ $page }}</a>
                                @endif
                            @endfor

                            @if ($end < $reports->lastPage())
                                @if ($end < $reports->lastPage() - 1)
                                    <span class="px-2 py-2 text-gray-400">...</span>
                                @endif
                                <a href="{{ $reports->url($reports->lastPage()) }}"
                                    class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">{{ $reports->lastPage() }}</a>
                            @endif

                            @if ($reports->hasMorePages())
                                <a href="{{ $reports->nextPageUrl() }}"
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

    <script>
        function searchReports() {
            const searchTerm = document.getElementById('search-input').value;
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('search', searchTerm);
            window.location.href = currentUrl.toString();
        }

        function filterByStatus(status) {
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('status', status);
            window.location.href = currentUrl.toString();
        }

        function viewReport(id) {
            // Implement view functionality
            console.log('Viewing report:', id);
        }
    </script>

</x-page-layout>
