<x-page-layout>
    <x-slot name="header">
        <x-app-header :homeUrl="route('admin')" :title="$pageTitle ?? __('DSWD-PRISM')" :userName="Auth::user()->first_name .
            (Auth::user()->middle_name ? ' ' . Auth::user()->middle_name : '') .
            ' ' .
            Auth::user()->last_name" :recentActivities="$recentActivities ?? collect()" />
    </x-slot>

    <!-- Main Content -->
    <!-- Main Content -->
    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('User Management') }}
                </h2>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Search and Action Controls -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2">
                    <form method="GET" action="{{ route('admin.user_management') }}"
                        class="flex items-center gap-2 w-full md:w-auto">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" name="search" placeholder="Search users..."
                                value="{{ request('search') }}"
                                class="pl-10 w-full md:w-64 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                            Search
                        </button>
                    </form>

                    <div class="flex items-center gap-2">
                        <!-- Filter Modal Trigger -->
                        <button class="flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100"
                            onclick="openFilterModal()">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 6h18M4 14h16M4 18h16">
                                </path>
                            </svg>
                            Filter
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Clear Filters -->
                        @if (request('search') || request('role') || request('status'))
                            <a href="{{ route('admin.user_management') }}"
                                class="px-4 py-2 text-gray-600 hover:text-gray-800 text-sm">
                                Clear Filters
                            </a>
                        @endif

                        <button
                            class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700 transition"
                            onclick="openAddUserModal()">
                            Add User
                        </button>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                @if (session('success'))
                    <div id="success-message"
                        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div id="error-message"
                        class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div id="error-list" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Active Filters Display -->
                @if (request('search') || request('role') || request('status'))
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-medium text-blue-800">Active Filters:</span>
                                @if (request('search'))
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Search: "{{ request('search') }}"
                                    </span>
                                @endif
                                @if (request('role') && request('role') !== 'all')
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Role: {{ ucfirst(request('role')) }}
                                    </span>
                                @endif
                                @if (request('status') && request('status') !== 'all')
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Status: {{ ucfirst(request('status')) }}
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('admin.user_management') }}"
                                class="text-sm text-blue-600 hover:text-blue-800">
                                Clear All
                            </a>
                        </div>
                    </div>
                @endif

                <!-- User Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    #</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email Address</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php $counter = ($users->currentPage() - 1) * $users->perPage() + 1; @endphp
                            @forelse($users as $user)
                                <tr>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                        {{ $counter++ }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $user->first_name }}{{ $user->middle_name ? ' ' . $user->middle_name : '' }}
                                        {{ $user->last_name }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ ucfirst($user->role) }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $user->isActive() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $user->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <button
                                                class="bg-blue-500 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-blue-600 transition"
                                                onclick="openViewUserModal(
                            {{ $user->id }},
                            '{{ $user->first_name }}',
                            '{{ $user->middle_name }}',
                            '{{ $user->last_name }}',
                            '{{ $user->email }}',
                            '{{ $user->role }}',
                            '{{ $user->designation }}',
                            '{{ $user->employee_id }}',
                            '{{ $user->office }}',
                            '{{ $user->status }}',
                            '{{ $user->created_at }}'
                        )">
                                                View
                                            </button>
                                            <button
                                                class="bg-gray-500 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-gray-600 transition"
                                                onclick="openEditUserModal(
                            {{ $user->id }},
                            '{{ $user->first_name }}',
                            '{{ $user->middle_name }}',
                            '{{ $user->last_name }}',
                            '{{ $user->email }}',
                            '{{ $user->role }}',
                            '{{ $user->designation }}',
                            '{{ $user->employee_id }}',
                            '{{ $user->office }}'
                        )">
                                                Edit
                                            </button>
                                            @if ($user->id !== auth()->id())
                                                {{-- <form method="POST"
                            action="{{ route('admin.user_management.destroy', $user) }}"
                            class="inline"
                            onsubmit="return confirm('Are you sure you want to delete this user?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-red-500 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-red-600 transition">Delete</button>
                        </form> --}}
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Custom Pagination - Only show when there are more than 5 items -->
                @if ($users->total() > 5)
                    <div class="flex justify-center mt-6">
                        <div class="flex items-center space-x-1">
                            @if ($users->onFirstPage())
                                <span class="px-3 py-2 text-gray-400 cursor-not-allowed">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </span>
                            @else
                                <a href="{{ $users->previousPageUrl() }}"
                                    class="px-3 py-2 text-gray-600 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </a>
                            @endif

                            @php
                                $start = max(1, $users->currentPage() - 2);
                                $end = min($users->lastPage(), $users->currentPage() + 2);

                                if ($end - $start < 4) {
                                    if ($start == 1) {
                                        $end = min($users->lastPage(), $start + 4);
                                    } else {
                                        $start = max(1, $end - 4);
                                    }
                                }
                            @endphp

                            @if ($start > 1)
                                <a href="{{ $users->url(1) }}"
                                    class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">1</a>
                                @if ($start > 2)
                                    <span class="px-2 py-2 text-gray-400">...</span>
                                @endif
                            @endif

                            @for ($page = $start; $page <= $end; $page++)
                                @if ($page == $users->currentPage())
                                    <span
                                        class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md">{{ $page }}</span>
                                @else
                                    <a href="{{ $users->url($page) }}"
                                        class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">{{ $page }}</a>
                                @endif
                            @endfor

                            @if ($end < $users->lastPage())
                                @if ($end < $users->lastPage() - 1)
                                    <span class="px-2 py-2 text-gray-400">...</span>
                                @endif
                                <a href="{{ $users->url($users->lastPage()) }}"
                                    class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">{{ $users->lastPage() }}</a>
                            @endif

                            @if ($users->hasMorePages())
                                <a href="{{ $users->nextPageUrl() }}"
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

    <!-- Add User Modal -->
    <div id="addUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Add New User</h3>
                    <button onclick="closeAddUserModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.user_management.store') }}">
                    @csrf
                    <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="first_name">
                                First Name
                            </label>
                            <input type="text" name="first_name" id="first_name" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="middle_name">
                                Middle Name
                            </label>
                            <input type="text" name="middle_name" id="middle_name"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="last_name">
                                Last Name
                            </label>
                            <input type="text" name="last_name" id="last_name" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                            Email
                        </label>
                        <input type="email" name="email" id="email" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="role">
                                Role
                            </label>
                            <select name="role" id="role" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="staff">Staff</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="designation">
                                Designation
                            </label>
                            <input type="text" name="designation" id="designation"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                    </div>

                    <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="employee_id">
                                Employee ID
                            </label>
                            <input type="text" name="employee_id" id="employee_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="office">
                                Office
                            </label>
                            <input type="text" name="office" id="office"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                            Password
                        </label>
                        <input type="password" name="password" id="password" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password_confirmation">
                            Confirm Password
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" onclick="closeAddUserModal()"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Cancel
                        </button>
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Add User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- View User Modal -->
    <div id="viewUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">User Details</h3>
                    <button onclick="closeViewUserModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                        <p id="view_name" class="text-gray-900 bg-gray-100 p-2 rounded"></p>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                        <p id="view_email" class="text-gray-900 bg-gray-100 p-2 rounded"></p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Role</label>
                            <p id="view_role" class="text-gray-900 bg-gray-100 p-2 rounded"></p>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Designation</label>
                            <p id="view_designation" class="text-gray-900 bg-gray-100 p-2 rounded"></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Employee ID</label>
                            <p id="view_employee_id" class="text-gray-900 bg-gray-100 p-2 rounded"></p>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Office</label>
                            <p id="view_office" class="text-gray-900 bg-gray-100 p-2 rounded"></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                            <p id="view_status" class="text-gray-900 bg-gray-100 p-2 rounded"></p>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Date Created</label>
                            <p id="view_created_at" class="text-gray-900 bg-gray-100 p-2 rounded"></p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6 space-x-3">
                    <form id="viewUserStatusForm" method="POST" style="display:inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" id="viewUserStatusBtn"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded font-semibold transition">
                            Deactivate
                        </button>
                    </form>
                    <button onclick="closeViewUserModal()"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Edit User</h3>
                    <button onclick="closeEditUserModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="editUserForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_first_name">
                                First Name
                            </label>
                            <input type="text" name="first_name" id="edit_first_name" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_middle_name">
                                Middle Name
                            </label>
                            <input type="text" name="middle_name" id="edit_middle_name"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_last_name">
                                Last Name
                            </label>
                            <input type="text" name="last_name" id="edit_last_name" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_email">
                            Email
                        </label>
                        <input type="email" name="email" id="edit_email" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_role">
                                Role
                            </label>
                            <select name="role" id="edit_role" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="admin">Admin</option>
                                <option value="staff">Staff</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_designation">
                                Designation
                            </label>
                            <input type="text" name="designation" id="edit_designation"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                    </div>

                    <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_employee_id">
                                Employee ID
                            </label>
                            <input type="text" name="employee_id" id="edit_employee_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_office">
                                Office
                            </label>
                            <input type="text" name="office" id="edit_office"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" onclick="closeEditUserModal()"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Cancel
                        </button>
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div id="filterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Filter Users</h3>
                    <button onclick="closeFilterModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form method="GET" action="{{ route('admin.user_management') }}">
                    <!-- Preserve search parameter -->
                    @if (request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="role">
                            Role
                        </label>
                        <select name="role" id="role"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="all" {{ request('role') == 'all' ? 'selected' : '' }}>All Roles</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                            Status
                        </label>
                        <select name="status" id="status"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status
                            </option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active
                            </option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="sort_by">
                            Sort By
                        </label>
                        <select name="sort_by" id="sort_by"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date
                                Created</option>
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>Email
                            </option>
                            <option value="role" {{ request('sort_by') == 'role' ? 'selected' : '' }}>Role</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="sort_order">
                            Sort Order
                        </label>
                        <select name="sort_order" id="sort_order"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending
                            </option>
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending
                            </option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" onclick="closeFilterModal()"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Cancel
                        </button>
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        // Add User Modal Functions
        function openAddUserModal() {
            document.getElementById('addUserModal').classList.remove('hidden');
        }

        function closeAddUserModal() {
            document.getElementById('addUserModal').classList.add('hidden');
            // Reset form
            document.getElementById('addUserModal').querySelector('form').reset();
        }

        // View User Modal Functions
        function openViewUserModal(userId, firstName, middleName, lastName, email, role, designation, employeeId, office,
            status, created_at) {
            document.getElementById('viewUserModal').classList.remove('hidden');
            document.getElementById('view_name').textContent = firstName + (middleName ? ' ' + middleName : '') + ' ' +
                lastName;
            document.getElementById('view_email').textContent = email;
            document.getElementById('view_role').textContent = role.charAt(0).toUpperCase() + role.slice(1);
            document.getElementById('view_designation').textContent = designation;
            document.getElementById('view_employee_id').textContent = employeeId;
            document.getElementById('view_office').textContent = office;
            document.getElementById('view_status').textContent = status;
            document.getElementById('view_created_at').textContent = created_at;

            const statusForm = document.getElementById('viewUserStatusForm');
            const statusBtn = document.getElementById('viewUserStatusBtn');
            if (parseInt(userId) === parseInt({{ auth()->id() }})) {
                statusForm.style.display = 'none';
            } else {
                statusForm.style.display = 'inline';
                statusForm.action = `/admin/user-management/${userId}/toggle-status`;
                if (status.toLowerCase() === 'active') {
                    statusBtn.textContent = 'Deactivate';
                    statusBtn.className =
                        'bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded font-semibold transition';
                } else {
                    statusBtn.textContent = 'Activate';
                    statusBtn.className =
                        'bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded font-semibold transition';
                }
            }
        }

        // Edit User Modal Functions
        function openEditUserModal(userId, firstName, middleName, lastName, email, role, designation, employeeId, office) {
            document.getElementById('editUserModal').classList.remove('hidden');
            document.getElementById('editUserForm').action = `/admin/user-management/${userId}`;
            document.getElementById('edit_first_name').value = firstName;
            document.getElementById('edit_middle_name').value = middleName;
            document.getElementById('edit_last_name').value = lastName;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_role').value = role;
            document.getElementById('edit_designation').value = designation;
            document.getElementById('edit_employee_id').value = employeeId;
            document.getElementById('edit_office').value = office;
        }

        function closeEditUserModal() {
            document.getElementById('editUserModal').classList.add('hidden');
        }

        function closeViewUserModal() {
            document.getElementById('viewUserModal').classList.add('hidden');
        }

        // Filter Modal Functions
        function openFilterModal() {
            document.getElementById('filterModal').classList.remove('hidden');
        }

        function closeFilterModal() {
            document.getElementById('filterModal').classList.add('hidden');
        }

        // Update your existing window.onclick function
        window.onclick = function(event) {
            const addModal = document.getElementById('addUserModal');
            const editModal = document.getElementById('editUserModal');
            const viewModal = document.getElementById('viewUserModal');
            const filterModal = document.getElementById('filterModal');

            if (event.target === addModal) {
                closeAddUserModal();
            }
            if (event.target === editModal) {
                closeEditUserModal();
            }
            if (event.target === viewModal) {
                closeViewUserModal();
            }
            if (event.target === filterModal) {
                closeFilterModal();
            }
        }

        // Auto-hide success/error messages after 4 seconds
        setTimeout(function() {
            const successMsg = document.getElementById('success-message');
            if (successMsg) successMsg.style.display = 'none';

            const errorMsg = document.getElementById('error-message');
            if (errorMsg) errorMsg.style.display = 'none';

            const errorList = document.getElementById('error-list');
            if (errorList) errorList.style.display = 'none';
        }, 4000);

        // // Close modals when clicking outside
        // window.onclick = function(event) {
        //     const addModal = document.getElementById('addUserModal');
        //     const editModal = document.getElementById('editUserModal');
        //     const viewModal = document.getElementById('viewUserModal');

        //     if (event.target === addModal) {
        //         closeAddUserModal();
        //     }
        //     if (event.target === editModal) {
        //         closeEditUserModal();
        //     }
        //     if (event.target === viewModal) {
        //         closeViewUserModal();
        //     }
        // }
    </script>

</x-page-layout>
