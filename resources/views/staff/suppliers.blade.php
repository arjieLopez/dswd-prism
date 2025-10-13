<x-page-layout>
    <x-slot name="header">
        <x-app-header :homeUrl="route('staff')" :title="$pageTitle ?? __('DSWD-PRISM')" :userName="Auth::user()->first_name .
            (Auth::user()->middle_name ? ' ' . Auth::user()->middle_name : '') .
            ' ' .
            Auth::user()->last_name" :recentActivities="$recentActivities ?? collect()" />
    </x-slot>

    <!-- Main Content -->
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Supplier Management') }}
                    </h2>
                    <button onclick="openAddModal()"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Add New Supplier</span>
                    </button>
                </div>

                @if ($suppliers->count() > 0)
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
                                        Supplier Name
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        TIN
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Address
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Contact Person
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Contact Number
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($suppliers as $index => $supplier)
                                    <tr>
                                        <td
                                            class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center font-medium">
                                            {{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $index + 1 }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $supplier->supplier_name }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $supplier->tin ?: 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-900 text-center">
                                            <div class="max-w-xs truncate" title="{{ $supplier->address }}">
                                                {{ $supplier->address }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $supplier->contact_person }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $supplier->contact_number }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $supplier->email }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $supplier->status_color }}">
                                                {{ ucfirst($supplier->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <div class="flex space-x-2 justify-center">
                                                <button onclick="openEditModal({{ $supplier->id }})"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium">
                                                    Edit
                                                </button>
                                                <button onclick="toggleSupplierStatus({{ $supplier->id }})"
                                                    class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm font-medium">
                                                    {{ $supplier->status === 'active' ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Custom Pagination - Only show when there are more than 10 items -->
                    @if ($suppliers->total() > 10)
                        <div class="flex justify-center mt-6">
                            <div class="flex items-center space-x-1">
                                @if ($suppliers->onFirstPage())
                                    <span class="px-3 py-2 text-gray-400 cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </span>
                                @else
                                    <a href="{{ $suppliers->appends(request()->query())->previousPageUrl() }}"
                                        class="px-3 py-2 text-gray-600 hover:text-blue-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </a>
                                @endif

                                @php
                                    $start = max(1, $suppliers->currentPage() - 2);
                                    $end = min($suppliers->lastPage(), $suppliers->currentPage() + 2);

                                    if ($end - $start < 4) {
                                        if ($start == 1) {
                                            $end = min($suppliers->lastPage(), $start + 4);
                                        } else {
                                            $start = max(1, $end - 4);
                                        }
                                    }
                                @endphp

                                @if ($start > 1)
                                    <a href="{{ $suppliers->appends(request()->query())->url(1) }}"
                                        class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">1</a>
                                    @if ($start > 2)
                                        <span class="px-2 py-2 text-gray-400">...</span>
                                    @endif
                                @endif

                                @for ($page = $start; $page <= $end; $page++)
                                    @if ($page == $suppliers->currentPage())
                                        <span
                                            class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md">{{ $page }}</span>
                                    @else
                                        <a href="{{ $suppliers->appends(request()->query())->url($page) }}"
                                            class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">{{ $page }}</a>
                                    @endif
                                @endfor

                                @if ($end < $suppliers->lastPage())
                                    @if ($end < $suppliers->lastPage() - 1)
                                        <span class="px-2 py-2 text-gray-400">...</span>
                                    @endif
                                    <a href="{{ $suppliers->appends(request()->query())->url($suppliers->lastPage()) }}"
                                        class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">{{ $suppliers->lastPage() }}</a>
                                @endif

                                @if ($suppliers->hasMorePages())
                                    <a href="{{ $suppliers->appends(request()->query())->nextPageUrl() }}"
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
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No suppliers found</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by adding your first supplier.</p>
                        <div class="mt-4">
                            <button onclick="openAddModal()"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Add New Supplier
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Supplier Modal -->
    <div id="add-supplier-modal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Supplier</h3>
                <form id="add-supplier-form">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="supplier_name" class="block text-sm font-medium text-gray-700">Supplier
                                Name <span class="text-red-500">*</span></label>
                            <input type="text" name="supplier_name" id="supplier_name" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="tin" class="block text-sm font-medium text-gray-700">TIN
                                (Optional)</label>
                            <input type="text" name="tin" id="tin"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Address <span
                                    class="text-red-500">*</span></label>
                            <textarea name="address" id="address" rows="3" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <div>
                            <label for="contact_person" class="block text-sm font-medium text-gray-700">Contact
                                Person <span class="text-red-500">*</span></label>
                            <input type="text" name="contact_person" id="contact_person" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact
                                Number <span class="text-red-500">*</span></label>
                            <input type="text" name="contact_number" id="contact_number" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email <span
                                    class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeAddModal()"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Cancel
                        </button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Supplier Modal -->
    <div id="edit-supplier-modal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Supplier</h3>
                <form id="edit-supplier-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_supplier_id" name="supplier_id">
                    <div class="space-y-4">
                        <div>
                            <label for="edit_supplier_name" class="block text-sm font-medium text-gray-700">Supplier
                                Name <span class="text-red-500">*</span></label>
                            <input type="text" name="supplier_name" id="edit_supplier_name" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="edit_tin" class="block text-sm font-medium text-gray-700">TIN
                                (Optional)</label>
                            <input type="text" name="tin" id="edit_tin"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="edit_address" class="block text-sm font-medium text-gray-700">Address <span
                                    class="text-red-500">*</span></label>
                            <textarea name="address" id="edit_address" rows="3" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <div>
                            <label for="edit_contact_person" class="block text-sm font-medium text-gray-700">Contact
                                Person <span class="text-red-500">*</span></label>
                            <input type="text" name="contact_person" id="edit_contact_person" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="edit_contact_number" class="block text-sm font-medium text-gray-700">Contact
                                Number <span class="text-red-500">*</span></label>
                            <input type="text" name="contact_number" id="edit_contact_number" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="edit_email" class="block text-sm font-medium text-gray-700">Email <span
                                    class="text-red-500">*</span></label>
                            <input type="email" name="email" id="edit_email" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeEditModal()"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Cancel
                        </button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Update Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('add-supplier-modal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('add-supplier-modal').classList.add('hidden');
            document.getElementById('add-supplier-form').reset();
        }

        function openEditModal(supplierId) {
            // Fetch supplier data
            fetch(`/suppliers/${supplierId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_supplier_id').value = data.id;
                    document.getElementById('edit_supplier_name').value = data.supplier_name;
                    document.getElementById('edit_tin').value = data.tin || '';
                    document.getElementById('edit_address').value = data.address;
                    document.getElementById('edit_contact_person').value = data.contact_person;
                    document.getElementById('edit_contact_number').value = data.contact_number;
                    document.getElementById('edit_email').value = data.email;
                    document.getElementById('edit-supplier-modal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorAlert('Error loading supplier data');
                });
        }

        function closeEditModal() {
            document.getElementById('edit-supplier-modal').classList.add('hidden');
        }

        function toggleSupplierStatus(supplierId) {
            // Create modern confirmation modal with animations
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
                opacity: 0;
                transition: opacity 0.3s ease-in-out;
                padding: 20px;
            `;

            const modalDiv = document.createElement('div');
            modalDiv.style.cssText = `
                background: white;
                padding: 32px;
                border-radius: 16px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                max-width: 420px;
                width: 100%;
                text-align: center;
                transform: scale(0.95);
                transition: transform 0.3s ease-in-out;
                border: 1px solid rgba(0, 0, 0, 0.05);
            `;

            modalDiv.innerHTML = `
                <div style="margin-bottom: 20px;">
                    <div style="
                        width: 64px;
                        height: 64px;
                        background: linear-gradient(135deg, #FEF3C7 0%, #FCD34D 100%);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 16px auto;
                        box-shadow: 0 10px 25px rgba(252, 211, 77, 0.3);
                    ">
                        <svg style="width: 28px; height: 28px; color: #D97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                    </div>
                    <h3 style="
                        margin: 0 0 12px 0; 
                        font-size: 20px; 
                        font-weight: 700; 
                        color: #1F2937;
                        letter-spacing: -0.025em;
                    ">Confirm Status Change</h3>
                    <p style="
                        margin: 0 0 28px 0; 
                        color: #6B7280; 
                        font-size: 15px; 
                        line-height: 1.6;
                        font-weight: 400;
                    ">Are you sure you want to change this supplier's status? This action will affect their availability in the system.</p>
                </div>
                <div style="display: flex; gap: 12px; justify-content: center;">
                    <button id="confirm-no" style="
                        padding: 12px 24px; 
                        background: #F9FAFB; 
                        color: #374151; 
                        border: 1px solid #D1D5DB;
                        border-radius: 8px; 
                        cursor: pointer;
                        font-weight: 600;
                        font-size: 14px;
                        transition: all 0.2s ease;
                        min-width: 100px;
                    " onmouseover="this.style.background='#F3F4F6'; this.style.borderColor='#9CA3AF';" 
                       onmouseout="this.style.background='#F9FAFB'; this.style.borderColor='#D1D5DB';">
                        Cancel
                    </button>
                    <button id="confirm-yes" style="
                        padding: 12px 24px; 
                        background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
                        color: white; 
                        border: none;
                        border-radius: 8px; 
                        cursor: pointer;
                        font-weight: 600;
                        font-size: 14px;
                        box-shadow: 0 4px 14px 0 rgba(239, 68, 68, 0.3);
                        transition: all 0.2s ease;
                        min-width: 100px;
                    " onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 20px 0 rgba(239, 68, 68, 0.4)';" 
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px 0 rgba(239, 68, 68, 0.3)';">
                        Confirm
                    </button>
                </div>
            `;

            confirmDiv.appendChild(modalDiv);
            document.body.appendChild(confirmDiv);

            // Animate in
            requestAnimationFrame(() => {
                confirmDiv.style.opacity = '1';
                modalDiv.style.transform = 'scale(1)';
            });

            // Handle confirmation
            document.getElementById('confirm-yes').onclick = () => {
                // Animate out
                confirmDiv.style.opacity = '0';
                modalDiv.style.transform = 'scale(0.95)';
                setTimeout(() => confirmDiv.remove(), 300);

                fetch(`/suppliers/${supplierId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showSuccessAlert(data.message);
                            // Reload page to update the table
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showErrorAlert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showErrorAlert('Error updating supplier status');
                    });
            };

            document.getElementById('confirm-no').onclick = () => {
                // Animate out
                confirmDiv.style.opacity = '0';
                modalDiv.style.transform = 'scale(0.95)';
                setTimeout(() => confirmDiv.remove(), 300);
            };

            // Close on background click with animation
            confirmDiv.onclick = (e) => {
                if (e.target === confirmDiv) {
                    confirmDiv.style.opacity = '0';
                    modalDiv.style.transform = 'scale(0.95)';
                    setTimeout(() => confirmDiv.remove(), 300);
                }
            };

            // Prevent modal from closing when clicking inside the modal
            modalDiv.onclick = (e) => {
                e.stopPropagation();
            };

            // Close with Escape key
            const escapeHandler = (e) => {
                if (e.key === 'Escape') {
                    confirmDiv.style.opacity = '0';
                    modalDiv.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        confirmDiv.remove();
                        document.removeEventListener('keydown', escapeHandler);
                    }, 300);
                }
            };
            document.addEventListener('keydown', escapeHandler);
        }

        // Add Supplier Form Submission
        document.getElementById('add-supplier-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('/suppliers', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeAddModal();
                        showSuccessAlert(data.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showErrorAlert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorAlert('Error adding supplier');
                });
        });
        // Edit Supplier Form Submission
        document.getElementById('edit-supplier-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const supplierId = document.getElementById('edit_supplier_id').value;

            // Add the _method field for PUT request
            formData.append('_method', 'POST');

            fetch(`/suppliers/${supplierId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeEditModal();
                        showSuccessAlert(data.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showErrorAlert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorAlert('Error updating supplier');
                });
        });

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
