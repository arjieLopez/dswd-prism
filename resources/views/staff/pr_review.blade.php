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
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Purchase Request Review') }}
                </h2>
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

            <!-- Purchase Requests Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if ($purchaseRequests->count() > 0)
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
                                        Date Created
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Requesting Unit
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
                                @foreach ($purchaseRequests as $pr)
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $pr->pr_number }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $pr->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ $pr->user->name }}
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
                                                    View
                                                </button>
                                                @if ($pr->status === 'pending')
                                                    <button onclick="approvePR({{ $pr->id }})"
                                                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium">
                                                        Approve
                                                    </button>
                                                    <button onclick="rejectPR({{ $pr->id }})"
                                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium">
                                                        Reject
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
                    <div class="flex items-center justify-between mt-4 px-6 py-4">
                        <div class="text-sm text-gray-700">
                            Showing {{ $purchaseRequests->firstItem() ?? 0 }} to
                            {{ $purchaseRequests->lastItem() ?? 0 }}
                            of {{ $purchaseRequests->total() }} results
                        </div>
                        <div class="flex items-center space-x-2">
                            {{ $purchaseRequests->links() }}
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
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No purchase requests found</h3>
                        <p class="mt-1 text-sm text-gray-500">No purchase requests are available for review.</p>
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
                <button type="button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg"
                    onclick="closeViewModal()">
                    Close
                </button>
            </div>
        </div>
    </x-modal>

    <script>
        function openViewModal(prId) {
            console.log('Opening modal for PR ID:', prId);

            fetch(`/staff/pr-review/${prId}/data`, {
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
                    document.getElementById('view-unit').textContent = data.unit;
                    document.getElementById('view-quantity').textContent = data.quantity;
                    document.getElementById('view-unit-cost').textContent = '₱ ' + parseFloat(data.unit_cost)
                        .toLocaleString('en-US', {
                            minimumFractionDigits: 2
                        });
                    document.getElementById('view-total-cost').textContent = '₱ ' + parseFloat(data.total_cost)
                        .toLocaleString('en-US', {
                            minimumFractionDigits: 2
                        });
                    document.getElementById('view-item-description').textContent = data.item_description;
                    document.getElementById('view-delivery-address').textContent = data.delivery_address;
                    document.getElementById('view-purpose').textContent = data.purpose;
                    document.getElementById('view-requested-by').textContent = data.requested_by_name;
                    document.getElementById('view-delivery-period').textContent = data.delivery_period;

                    const statusElement = document.getElementById('view-status');
                    statusElement.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                    statusElement.className =
                        `mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full ${data.status_color}`;

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

        function approvePR(prId) {
            if (confirm('Are you sure you want to approve this purchase request?')) {
                fetch(`/staff/pr-review/${prId}/approve`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Purchase Request approved successfully!');
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error approving purchase request');
                    });
            }
        }

        function rejectPR(prId) {
            if (confirm('Are you sure you want to reject this purchase request?')) {
                fetch(`/staff/pr-review/${prId}/reject`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Purchase Request rejected successfully!');
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error rejecting purchase request');
                    });
            }
        }
    </script>

</x-page-layout>
