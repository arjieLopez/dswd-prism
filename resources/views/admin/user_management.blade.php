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
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('User Management') }}
                </h2>
                <div>
                    <button
                        class="relative bg-gradient-to-r from-green-500 to-green-600 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2
                               hover:from-green-600 hover:to-green-700 hover:shadow-lg hover:scale-105
                               active:from-green-700 active:to-green-800 active:scale-95 active:shadow-inner
                               transition-all duration-200 ease-in-out transform
                               before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                               hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200"
                        onclick="openAddUserModal()">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        <span>Add User</span>
                    </button>
                </div>
            </div>

            <div class="bg-white overflow-visible shadow-sm sm:rounded-lg p-6">
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
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full md:w-64 placeholder-gray-400">
                        </div>
                        <!-- Preserve other query parameters -->
                        @if (request('role'))
                            <input type="hidden" name="role" value="{{ request('role') }}">
                        @endif
                        @if (request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        @if (request('sort_by'))
                            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                        @endif
                        @if (request('sort_order'))
                            <input type="hidden" name="sort_order" value="{{ request('sort_order') }}">
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
                        <div class="relative" x-data="{ open: false, activeTab: 'role' }">
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
                                    <button @click="activeTab = 'role'"
                                        :class="activeTab === 'role' ? 'bg-blue-50 text-blue-700 border-blue-500' :
                                            'text-gray-500 hover:text-gray-700'"
                                        class="flex-1 px-4 py-3 text-sm font-medium border-b-2 border-transparent">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                            </path>
                                        </svg>
                                        Role
                                    </button>
                                    <button @click="activeTab = 'status'"
                                        :class="activeTab === 'status' ? 'bg-blue-50 text-blue-700 border-blue-500' :
                                            'text-gray-500 hover:text-gray-700'"
                                        class="flex-1 px-4 py-3 text-sm font-medium border-b-2 border-transparent">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                        Status
                                    </button>
                                    <button @click="activeTab = 'sort'"
                                        :class="activeTab === 'sort' ? 'bg-blue-50 text-blue-700 border-blue-500' :
                                            'text-gray-500 hover:text-gray-700'"
                                        class="flex-1 px-4 py-3 text-sm font-medium border-b-2 border-transparent">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12">
                                            </path>
                                        </svg>
                                        Sort
                                    </button>
                                </div>

                                <!-- Role Filter Content -->
                                <div x-show="activeTab === 'role'" class="p-2">
                                    <ul>
                                        <li>
                                            <a href="{{ route('admin.user_management', array_filter(['search' => request('search'), 'status' => request('status'), 'sort_by' => request('sort_by'), 'sort_order' => request('sort_order')])) }}"
                                                class="block px-4 py-2 text-gray-700 hover:bg-blue-100 rounded {{ !request('role') || request('role') == 'all' ? 'font-bold text-blue-600 bg-blue-50' : '' }}">
                                                All Roles
                                            </a>
                                        </li>
                                        @php
                                            $roles = ['admin', 'staff', 'user'];
                                            $roleDisplayMap = [
                                                'admin' => 'Admin',
                                                'staff' => 'Staff',
                                                'user' => 'User',
                                            ];
                                        @endphp
                                        @foreach ($roles as $role)
                                            <li>
                                                <a href="{{ route('admin.user_management', array_filter(['role' => $role, 'search' => request('search'), 'status' => request('status'), 'sort_by' => request('sort_by'), 'sort_order' => request('sort_order')])) }}"
                                                    class="block px-4 py-2 text-gray-700 hover:bg-blue-100 rounded {{ request('role') == $role ? 'font-bold text-blue-600 bg-blue-50' : '' }}">
                                                    {{ $roleDisplayMap[$role] ?? ucfirst($role) }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <!-- Status Filter Content -->
                                <div x-show="activeTab === 'status'" class="p-2">
                                    <ul>
                                        <li>
                                            <a href="{{ route('admin.user_management', array_filter(['search' => request('search'), 'role' => request('role'), 'sort_by' => request('sort_by'), 'sort_order' => request('sort_order')])) }}"
                                                class="block px-4 py-2 text-gray-700 hover:bg-blue-100 rounded {{ !request('status') || request('status') == 'all' ? 'font-bold text-blue-600 bg-blue-50' : '' }}">
                                                All Statuses
                                            </a>
                                        </li>
                                        @php
                                            $statuses = ['active', 'inactive'];
                                            $statusDisplayMap = [
                                                'active' => 'Active',
                                                'inactive' => 'Inactive',
                                            ];
                                        @endphp
                                        @foreach ($statuses as $status)
                                            <li>
                                                <a href="{{ route('admin.user_management', array_filter(['status' => $status, 'search' => request('search'), 'role' => request('role'), 'sort_by' => request('sort_by'), 'sort_order' => request('sort_order')])) }}"
                                                    class="block px-4 py-2 text-gray-700 hover:bg-blue-100 rounded {{ request('status') == $status ? 'font-bold text-blue-600 bg-blue-50' : '' }}">
                                                    {{ $statusDisplayMap[$status] ?? ucfirst($status) }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <!-- Sort Filter Content -->
                                <div x-show="activeTab === 'sort'" class="p-4">
                                    <form method="GET" action="{{ route('admin.user_management') }}"
                                        class="space-y-4">
                                        <!-- Preserve existing parameters -->
                                        @if (request('search'))
                                            <input type="hidden" name="search" value="{{ request('search') }}">
                                        @endif
                                        @if (request('role'))
                                            <input type="hidden" name="role" value="{{ request('role') }}">
                                        @endif
                                        @if (request('status'))
                                            <input type="hidden" name="status" value="{{ request('status') }}">
                                        @endif

                                        <div class="space-y-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Sort
                                                    By</label>
                                                <select name="sort_by"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                                    <option value="created_at"
                                                        {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date
                                                        Created</option>
                                                    <option value="first_name"
                                                        {{ request('sort_by') == 'first_name' ? 'selected' : '' }}>Name
                                                    </option>
                                                    <option value="email"
                                                        {{ request('sort_by') == 'email' ? 'selected' : '' }}>Email
                                                    </option>
                                                    <option value="role"
                                                        {{ request('sort_by') == 'role' ? 'selected' : '' }}>Role
                                                    </option>
                                                    <option value="status"
                                                        {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status
                                                    </option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Sort
                                                    Order</label>
                                                <select name="sort_order"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                                    <option value="desc"
                                                        {{ request('sort_order') == 'desc' ? 'selected' : '' }}>
                                                        Descending</option>
                                                    <option value="asc"
                                                        {{ request('sort_order') == 'asc' ? 'selected' : '' }}>
                                                        Ascending</option>
                                                </select>
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
                                                Apply Sort
                                            </button>
                                            @if (request('sort_by') || request('sort_order'))
                                                <a href="{{ route('admin.user_management', array_filter(['search' => request('search'), 'role' => request('role'), 'status' => request('status')])) }}"
                                                    class="relative flex-1 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-bold py-2 px-4 rounded-lg text-sm text-center
                                                           hover:from-gray-600 hover:to-gray-700 hover:shadow-lg hover:scale-105
                                                           active:from-gray-700 active:to-gray-800 active:scale-95 active:shadow-inner
                                                           transition-all duration-200 ease-in-out transform
                                                           before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                                                           hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200 inline-block">
                                                    <svg class="w-3 h-3 inline mr-1" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                    Clear Sort
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

                <!-- Active Filters Display -->
                @if (request('search') || request('role') || request('status') || request('sort_by'))
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
                                @if (request('sort_by'))
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Sort: {{ ucfirst(str_replace('_', ' ', request('sort_by'))) }}
                                        @if (request('sort_order'))
                                            ({{ ucfirst(request('sort_order')) }})
                                        @endif
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
                @if ($users->count() > 0)
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
                                @foreach ($users as $user)
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $counter++ }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $user->first_name }}{{ $user->middle_name ? ' ' . $user->middle_name : '' }}
                                            {{ $user->last_name }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $user->email }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ ucfirst($user->role) }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $user->isActive() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ $user->status }}</span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <button
                                                    class="relative bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-lg text-xs font-semibold hover:from-blue-600 hover:to-blue-700 hover:shadow-lg hover:scale-105 active:from-blue-700 active:to-blue-800 active:scale-95 active:shadow-inner transition-all duration-200 ease-in-out transform view-user-btn before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200"
                                                    data-user-id="{{ $user->id }}"
                                                    data-first-name="{{ $user->first_name }}"
                                                    data-middle-name="{{ $user->middle_name }}"
                                                    data-last-name="{{ $user->last_name }}"
                                                    data-email="{{ $user->email }}" data-role="{{ $user->role }}"
                                                    data-designation="{{ $user->designation }}"
                                                    data-employee-id="{{ $user->employee_id }}"
                                                    data-office="{{ $user->office }}"
                                                    data-status="{{ $user->status }}"
                                                    data-created-at="{{ $user->created_at }}">
                                                    <svg class="w-3 h-3 inline mr-1" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                    View
                                                </button>
                                                <button
                                                    class="relative bg-gradient-to-r from-gray-500 to-gray-600 text-white px-4 py-2 rounded-lg text-xs font-semibold hover:from-gray-600 hover:to-gray-700 hover:shadow-lg hover:scale-105 active:from-gray-700 active:to-gray-800 active:scale-95 active:shadow-inner transition-all duration-200 ease-in-out transform edit-user-btn before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200"
                                                    data-user-id="{{ $user->id }}"
                                                    data-first-name="{{ $user->first_name }}"
                                                    data-middle-name="{{ $user->middle_name }}"
                                                    data-last-name="{{ $user->last_name }}"
                                                    data-email="{{ $user->email }}" data-role="{{ $user->role }}"
                                                    data-designation="{{ $user->designation }}"
                                                    data-employee-id="{{ $user->employee_id }}"
                                                    data-office="{{ $user->office }}">
                                                    <svg class="w-3 h-3 inline mr-1" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                    Edit
                                                </button>
                                                @if ($user->id !== auth()->id())
                                                    {{-- Delete button commented out for safety --}}
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        @if (request('search') || request('role') || request('status') || request('sort_by'))
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
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                            <p class="mt-1 text-sm text-gray-500">No users are available for the selected criteria.</p>
                        @endif
                    </div>

                    <!-- Custom Pagination - Only show when there are more than 10 items -->
                    @if ($users->total() > 10)
                        <div class="flex justify-center mt-6">
                            <div class="flex items-center space-x-1">
                                @if ($users->onFirstPage())
                                    <span class="px-3 py-2 text-gray-400 cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </span>
                                @else
                                    <a href="{{ $users->appends(request()->query())->previousPageUrl() }}"
                                        class="px-3 py-2 text-gray-600 hover:text-blue-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
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
                                    <a href="{{ $users->appends(request()->query())->url(1) }}"
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
                                        <a href="{{ $users->appends(request()->query())->url($page) }}"
                                            class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">{{ $page }}</a>
                                    @endif
                                @endfor

                                @if ($end < $users->lastPage())
                                    @if ($end < $users->lastPage() - 1)
                                        <span class="px-2 py-2 text-gray-400">...</span>
                                    @endif
                                    <a href="{{ $users->appends(request()->query())->url($users->lastPage()) }}"
                                        class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">{{ $users->lastPage() }}</a>
                                @endif

                                @if ($users->hasMorePages())
                                    <a href="{{ $users->appends(request()->query())->nextPageUrl() }}"
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
                                First Name <span class="text-red-500">*</span>
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
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="last_name" id="last_name" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="role">
                                Role <span class="text-red-500">*</span>
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
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" id="password" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password_confirmation">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" onclick="closeAddUserModal()"
                            class="relative bg-gradient-to-r from-gray-500 to-gray-600 text-white font-bold py-2 px-4 rounded-lg
                                   hover:from-gray-600 hover:to-gray-700 hover:shadow-lg hover:scale-105
                                   active:from-gray-700 active:to-gray-800 active:scale-95 active:shadow-inner
                                   transition-all duration-200 ease-in-out transform focus:outline-none
                                   before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                                   hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </button>
                        <button type="submit"
                            class="relative bg-gradient-to-r from-blue-500 to-blue-600 text-white font-bold py-2 px-4 rounded-lg
                                   hover:from-blue-600 hover:to-blue-700 hover:shadow-lg hover:scale-105
                                   active:from-blue-700 active:to-blue-800 active:scale-95 active:shadow-inner
                                   transition-all duration-200 ease-in-out transform focus:outline-none
                                   before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                                   hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
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
                        <button type="button" id="viewUserStatusBtn" onclick="confirmStatusToggle()"
                            class="relative bg-gradient-to-r from-yellow-500 to-yellow-600 text-white px-4 py-2 rounded-lg font-semibold
                                   hover:from-yellow-600 hover:to-yellow-700 hover:shadow-lg hover:scale-105
                                   active:from-yellow-700 active:to-yellow-800 active:scale-95 active:shadow-inner
                                   transition-all duration-200 ease-in-out transform
                                   before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                                   hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18 12M6 6l12 12">
                                </path>
                            </svg>
                            Deactivate
                        </button>
                    </form>
                    <button onclick="closeViewUserModal()"
                        class="relative bg-gradient-to-r from-gray-500 to-gray-600 text-white font-bold py-2 px-4 rounded-lg
                               hover:from-gray-600 hover:to-gray-700 hover:shadow-lg hover:scale-105
                               active:from-gray-700 active:to-gray-800 active:scale-95 active:shadow-inner
                               transition-all duration-200 ease-in-out transform focus:outline-none
                               before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                               hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
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
                                First Name <span class="text-red-500">*</span>
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
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="last_name" id="edit_last_name" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_email">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="edit_email" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_role">
                                Role <span class="text-red-500">*</span>
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
                            class="relative bg-gradient-to-r from-gray-500 to-gray-600 text-white font-bold py-2 px-4 rounded-lg
                                   hover:from-gray-600 hover:to-gray-700 hover:shadow-lg hover:scale-105
                                   active:from-gray-700 active:to-gray-800 active:scale-95 active:shadow-inner
                                   transition-all duration-200 ease-in-out transform focus:outline-none
                                   before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                                   hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </button>
                        <button type="submit"
                            class="relative bg-gradient-to-r from-blue-500 to-blue-600 text-white font-bold py-2 px-4 rounded-lg
                                   hover:from-blue-600 hover:to-blue-700 hover:shadow-lg hover:scale-105
                                   active:from-blue-700 active:to-blue-800 active:scale-95 active:shadow-inner
                                   transition-all duration-200 ease-in-out transform focus:outline-none
                                   before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                                   hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            Update User
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

        // Event listeners for View and Edit buttons
        document.addEventListener('DOMContentLoaded', function() {
            // View button event listeners
            document.querySelectorAll('.view-user-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const firstName = this.dataset.firstName;
                    const middleName = this.dataset.middleName;
                    const lastName = this.dataset.lastName;
                    const email = this.dataset.email;
                    const role = this.dataset.role;
                    const designation = this.dataset.designation;
                    const employeeId = this.dataset.employeeId;
                    const office = this.dataset.office;
                    const status = this.dataset.status;
                    const createdAt = this.dataset.createdAt;

                    openViewUserModal(userId, firstName, middleName, lastName, email, role,
                        designation, employeeId, office, status, createdAt);
                });
            });

            // Edit button event listeners
            document.querySelectorAll('.edit-user-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const firstName = this.dataset.firstName;
                    const middleName = this.dataset.middleName;
                    const lastName = this.dataset.lastName;
                    const email = this.dataset.email;
                    const role = this.dataset.role;
                    const designation = this.dataset.designation;
                    const employeeId = this.dataset.employeeId;
                    const office = this.dataset.office;

                    openEditUserModal(userId, firstName, middleName, lastName, email, role,
                        designation, employeeId, office);
                });
            });
        });

        // View User Modal Functions
        function openViewUserModal(userId, firstName, middleName, lastName, email, role, designation, employeeId, office,
            status, created_at) {
            document.getElementById('viewUserModal').classList.remove('hidden');
            document.getElementById('view_name').textContent = firstName + (middleName ? ' ' + middleName : '') + ' ' +
                lastName;
            document.getElementById('view_email').textContent = email;
            document.getElementById('view_role').textContent = role.charAt(0).toUpperCase() + role.slice(1);
            document.getElementById('view_designation').textContent = designation || '-';
            document.getElementById('view_employee_id').textContent = employeeId || '-';
            document.getElementById('view_office').textContent = office || '-';
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
                    statusBtn.innerHTML = `
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18 12M6 6l12 12"></path>
                        </svg>
                        Deactivate
                    `;
                    statusBtn.className = `
                        relative bg-gradient-to-r from-yellow-500 to-yellow-600 text-white px-4 py-2 rounded-lg font-semibold
                        hover:from-yellow-600 hover:to-yellow-700 hover:shadow-lg hover:scale-105
                        active:from-yellow-700 active:to-yellow-800 active:scale-95 active:shadow-inner
                        transition-all duration-200 ease-in-out transform
                        before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                        hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200
                    `.replace(/\s+/g, ' ').trim();
                } else {
                    statusBtn.innerHTML = `
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Activate
                    `;
                    statusBtn.className = `
                        relative bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-2 rounded-lg font-semibold
                        hover:from-green-600 hover:to-green-700 hover:shadow-lg hover:scale-105
                        active:from-green-700 active:to-green-800 active:scale-95 active:shadow-inner
                        transition-all duration-200 ease-in-out transform
                        before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                        hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200
                    `.replace(/\s+/g, ' ').trim();
                }
            }
        }

        // Edit User Modal Functions
        function openEditUserModal(userId, firstName, middleName, lastName, email, role, designation, employeeId, office) {
            document.getElementById('editUserModal').classList.remove('hidden');
            document.getElementById('editUserForm').action = `/admin/user-management/${userId}`;
            document.getElementById('edit_first_name').value = firstName || '';
            document.getElementById('edit_middle_name').value = middleName || '';
            document.getElementById('edit_last_name').value = lastName || '';
            document.getElementById('edit_email').value = email || '';
            document.getElementById('edit_role').value = role || '';
            document.getElementById('edit_designation').value = designation || '';
            document.getElementById('edit_employee_id').value = employeeId || '';
            document.getElementById('edit_office').value = office || '';
        }

        function closeEditUserModal() {
            document.getElementById('editUserModal').classList.add('hidden');
        }

        function closeViewUserModal() {
            document.getElementById('viewUserModal').classList.add('hidden');
        }

        // Update your existing window.onclick function
        window.onclick = function(event) {
            const addModal = document.getElementById('addUserModal');
            const editModal = document.getElementById('editUserModal');
            const viewModal = document.getElementById('viewUserModal');

            if (event.target === addModal) {
                closeAddUserModal();
            }
            if (event.target === editModal) {
                closeEditUserModal();
            }
            if (event.target === viewModal) {
                closeViewUserModal();
            }
        }

        // JavaScript alert functions - consistent with all other pages
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

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 3000);
        }

        // Check for Laravel session messages and show as JavaScript alerts
        @if (session('success'))
            showSuccessAlert('{{ session('success') }}');
        @endif

        @if (session('error'))
            showErrorAlert('{{ session('error') }}');
        @endif

        @if ($errors->any())
            showErrorAlert('{{ $errors->first() }}');
        @endif

        // Confirmation dialog for status toggle
        function confirmStatusToggle() {
            const statusBtn = document.getElementById('viewUserStatusBtn');
            const action = statusBtn.textContent.trim();
            const form = document.getElementById('viewUserStatusForm');

            let message = '';
            if (action === 'Deactivate') {
                message =
                    'Are you sure you want to deactivate this user? They will be immediately logged out of all sessions.';
            } else {
                message = 'Are you sure you want to activate this user?';
            }

            showModernConfirmation(message, function() {
                form.submit();
            });
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
