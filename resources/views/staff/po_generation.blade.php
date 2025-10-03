<x-page-layout>
    <x-slot name="header">
        <x-app-header :homeUrl="route('staff')" :title="$pageTitle ?? __('DSWD-PRISM')" :userName="Auth::user()->first_name .
            (Auth::user()->middle_name ? ' ' . Auth::user()->middle_name : '') .
            ' ' .
            Auth::user()->last_name" :recentActivities="$recentActivities ?? collect()" />
    </x-slot>

    <!-- Main Content -->
    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Purchase Order Generation') }}
                </h2>
                <a href="{{ route('po-documents.upload') }}"
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                        </path>
                    </svg>
                    <span>Upload PO Document</span>
                </a>
            </div>
            <!-- Approved PRs Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ __('Approved Purchase Request') }}
                    </h3>
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
                @if ($approvedPRs->count() > 0)
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
                                        Date Approved
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Requesting Unit
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($approvedPRs as $pr)
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $pr->pr_number }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $pr->updated_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $pr->user->first_name }}{{ $pr->user->middle_name ? ' ' . $pr->user->middle_name : '' }}
                                            {{ $pr->user->last_name }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            ₱ {{ number_format($pr->total, 2) }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $pr->status_color }}">
                                                {{ $pr->status_display }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <div class="flex space-x-2 justify-center">
                                                <button onclick="openViewModal({{ $pr->id }})"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium">
                                                    View PR
                                                </button>
                                                @if ($pr->status === 'approved')
                                                    <button onclick="generatePO({{ $pr->id }})"
                                                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium">
                                                        Generate PO
                                                    </button>
                                                @endif
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
                            Showing {{ $approvedPRs->firstItem() ?? 0 }} to
                            {{ $approvedPRs->lastItem() ?? 0 }}
                            of {{ $approvedPRs->total() }} results
                        </div>
                        <div class="flex items-center space-x-2">
                            {{ $approvedPRs->links() }}
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
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No approved PRs found</h3>
                        <p class="mt-1 text-sm text-gray-500">No approved purchase requests are available for PO
                            generation.</p>
                    </div>
                @endif
            </div>

            <!-- Generated Purchase Orders Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ __('Generated Purchase Orders') }}
                    </h3>
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

                @if ($generatedPOs->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        PO #</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Supplier</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date Generated</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Delivery Term</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Payment Term</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($generatedPOs as $po)
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $po->po_number }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $po->supplier ? $po->supplier->supplier_name : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $po->po_generated_at ? $po->po_generated_at->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            ₱{{ number_format($po->total_cost, 2) }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $po->delivery_term ?: 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $po->payment_term ?: 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <div class="flex space-x-2 justify-center">
                                                <button onclick="openViewPOModal({{ $po->id }})"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium">
                                                    View
                                                </button>

                                                <button onclick="openEditPOModal({{ $po->id }})"
                                                    class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 px-3 py-1 rounded text-sm font-medium">
                                                    Edit
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="flex items-center justify-between px-6 py-4">
                        <div class="text-sm text-gray-700">
                            Showing {{ $generatedPOs->firstItem() ?? 0 }} to
                            {{ $generatedPOs->lastItem() ?? 0 }}
                            of {{ $generatedPOs->total() }} results
                        </div>
                        <div class="flex items-center space-x-2">
                            {{ $generatedPOs->links() }}
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
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No purchase orders generated</h3>
                        <p class="mt-1 text-sm text-gray-500">Generated purchase orders will appear here.</p>
                    </div>
                @endif
            </div>

            <!-- PO Documents Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ __('PO Documents') }}
                    </h3>
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

                @if ($poDocuments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        PO Number
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
                                @foreach ($poDocuments as $document)
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $document->po_number }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $document->file_name }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $document->file_type }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $document->file_size }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $document->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <div class="flex space-x-2 justify-center">
                                                <a href="{{ route('po-documents.download', $document) }}"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium">
                                                    Download
                                                </a>
                                                <button onclick="deletePODocument({{ $document->id }})"
                                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium">
                                                    Delete
                                                </button>
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
                            Showing {{ $poDocuments->firstItem() ?? 0 }} to
                            {{ $poDocuments->lastItem() ?? 0 }}
                            of {{ $poDocuments->total() }} results
                        </div>
                        <div class="flex items-center space-x-2">
                            {{ $poDocuments->links() }}
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
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No PO documents uploaded</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by uploading your first PO document.</p>
                        <div class="mt-4">
                            <a href="{{ route('po-documents.upload') }}"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Upload PO Document
                            </a>
                        </div>
                    </div>
                @endif
            </div>

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
                    <label class="block text-sm font-medium text-gray-700">Requesting Unit</label>
                    <p id="view-requesting-unit" class="mt-1 text-sm text-gray-900"></p>
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
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Items</label>
                    <div
                        style="max-height: 220px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 0.5rem;">
                        <table class="min-w-full divide-y divide-gray-200 text-sm" id="view-items-table">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-2 py-1 text-left font-semibold">Unit</th>
                                    <th class="px-2 py-1 text-left font-semibold">Qty</th>
                                    <th class="px-2 py-1 text-left font-semibold">Unit Cost</th>
                                    <th class="px-2 py-1 text-left font-semibold">Total Cost</th>
                                    <th class="px-2 py-1 text-left font-semibold">Description</th>
                                </tr>
                            </thead>
                            <tbody id="view-items-table-body">
                                <!-- Items will be populated by JS -->
                            </tbody>
                        </table>
                    </div>
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
                <button type="button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg"
                    onclick="closeViewModal()">
                    Close
                </button>
            </div>
        </div>
    </x-modal>

    <!-- View PO Modal -->
    <x-modal name="view-po-modal" maxWidth="2xl">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Purchase Order Details</h3>
            <div id="view-po-content">
                <!-- Content will be loaded by JS -->
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <button id="print-po-btn" type="button"
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg"
                    onclick="openPrintPOView()">
                    Print
                </button>
                <button type="button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg"
                    onclick="closeViewPOModal()">
                    Close
                </button>
            </div>
        </div>
    </x-modal>

    <!-- Edit PO Modal -->
    <x-modal name="edit-po-modal" maxWidth="2xl">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Purchase Order</h3>
            <form id="edit-po-form">
                <div id="edit-po-content">
                    <!-- Form fields will be loaded by JS -->
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="button"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg mr-2"
                        onclick="closeEditPOModal()">
                        Cancel
                    </button>
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
        window.suppliers = @json($suppliers);
    </script>

    <script>
        function openViewModal(prId) {
            console.log('Opening modal for PR ID:', prId);

            fetch(`/staff/po-generation/${prId}/data`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received data:', data);

                    document.getElementById('view-pr-number').textContent = data.pr_number;
                    document.getElementById('view-pr-date').textContent = data.date;
                    document.getElementById('view-requesting-unit').textContent = data.requesting_unit;
                    document.getElementById('view-entity-name').textContent = data.entity_name;
                    document.getElementById('view-fund-cluster').textContent = data.fund_cluster;
                    document.getElementById('view-office-section').textContent = data.office_section;
                    document.getElementById('view-delivery-address').textContent = data.delivery_address;
                    document.getElementById('view-purpose').textContent = data.purpose;
                    document.getElementById('view-requested-by').textContent = data.requested_by_name;
                    document.getElementById('view-delivery-period').textContent = data.delivery_period;

                    const statusElement = document.getElementById('view-status');
                    statusElement.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                    statusElement.className =
                        `mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full ${data.status_color}`;

                    // Populate items table
                    const itemsTableBody = document.getElementById('view-items-table-body');
                    itemsTableBody.innerHTML = '';

                    if (data.items && data.items.length > 0) {
                        data.items.forEach(item => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-2 py-1 text-xs">${item.unit}</td>
                                <td class="px-2 py-1 text-xs">${item.quantity}</td>
                                <td class="px-2 py-1 text-xs">₱${parseFloat(item.unit_cost).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                <td class="px-2 py-1 text-xs">₱${parseFloat(item.total_cost).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                <td class="px-2 py-1 text-xs" style="max-width: 200px; word-wrap: break-word;">${item.item_description}</td>
                            `;
                            itemsTableBody.appendChild(row);
                        });
                    } else {
                        const row = document.createElement('tr');
                        row.innerHTML =
                            '<td colspan="5" class="px-2 py-4 text-center text-gray-500">No items found</td>';
                        itemsTableBody.appendChild(row);
                    }

                    // Show the modal
                    window.dispatchEvent(new CustomEvent('open-modal', {
                        detail: 'view-pr-modal'
                    }));
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading purchase request details: ' + error.message);
                });
        }

        function closeViewModal() {
            window.dispatchEvent(new CustomEvent('close-modal', {
                detail: 'view-pr-modal'
            }));
        }

        function generatePO(prId) {
            if (confirm('Are you sure you want to generate a Purchase Order for this PR?')) {
                fetch(`/staff/po-generation/${prId}/generate-po`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                location.reload();
                            }
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error generating PO');
                    });
            }
        }

        function openViewPOModal(poId) {
            fetch(`/staff/po-generation/${poId}/data`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    let html = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">PR Number</label>
                <p class="mt-1 text-sm text-gray-900">${data.pr_number ?? ''}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Date</label>
                <p class="mt-1 text-sm text-gray-900">${data.po_generated_at ?? ''}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Supplier</label>
                <p class="mt-1 text-sm text-gray-900">${data.supplier_name ?? ''}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">PO Number</label>
                <p class="mt-1 text-sm text-gray-900">${data.po_number ?? ''}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Supplier Address</label>
                <p class="mt-1 text-sm text-gray-900">${data.supplier_address ?? ''}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">TIN</label>
                <p class="mt-1 text-sm text-gray-900">${data.supplier_tin ?? ''}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Mode of Procurement</label>
                <p class="mt-1 text-sm text-gray-900">${data.mode_of_procurement ?? ''}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Place of Delivery</label>
                <p class="mt-1 text-sm text-gray-900">${data.place_of_delivery ?? ''}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Delivery Term</label>
                <p class="mt-1 text-sm text-gray-900">${data.delivery_term ?? ''}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Payment Term</label>
                <p class="mt-1 text-sm text-gray-900">${data.payment_term ?? ''}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Date of Delivery</label>
                <p class="mt-1 text-sm text-gray-900">${data.date_of_delivery ?? ''}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Requesting Unit</label>
                <p class="mt-1 text-sm text-gray-900">${data.requesting_unit ?? ''}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full ${data.status_color}">
                    ${data.status ? data.status.charAt(0).toUpperCase() + data.status.slice(1) : ''}
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
                        data.items.forEach(item => {
                            html += `
                                <tr class="border-b">
                                    <td class="px-2 py-1 text-xs">${item.unit}</td>
                                    <td class="px-2 py-1 text-xs">${item.quantity}</td>
                                    <td class="px-2 py-1 text-xs">₱${parseFloat(item.unit_cost).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                    <td class="px-2 py-1 text-xs">₱${parseFloat(item.total_cost).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                    <td class="px-2 py-1 text-xs" style="max-width: 200px; word-wrap: break-word;">${item.item_description}</td>
                                </tr>
                            `;
                        });
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

                    document.getElementById('view-po-content').innerHTML = html;

                    // Set the PO ID on the print button
                    const printBtn = document.getElementById('print-po-btn');
                    if (printBtn) {
                        printBtn.setAttribute('data-po-id', poId);
                    }

                    window.dispatchEvent(new CustomEvent('open-modal', {
                        detail: 'view-po-modal'
                    }));
                })
                .catch(error => {
                    alert('Error loading PO details: ' + error.message);
                });
        }

        function openPrintPOView() {
            const btn = document.getElementById('print-po-btn');
            const poId = btn.getAttribute('data-po-id');
            if (poId) {
                window.open(`/staff/po-generation/${poId}/print`, '_blank');
            } else {
                alert('PO ID not found.');
            }
        }

        function closeViewPOModal() {
            window.dispatchEvent(new CustomEvent('close-modal', {
                detail: 'view-po-modal'
            }));
        }

        function openEditPOModal(poId) {
            fetch(`/staff/po-generation/${poId}/data`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    // Build supplier options
                    let supplierOptions = `<option value="">Select Supplier</option>`;
                    window.suppliers.forEach(s => {
                        supplierOptions +=
                            `<option value="${s.id}" data-address="${s.address ?? ''}" data-tin="${s.tin ?? ''}" ${data.supplier_id == s.id ? 'selected' : ''}>${s.supplier_name}</option>`;
                    });

                    let html = `
        <div class="bg-gray-50 p-4 rounded-lg mb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Purchase Request Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">PR Number</label>
                    <input type="text" value="${data.pr_number ?? ''}" readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Requesting Unit</label>
                    <input type="text" value="${data.requesting_unit ?? ''}" readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                </div>
            </div>
        </div>
        <div class="bg-blue-50 p-4 rounded-lg mb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Supplier Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="edit_supplier_id" class="block text-sm font-medium text-gray-700">Supplier *</label>
                    <select name="supplier_id" id="edit_supplier_id" required onchange="updateEditSupplierInfo()" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        ${supplierOptions}
                    </select>
                </div>
                <div>
                    <label for="edit_po_number" class="block text-sm font-medium text-gray-700">PO Number</label>
                    <input type="text" name="po_number" id="edit_po_number" value="${data.po_number ?? ''}" readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                </div>
                <div>
                    <label for="edit_supplier_address" class="block text-sm font-medium text-gray-700">Supplier Address</label>
                    <textarea name="supplier_address" id="edit_supplier_address" rows="2" readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100">${data.supplier_address ?? ''}</textarea>
                </div>
                <div>
                    <label for="edit_supplier_tin" class="block text-sm font-medium text-gray-700">TIN</label>
                    <input type="text" name="supplier_tin" id="edit_supplier_tin" value="${data.supplier_tin ?? ''}" readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                </div>
            </div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg mb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Procurement Details</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="edit_mode_of_procurement" class="block text-sm font-medium text-gray-700">Mode of Procurement *</label>
                    <select name="mode_of_procurement" id="edit_mode_of_procurement" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Mode</option>
                        <option value="Shopping" ${data.mode_of_procurement === 'Shopping' ? 'selected' : ''}>Shopping</option>
                        <option value="Small Value Procurement" ${data.mode_of_procurement === 'Small Value Procurement' ? 'selected' : ''}>Small Value Procurement</option>
                        <option value="Direct Contracting" ${data.mode_of_procurement === 'Direct Contracting' ? 'selected' : ''}>Direct Contracting</option>
                        <option value="Limited Source Bidding" ${data.mode_of_procurement === 'Limited Source Bidding' ? 'selected' : ''}>Limited Source Bidding</option>
                        <option value="Competitive Bidding" ${data.mode_of_procurement === 'Competitive Bidding' ? 'selected' : ''}>Competitive Bidding</option>
                    </select>
                </div>
                <div>
                    <label for="edit_place_of_delivery" class="block text-sm font-medium text-gray-700">Place of Delivery</label>
                    <input type="text" name="place_of_delivery" id="edit_place_of_delivery" value="${data.place_of_delivery ?? ''}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="edit_delivery_term" class="block text-sm font-medium text-gray-700">Delivery Term *</label>
                    <input type="text" name="delivery_term" id="edit_delivery_term" value="${data.delivery_term ?? ''}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="edit_payment_term" class="block text-sm font-medium text-gray-700">Payment Term *</label>
                    <input type="text" name="payment_term" id="edit_payment_term" value="${data.payment_term ?? ''}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="edit_date_of_delivery" class="block text-sm font-medium text-gray-700">Date of Delivery *</label>
                    <input type="date" name="date_of_delivery" id="edit_date_of_delivery" value="${data.date_of_delivery ?? ''}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Item Details (from PR)</h3>
            <div style="max-height: 250px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 0.5rem; background: white;">
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
                        data.items.forEach(item => {
                            html += `
                                <tr class="border-b">
                                    <td class="px-2 py-1 text-xs">${item.unit}</td>
                                    <td class="px-2 py-1 text-xs">${item.quantity}</td>
                                    <td class="px-2 py-1 text-xs">₱${parseFloat(item.unit_cost).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                    <td class="px-2 py-1 text-xs">₱${parseFloat(item.total_cost).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                    <td class="px-2 py-1 text-xs" style="max-width: 200px; word-wrap: break-word;">${item.item_description}</td>
                                </tr>
                            `;
                        });

                        // Calculate and display total
                        const totalCost = data.items.reduce((sum, item) => sum + parseFloat(item.total_cost), 0);
                        html += `
                            <tr class="bg-gray-100 font-semibold">
                                <td colspan="3" class="px-2 py-2 text-right text-sm">Grand Total:</td>
                                <td class="px-2 py-2 text-sm">₱${totalCost.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                <td></td>
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
        `;

                    document.getElementById('edit-po-content').innerHTML = html;
                    window.dispatchEvent(new CustomEvent('open-modal', {
                        detail: 'edit-po-modal'
                    }));

                    // Attach submit handler
                    document.getElementById('edit-po-form').onsubmit = function(e) {
                        e.preventDefault();
                        let formData = new FormData(e.target);
                        fetch(`/staff/po-generation/${poId}/edit`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content'),
                                },
                                body: formData
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    alert('PO updated successfully!');
                                    closeEditPOModal();
                                    location.reload();
                                } else {
                                    alert('Error: ' + (result.message || 'Failed to update PO.'));
                                }
                            })
                            .catch(error => {
                                alert('Error updating PO: ' + error.message);
                            });
                    };

                    // Auto-fill supplier address and TIN on change
                    window.updateEditSupplierInfo = function() {
                        const supplierSelect = document.getElementById('edit_supplier_id');
                        const selectedOption = supplierSelect.options[supplierSelect.selectedIndex];
                        document.getElementById('edit_supplier_address').value = selectedOption.getAttribute(
                            'data-address') || '';
                        document.getElementById('edit_supplier_tin').value = selectedOption.getAttribute(
                            'data-tin') || '';
                    };
                    // Initial fill
                    window.updateEditSupplierInfo();
                })
                .catch(error => {
                    alert('Error loading PO details: ' + error.message);
                });
        }

        function closeEditPOModal() {
            window.dispatchEvent(new CustomEvent('close-modal', {
                detail: 'edit-po-modal'
            }));
        }

        function deletePODocument(documentId) {
            if (confirm('Are you sure you want to delete this PO document?')) {
                console.log('Deleting PO document with ID:', documentId);

                fetch(`/po-documents/${documentId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('Delete response status:', response.status);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Delete response data:', data);
                        if (data.success) {
                            // Show success message
                            alert('PO Document deleted successfully!');
                            // Reload the page to update the table
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the document: ' + error.message);
                    });
            }
        }
    </script>

</x-page-layout>
