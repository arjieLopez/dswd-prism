<x-page-layout>
    <x-slot name="header">
        <a href="/user">
            <img src="{{ asset('images/DSWD-Logo1.png') }}" alt="DSWD Logo" class="w-16">
        </a>
        <h2 class="p-4 font-bold text-xl text-gray-800 leading-tight tracking-wide">
            {{ __('DSWD-PRISM') }}
        </h2>
        <span class="flex-1"></span>
        <div class="p-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-7 h-7 inline-block align-middle">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
        </div>

        <div class="p-2">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button
                        class="inline-flex items-center px-2 py-2 border border-transparent rounded-full text-gray-900 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                        aria-label="User menu">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-7 h-7">
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
                        <span>Create New PR</span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
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
                        <button
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z">
                                </path>
                            </svg>
                            <span>Filter</span>
                        </button>
                        <button
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <span>Export</span>
                        </button>
                    </div>
                </div>

                <!-- Purchase Request Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    PR Number
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date Created
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if ($purchaseRequests->count() > 0)
                                @foreach ($purchaseRequests as $pr)
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $pr->pr_number }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $pr->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $pr->status_color }}-100 text-{{ $pr->status_color }}-800">
                                                {{ ucfirst($pr->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ₱ {{ number_format($pr->total, 2) }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <button onclick="openViewModal({{ $pr->id }})"
                                                class="text-blue-600 hover:text-blue-900">View</button>
                                            <button onclick="openEditModal({{ $pr->id }})"
                                                class="text-blue-600 hover:text-blue-900">Edit</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            <p class="text-gray-500">No purchase requests found.</p>
                                            <a href="{{ route('purchase-requests.create') }}"
                                                class="text-blue-600 hover:text-blue-900 text-sm mt-1">Create your
                                                first PR</a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between mt-4">
                    <div class="text-sm text-gray-700">
                        Showing {{ $purchaseRequests->firstItem() ?? 0 }} to {{ $purchaseRequests->lastItem() ?? 0 }}
                        of {{ $purchaseRequests->total() }} results
                    </div>
                    <div class="flex items-center space-x-2">
                        {{ $purchaseRequests->links() }}
                    </div>
                </div>
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
                        <button
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z">
                                </path>
                            </svg>
                            <span>Filter</span>
                        </button>
                        <button
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <span>Export</span>
                        </button>
                    </div>
                </div>

                @if ($uploadedDocuments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        PR Number
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        File Name
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        File Type
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        File Size
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Upload Date
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($uploadedDocuments as $document)
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $document->pr_number }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $document->original_filename }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ strtoupper($document->file_type) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $document->file_size_formatted }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $document->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('uploaded-documents.download', $document) }}"
                                                    class="text-blue-600 hover:text-blue-900">
                                                    Download
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('uploaded-documents.destroy', $document) }}"
                                                    class="inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this document?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
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

                <div class="mt-6 flex justify-end">
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
                }, 5000);
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
                }, 5000);
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
                        const statusElement = document.getElementById('view-status');
                        statusElement.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                        statusElement.className =
                            `mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-${data.status_color}-100 text-${data.status_color}-800`;

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
