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
                    {{ __('Audit Trail') }}
                </h2>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Search, Filter, Export Controls -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2">
                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" placeholder="Search..."
                                class="pl-10 w-full md:w-64 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <button
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">Search</button>
                    </div>
                    <!-- Add filter dropdowns -->
                    <div class="flex flex-wrap gap-4 mb-4">
                        <select id="action-filter"
                            class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="all">All Actions</option>
                            @foreach ($actions as $action)
                                <option value="{{ $action }}">{{ ucfirst(str_replace('_', ' ', $action)) }}
                                </option>
                            @endforeach
                        </select>

                        <select id="role-filter"
                            class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="all">All Roles</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>

                        <input type="date" id="date-from"
                            class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="From Date">
                        <input type="date" id="date-to"
                            class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="To Date">
                    </div>
                </div>

                <!-- Audit Logs Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Timestamp
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php $counter = ($auditLogs->currentPage() - 1) * $auditLogs->perPage() + 1; @endphp
                            @forelse ($auditLogs as $log)
                                <tr>
                                    <td class="px-4 py-2 text-center">{{ $counter++ }}</td>
                                    <td class="px-4 py-2">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td class="px-4 py-2">{{ $log->user_name }}</td>
                                    <td class="px-4 py-2">
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ ucfirst($log->user_role) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex items-center">
                                            <i class="iconify w-4 h-4 mr-2 {{ $log->action_color }}"
                                                data-icon="{{ $log->action_icon }}"></i>
                                            {{ $log->description }}
                                            @if ($log->pr_number)
                                                <span class="ml-1 text-gray-500">({{ $log->pr_number }})</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        No audit logs found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Custom Pagination - Only show when there are more than 5 items -->
                @if ($auditLogs->total() > 5)
                    <div class="flex justify-center mt-6">
                        <div class="flex items-center space-x-1">
                            @if ($auditLogs->onFirstPage())
                                <span class="px-3 py-2 text-gray-400 cursor-not-allowed">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </span>
                            @else
                                <a href="{{ $auditLogs->previousPageUrl() }}"
                                    class="px-3 py-2 text-gray-600 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </a>
                            @endif

                            @php
                                $start = max(1, $auditLogs->currentPage() - 2);
                                $end = min($auditLogs->lastPage(), $auditLogs->currentPage() + 2);

                                if ($end - $start < 4) {
                                    if ($start == 1) {
                                        $end = min($auditLogs->lastPage(), $start + 4);
                                    } else {
                                        $start = max(1, $end - 4);
                                    }
                                }
                            @endphp

                            @if ($start > 1)
                                <a href="{{ $auditLogs->url(1) }}"
                                    class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">1</a>
                                @if ($start > 2)
                                    <span class="px-2 py-2 text-gray-400">...</span>
                                @endif
                            @endif

                            @for ($page = $start; $page <= $end; $page++)
                                @if ($page == $auditLogs->currentPage())
                                    <span
                                        class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md">{{ $page }}</span>
                                @else
                                    <a href="{{ $auditLogs->url($page) }}"
                                        class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">{{ $page }}</a>
                                @endif
                            @endfor

                            @if ($end < $auditLogs->lastPage())
                                @if ($end < $auditLogs->lastPage() - 1)
                                    <span class="px-2 py-2 text-gray-400">...</span>
                                @endif
                                <a href="{{ $auditLogs->url($auditLogs->lastPage()) }}"
                                    class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">{{ $auditLogs->lastPage() }}</a>
                            @endif

                            @if ($auditLogs->hasMorePages())
                                <a href="{{ $auditLogs->nextPageUrl() }}"
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
        function applyFilters() {
            const action = document.getElementById('action-filter').value;
            const role = document.getElementById('role-filter').value;
            const dateFrom = document.getElementById('date-from').value;
            const dateTo = document.getElementById('date-to').value;
            const search = document.querySelector('input[placeholder="Search..."]').value;

            const params = new URLSearchParams();
            if (action !== 'all') params.append('action', action);
            if (role !== 'all') params.append('role', role);
            if (dateFrom) params.append('date_from', dateFrom);
            if (dateTo) params.append('date_to', dateTo);
            if (search) params.append('search', search);

            window.location.href = '{{ route('admin.audit_logs') }}?' + params.toString();
        }

        // Event listeners
        document.getElementById('action-filter').addEventListener('change', applyFilters);
        document.getElementById('role-filter').addEventListener('change', applyFilters);
        document.getElementById('date-from').addEventListener('change', applyFilters);
        document.getElementById('date-to').addEventListener('change', applyFilters);

        function searchLogs() {
            applyFilters();
        }
    </script>
</x-page-layout>
