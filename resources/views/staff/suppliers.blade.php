<x-page-layout>
    <x-slot name="header">
        <a href="/staff">
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
                                @foreach ($suppliers as $supplier)
                                    <tr>
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

                    <!-- Pagination -->
                    <div class="flex items-center justify-between mt-4">
                        <div class="text-sm text-gray-700">
                            Showing {{ $suppliers->firstItem() ?? 0 }} to
                            {{ $suppliers->lastItem() ?? 0 }}
                            of {{ $suppliers->total() }} results
                        </div>
                        <div class="flex items-center space-x-2">
                            {{ $suppliers->links() }}
                        </div>
                    </div>
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
                                Name</label>
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
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea name="address" id="address" rows="3" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <div>
                            <label for="contact_person" class="block text-sm font-medium text-gray-700">Contact
                                Person</label>
                            <input type="text" name="contact_person" id="contact_person" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact
                                Number</label>
                            <input type="text" name="contact_number" id="contact_number" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
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
                                Name</label>
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
                            <label for="edit_address" class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea name="address" id="edit_address" rows="3" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <div>
                            <label for="edit_contact_person" class="block text-sm font-medium text-gray-700">Contact
                                Person</label>
                            <input type="text" name="contact_person" id="edit_contact_person" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="edit_contact_number" class="block text-sm font-medium text-gray-700">Contact
                                Number</label>
                            <input type="text" name="contact_number" id="edit_contact_number" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="edit_email" class="block text-sm font-medium text-gray-700">Email</label>
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
                    alert('Error loading supplier data');
                });
        }

        function closeEditModal() {
            document.getElementById('edit-supplier-modal').classList.add('hidden');
        }

        function toggleSupplierStatus(supplierId) {
            if (confirm('Are you sure you want to change the supplier status?')) {
                fetch(`/suppliers/${supplierId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error updating supplier status');
                    });
            }
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
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error adding supplier');
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
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating supplier');
                });
        });
    </script>
</x-page-layout>
