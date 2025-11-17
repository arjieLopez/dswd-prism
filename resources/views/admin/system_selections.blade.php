<x-page-layout>
    <x-slot name="header">
        <x-app-header :homeUrl="route('admin')" :title="$pageTitle ?? __('DSWD-PRISM')" :userName="Auth::user()->first_name .
            (Auth::user()->middle_name ? ' ' . Auth::user()->middle_name : '') .
            ' ' .
            Auth::user()->last_name" :recentActivities="$recentActivities ?? collect()" />
    </x-slot>

    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('System Selections Management') }}
                </h2>
            </div>
            <div class="bg-white overflow-visible shadow-sm sm:rounded-lg p-6">
                @php
                    $icons = [
                        'mdi:ruler',
                        'mdi:domain',
                        'mdi:cash-multiple',
                        'mdi:code-tags',
                        'mdi:calendar-clock',
                        'mdi:map-marker',
                        'mdi:handshake',
                        'mdi:truck-fast',
                        'mdi:credit-card-outline',
                        'mdi:account-tie',
                        'mdi:office-building',
                    ];
                    $titles = [
                        'Metric Units',
                        'Entity',
                        'Fund Cluster',
                        'Responsibility Code',
                        'Delivery Period',
                        'Delivery Address',
                        'Mode of Procurement',
                        'Delivery Term',
                        'Payment Term',
                        'Designation',
                        'Office',
                    ];
                    $descs = [
                        'Edit units used in PR/PO items.',
                        'Edit available entities for requests.',
                        'Edit fund clusters for PR/PO.',
                        'Edit responsibility codes.',
                        'Edit delivery period options.',
                        'Edit delivery addresses.',
                        'Edit procurement modes.',
                        'Edit delivery terms.',
                        'Edit payment terms.',
                        'Edit user designations.',
                        'Edit office departments.',
                    ];
                    $types = [
                        'metric_units',
                        'entity',
                        'fund_cluster',
                        'responsibility_code',
                        'delivery_period',
                        'delivery_address',
                        'mode_of_procurement',
                        'delivery_term',
                        'payment_term',
                        'designation',
                        'office',
                    ];
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 w-full">
                    @for ($i = 0; $i < count($titles); $i++)
                        <div x-data="{
                            modalOpen: false,
                            addModalOpen: false,
                            editModalOpen: false,
                            items: [],
                            type: '{{ $types[$i] }}',
                            newName: '',
                            editId: null,
                            editName: '',
                            isLoading: false,
                            isLoadingItems: false,
                            currentPage: 1,
                            lastPage: 1,
                            total: 0,
                            perPage: 10,
                            hasMorePages: false,
                            nextPageUrl: null,
                            prevPageUrl: null,
                            openModal() {
                                this.modalOpen = true;
                                this.loadItems();
                            },
                            closeModal() {
                                this.modalOpen = false;
                                this.items = [];
                            },
                            loadItems(page = 1) {
                                this.isLoadingItems = true;
                                this.currentPage = page;
                                fetch(`/admin/system-selections/${this.type}/items?page=${page}`, {
                                        method: 'GET',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                            'Accept': 'application/json',
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        this.isLoadingItems = false;
                                        if (data.success) {
                                            this.items = data.items;
                                            this.currentPage = parseInt(data.pagination.current_page);
                                            this.lastPage = parseInt(data.pagination.last_page);
                                            this.total = parseInt(data.pagination.total);
                                            this.perPage = parseInt(data.pagination.per_page);
                                            this.hasMorePages = data.pagination.has_more_pages;
                                            this.nextPageUrl = data.pagination.next_page_url;
                                            this.prevPageUrl = data.pagination.prev_page_url;
                                        } else {
                                            showErrorAlert('Failed to load items.');
                                        }
                                    })
                                    .catch(error => {
                                        this.isLoadingItems = false;
                                        showErrorAlert('Failed to load items.');
                                    });
                            },
                            openAddModal() {
                                this.newName = '';
                                this.addModalOpen = true;
                            },
                            closeAddModal() {
                                this.addModalOpen = false;
                                this.newName = '';
                            },
                            addItem() {
                                if (!this.newName.trim()) return;
                                this.isLoading = true;
                                fetch(`/admin/system-selections/${this.type}/items`, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                        },
                                        body: JSON.stringify({
                                            name: this.newName.trim()
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        this.isLoading = false;
                                        if (data.success) {
                                            this.loadItems(this.currentPage);
                                            this.closeAddModal();
                                            showSuccessAlert(data.message);
                                        } else {
                                            showErrorAlert(data.message || 'Failed to add item.');
                                        }
                                    })
                                    .catch(error => {
                                        this.isLoading = false;
                                        showErrorAlert('Failed to add item.');
                                    });
                            },
                            openEditModal(id) {
                                const item = this.items.find(x => x.id === id);
                                if (item) {
                                    this.editId = id;
                                    this.editName = item.name;
                                    this.editModalOpen = true;
                                }
                            },
                            closeEditModal() {
                                this.editModalOpen = false;
                                this.editId = null;
                                this.editName = '';
                            },
                            saveEdit() {
                                if (!this.editName.trim()) return;
                                this.isLoading = true;
                                fetch(`/admin/system-selections/${this.type}/items/${this.editId}`, {
                                        method: 'PUT',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                        },
                                        body: JSON.stringify({
                                            name: this.editName.trim()
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        this.isLoading = false;
                                        if (data.success) {
                                            this.loadItems(this.currentPage);
                                            this.closeEditModal();
                                            showSuccessAlert(data.message);
                                        } else {
                                            showErrorAlert(data.message || 'Failed to update item.');
                                        }
                                    })
                                    .catch(error => {
                                        this.isLoading = false;
                                        showErrorAlert('Failed to update item.');
                                    });
                            },
                            deleteItem(id) {
                                showConfirmationModal('Are you sure you want to delete this item? This action cannot be undone.', () => {
                                    this.isLoading = true;
                                    fetch(`/admin/system-selections/${this.type}/items/${id}`, {
                                            method: 'DELETE',
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                                'Accept': 'application/json',
                                            }
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            this.isLoading = false;
                                            if (data.success) {
                                                this.loadItems(this.currentPage);
                                                showSuccessAlert(data.message);
                                            } else {
                                                showErrorAlert(data.message || 'Failed to delete item.');
                                            }
                                        })
                                        .catch(error => {
                                            this.isLoading = false;
                                            showErrorAlert('Failed to delete item.');
                                        });
                                });
                            },
                            getPaginationPages() {
                                const pages = [];
                                const current = parseInt(this.currentPage);
                                const last = parseInt(this.lastPage);
                                const start = Math.max(1, current - 2);
                                const end = Math.min(last, current + 2);
                        
                                for (let i = start; i <= end; i++) {
                                    pages.push(i);
                                }
                        
                                return pages;
                            }
                        }" class="relative">
                            <button type="button" @click="openModal()"
                                class="block relative px-6 py-4 border border-gray-300 rounded-lg text-gray-700 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400
                                    hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 hover:border-gray-400 hover:shadow-lg hover:scale-105
                                    active:from-gray-100 active:to-gray-200 active:scale-95 active:shadow-inner
                                    transition-all duration-200 ease-in-out transform overflow-hidden w-full">
                                <span
                                    class="absolute right-4 bottom-2 text-7xl text-gray-300 opacity-20 pointer-events-none select-none">
                                    <i class="iconify" data-icon="{{ $icons[$i] }}"></i>
                                </span>
                                <div class="font-semibold text-lg mb-2">{{ $titles[$i] }}</div>
                                <div class="text-gray-500 text-sm">{{ $descs[$i] }}</div>
                            </button>
                            <!-- Main Modal -->
                            <div x-show="modalOpen" style="display: none;"
                                class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm">
                                <div
                                    class="bg-white rounded-xl shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] flex flex-col">
                                    <!-- Modal Header -->
                                    <div
                                        class="flex items-center justify-between p-6 border-b border-gray-200 flex-shrink-0">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <i class="iconify text-blue-600 text-xl"
                                                    data-icon="{{ $icons[$i] }}"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-semibold text-gray-900">{{ $titles[$i] }}</h3>
                                                <p class="text-sm text-gray-500">{{ $descs[$i] }}</p>
                                            </div>
                                        </div>
                                        <button @click="closeModal()"
                                            class="text-gray-400 hover:text-gray-600 transition-colors">
                                            <i class="iconify text-2xl" data-icon="mdi:close"></i>
                                        </button>
                                    </div>

                                    <!-- Modal Content -->
                                    <div class="p-6 flex-1 overflow-y-auto">
                                        <!-- Header with Add Button -->
                                        <div class="flex items-center justify-between mb-6">
                                            <div class="flex items-center space-x-2">
                                                <i class="iconify text-gray-500"
                                                    data-icon="mdi:format-list-bulleted"></i>
                                                <span class="text-lg font-medium text-gray-900">Items (<span
                                                        x-text="total"></span>)</span>
                                            </div>
                                            <button @click="openAddModal()"
                                                class="relative flex items-center bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-2 rounded-lg text-xs font-medium hover:from-green-600 hover:to-green-700 hover:shadow-lg hover:scale-105 active:from-green-700 active:to-green-800 active:scale-95 active:shadow-inner transition-all duration-200 ease-in-out transform before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                                                <i class="iconify align-middle mr-1.5 text-xs" data-icon="mdi:plus"></i>
                                                Add New
                                            </button>
                                        </div>

                                        <!-- Table -->
                                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                                            <div class="overflow-y-auto max-h-96">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-50 sticky top-0 z-10">
                                                        <tr>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                #</th>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Name</th>
                                                            <th
                                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200"
                                                        x-show="!isLoadingItems">
                                                        <!-- Items List -->
                                                        <template x-for="(item, idx) in items" :key="item.id">
                                                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                                                    x-text="(currentPage - 1) * perPage + (idx + 1)">
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                                                    x-text="item.name"></td>
                                                                <td
                                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                                    <div
                                                                        class="flex items-center justify-center space-x-2">
                                                                        <button @click="openEditModal(item.id)"
                                                                            class="relative flex items-center bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-2 rounded-lg text-xs font-medium hover:from-blue-700 hover:to-blue-800 hover:shadow-lg hover:scale-105 active:from-blue-800 active:to-blue-900 active:scale-95 active:shadow-inner transition-all duration-200 ease-in-out transform before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                                                                            <i class="iconify align-middle mr-1.5 text-xs"
                                                                                data-icon="mdi:pencil"></i>
                                                                            Edit
                                                                        </button>
                                                                        <button @click="deleteItem(item.id)"
                                                                            class="relative flex items-center bg-gradient-to-r from-red-600 to-red-700 text-white px-4 py-2 rounded-lg text-xs font-medium hover:from-red-700 hover:to-red-800 hover:shadow-lg hover:scale-105 active:from-red-800 active:to-red-900 active:scale-95 active:shadow-inner transition-all duration-200 ease-in-out transform before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                                                                            <i class="iconify align-middle mr-1.5 text-xs"
                                                                                data-icon="mdi:delete"></i>
                                                                            Delete
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </template>

                                                        <!-- Empty State -->
                                                        <template x-if="items.length === 0">
                                                            <tr>
                                                                <td colspan="3" class="px-6 py-12 text-center">
                                                                    <div class="flex flex-col items-center">
                                                                        <i class="iconify text-gray-400 text-4xl mb-3"
                                                                            data-icon="mdi:database-off"></i>
                                                                        <p class="text-gray-500 text-sm">No items found
                                                                        </p>
                                                                        <button @click="openAddModal()"
                                                                            class="mt-3 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                                            Add your first item
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                    </tbody>

                                                    <!-- Loading State -->
                                                    <tbody class="bg-white divide-y divide-gray-200"
                                                        x-show="isLoadingItems">
                                                        <tr>
                                                            <td colspan="3" class="px-6 py-12 text-center">
                                                                <div class="flex flex-col items-center">
                                                                    <i class="iconify text-blue-500 text-4xl mb-3 animate-spin"
                                                                        data-icon="mdi:loading"></i>
                                                                    <p class="text-gray-500 text-sm">Loading
                                                                        items...
                                                                    </p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Custom Pagination - Only show when there are more than 10 items -->
                                        <template x-if="total > 10">
                                            <div class="flex justify-center mt-6">
                                                <div class="flex items-center space-x-2">
                                                    <template x-if="currentPage === 1">
                                                        <span class="px-3 py-2 text-gray-400 cursor-not-allowed">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                                            </svg>
                                                        </span>
                                                    </template>
                                                    <template x-if="currentPage > 1">
                                                        <button @click="loadItems(currentPage - 1)"
                                                            class="px-3 py-2 text-gray-600 hover:text-blue-600 transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                                            </svg>
                                                        </button>
                                                    </template>

                                                    <template x-for="page in getPaginationPages()"
                                                        :key="page">
                                                        <button @click="page != currentPage ? loadItems(page) : null"
                                                            :class="page == currentPage ?
                                                                'px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md cursor-default' :
                                                                'px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors cursor-pointer'"
                                                            x-text="page"></button>
                                                    </template>

                                                    <template x-if="hasMorePages">
                                                        <button @click="loadItems(currentPage + 1)"
                                                            class="px-3 py-2 text-gray-600 hover:text-blue-600 transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                            </svg>
                                                        </button>
                                                    </template>
                                                    <template x-if="!hasMorePages">
                                                        <span class="px-3 py-2 text-gray-400 cursor-not-allowed">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                            </svg>
                                                        </span>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Modal -->
                            <div x-show="addModalOpen" style="display: none;"
                                class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm">
                                <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
                                    <div class="p-6">
                                        <div class="flex items-center space-x-3 mb-4">
                                            <div
                                                class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                                <i class="iconify text-green-600 text-xl" data-icon="mdi:plus"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">Add New
                                                    {{ $titles[$i] }}</h3>
                                                <p class="text-sm text-gray-500">Enter the name for the new item</p>
                                            </div>
                                        </div>

                                        <div class="space-y-4">
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                                                <input type="text" x-model="newName" @keydown.enter="addItem()"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors placeholder-gray-500"
                                                    placeholder="Enter name...">
                                            </div>

                                            <div class="flex space-x-3 pt-4">
                                                <button @click="addItem()" :disabled="!newName.trim() || isLoading"
                                                    :class="{ 'opacity-50 cursor-not-allowed': !newName.trim() || isLoading }"
                                                    class="flex-1 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                                                    <i x-show="isLoading" class="iconify mr-2 animate-spin"
                                                        data-icon="mdi:loading"></i>
                                                    <span x-show="!isLoading">Add Item</span>
                                                    <span x-show="isLoading" x-cloak>Add Item</span>
                                                </button>
                                                <button @click="closeAddModal()"
                                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium transition-colors duration-200">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Modal -->
                            <div x-show="editModalOpen" style="display: none;"
                                class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm">
                                <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
                                    <div class="p-6">
                                        <div class="flex items-center space-x-3 mb-4">
                                            <div
                                                class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <i class="iconify text-blue-600 text-xl" data-icon="mdi:pencil"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">Edit
                                                    {{ $titles[$i] }}</h3>
                                                <p class="text-sm text-gray-500">Update the item name</p>
                                            </div>
                                        </div>

                                        <div class="space-y-4">
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                                                <input type="text" x-model="editName" @keydown.enter="saveEdit()"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors placeholder-gray-500"
                                                    placeholder="Enter name...">
                                            </div>

                                            <div class="flex space-x-3 pt-4">
                                                <button @click="saveEdit()" :disabled="!editName.trim() || isLoading"
                                                    :class="{ 'opacity-50 cursor-not-allowed': !editName.trim() || isLoading }"
                                                    class="flex-1 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                                                    <i x-show="isLoading" class="iconify mr-2 animate-spin"
                                                        data-icon="mdi:loading"></i>
                                                    <span x-show="!isLoading">Update Item</span>
                                                    <span x-show="isLoading" x-cloak>Update Item</span>
                                                </button>
                                                <button @click="closeEditModal()"
                                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium transition-colors duration-200">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor

                    {{-- Recommending Approval Cards --}}
                    @php
                        $approvalTypes = ['primary', 'secondary'];
                        $approvalTitles = ['Primary Approver', 'Secondary Approver'];
                        $approvalDescs = ['Manage primary approver.', 'Manage secondary approver.'];
                    @endphp

                    @for ($j = 0; $j < count($approvalTypes); $j++)
                        <div x-data="{
                            modalOpen: false,
                            addModalOpen: false,
                            editModalOpen: false,
                            items: [],
                            type: '{{ $approvalTypes[$j] }}',
                            newFirstName: '',
                            newMiddleName: '',
                            newLastName: '',
                            newDesignationId: '',
                            newOfficeIds: [],
                            editId: null,
                            editFirstName: '',
                            editMiddleName: '',
                            editLastName: '',
                            editDesignationId: '',
                            editOfficeIds: [],
                            isLoading: false,
                            isLoadingItems: false,
                            currentPage: 1,
                            lastPage: 1,
                            total: 0,
                            perPage: 10,
                            hasMorePages: false,
                            nextPageUrl: null,
                            prevPageUrl: null,
                            openModal() {
                                this.modalOpen = true;
                                this.loadItems();
                            },
                            closeModal() {
                                this.modalOpen = false;
                                this.items = [];
                            },
                            loadItems(page = 1) {
                                this.isLoadingItems = true;
                                this.currentPage = page;
                                fetch(`/admin/recommending-approvals/${this.type}/items?page=${page}`, {
                                        method: 'GET',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                            'Accept': 'application/json',
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        this.isLoadingItems = false;
                                        if (data.success) {
                                            this.items = data.items;
                                            this.currentPage = parseInt(data.pagination.current_page);
                                            this.lastPage = parseInt(data.pagination.last_page);
                                            this.total = parseInt(data.pagination.total);
                                            this.perPage = parseInt(data.pagination.per_page);
                                            this.hasMorePages = data.pagination.has_more_pages;
                                            this.nextPageUrl = data.pagination.next_page_url;
                                            this.prevPageUrl = data.pagination.prev_page_url;
                                        } else {
                                            showErrorAlert('Failed to load items.');
                                        }
                                    })
                                    .catch(error => {
                                        this.isLoadingItems = false;
                                        showErrorAlert('Failed to load items.');
                                    });
                            },
                            openAddModal() {
                                this.newFirstName = '';
                                this.newMiddleName = '';
                                this.newLastName = '';
                                this.newDesignationId = '';
                                this.newOfficeIds = [];
                                this.addModalOpen = true;
                            },
                            closeAddModal() {
                                this.addModalOpen = false;
                                this.newFirstName = '';
                                this.newMiddleName = '';
                                this.newLastName = '';
                                this.newDesignationId = '';
                                this.newOfficeIds = [];
                            },
                            addItem() {
                                if (!this.newFirstName.trim() || !this.newLastName.trim() || !this.newDesignationId || this.newOfficeIds.length === 0) return;
                                this.isLoading = true;
                                fetch(`/admin/recommending-approvals/${this.type}/items`, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                        },
                                        body: JSON.stringify({
                                            first_name: this.newFirstName.trim(),
                                            middle_name: this.newMiddleName.trim(),
                                            last_name: this.newLastName.trim(),
                                            designation_id: this.newDesignationId,
                                            office_ids: this.newOfficeIds
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        this.isLoading = false;
                                        if (data.success) {
                                            this.loadItems(this.currentPage);
                                            this.closeAddModal();
                                            showSuccessAlert(data.message);
                                        } else {
                                            showErrorAlert(data.message || 'Failed to add item.');
                                        }
                                    })
                                    .catch(error => {
                                        this.isLoading = false;
                                        showErrorAlert('Failed to add item.');
                                    });
                            },
                            openEditModal(id) {
                                const item = this.items.find(x => x.id === id);
                                if (item) {
                                    this.editId = id;
                                    this.editFirstName = item.first_name;
                                    this.editMiddleName = item.middle_name || '';
                                    this.editLastName = item.last_name;
                                    this.editDesignationId = item.designation_id;
                                    this.editOfficeIds = item.offices ? item.offices.map(o => o.id) : [];
                                    this.editModalOpen = true;
                                }
                            },
                            closeEditModal() {
                                this.editModalOpen = false;
                                this.editId = null;
                                this.editFirstName = '';
                                this.editMiddleName = '';
                                this.editLastName = '';
                                this.editDesignationId = '';
                                this.editOfficeIds = [];
                            },
                            saveEdit() {
                                if (!this.editFirstName.trim() || !this.editLastName.trim() || !this.editDesignationId || this.editOfficeIds.length === 0) return;
                                this.isLoading = true;
                                fetch(`/admin/recommending-approvals/${this.type}/items/${this.editId}`, {
                                        method: 'PUT',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                        },
                                        body: JSON.stringify({
                                            first_name: this.editFirstName.trim(),
                                            middle_name: this.editMiddleName.trim(),
                                            last_name: this.editLastName.trim(),
                                            designation_id: this.editDesignationId,
                                            office_ids: this.editOfficeIds
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        this.isLoading = false;
                                        if (data.success) {
                                            this.loadItems(this.currentPage);
                                            this.closeEditModal();
                                            showSuccessAlert(data.message);
                                        } else {
                                            showErrorAlert(data.message || 'Failed to update item.');
                                        }
                                    })
                                    .catch(error => {
                                        this.isLoading = false;
                                        showErrorAlert('Failed to update item.');
                                    });
                            },
                            deleteItem(id) {
                                showConfirmationModal('Are you sure you want to delete this approver?', () => {
                                    fetch(`/admin/recommending-approvals/${this.type}/items/${id}`, {
                                            method: 'DELETE',
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                                'Accept': 'application/json',
                                            }
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success) {
                                                this.loadItems(this.currentPage);
                                                showSuccessAlert(data.message);
                                            } else {
                                                showErrorAlert(data.message || 'Failed to delete item.');
                                            }
                                        })
                                        .catch(error => {
                                            showErrorAlert('Failed to delete item.');
                                        });
                                });
                            }
                        }" class="relative">
                            <button type="button" @click="openModal()"
                                class="block relative px-6 py-4 border border-gray-300 rounded-lg text-gray-700 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400
                                    hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 hover:border-gray-400 hover:shadow-lg hover:scale-105
                                    active:from-gray-100 active:to-gray-200 active:scale-95 active:shadow-inner
                                    transition-all duration-200 ease-in-out transform overflow-hidden w-full">
                                <span
                                    class="absolute right-4 bottom-2 text-7xl text-gray-300 opacity-20 pointer-events-none select-none">
                                    <i class="iconify" data-icon="mdi:account-check"></i>
                                </span>
                                <div class="font-semibold text-lg mb-2">{{ $approvalTitles[$j] }}</div>
                                <div class="text-gray-500 text-sm">{{ $approvalDescs[$j] }}</div>
                            </button>

                            {{-- Main Modal --}}
                            <div x-show="modalOpen" style="display: none;"
                                class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm">
                                <div
                                    class="bg-white rounded-xl shadow-xl w-full max-w-6xl mx-4 max-h-[90vh] flex flex-col">
                                    {{-- Modal Header --}}
                                    <div
                                        class="flex items-center justify-between p-6 border-b border-gray-200 flex-shrink-0">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <i class="iconify text-blue-600 text-xl"
                                                    data-icon="mdi:account-check"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-semibold text-gray-900">
                                                    {{ $approvalTitles[$j] }}
                                                </h3>
                                                <p class="text-sm text-gray-500">{{ $approvalDescs[$j] }}</p>
                                            </div>
                                        </div>
                                        <button @click="closeModal()"
                                            class="text-gray-400 hover:text-gray-600 transition-colors">
                                            <i class="iconify text-2xl" data-icon="mdi:close"></i>
                                        </button>
                                    </div>

                                    {{-- Modal Content --}}
                                    <div class="p-6 flex-1 overflow-y-auto">
                                        {{-- Header with Add Button --}}
                                        <div class="flex items-center justify-between mb-6">
                                            <div class="flex items-center space-x-2">
                                                <i class="iconify text-gray-500"
                                                    data-icon="mdi:format-list-bulleted"></i>
                                                <span class="text-lg font-medium text-gray-900">Approvers (<span
                                                        x-text="total"></span>)</span>
                                            </div>
                                            <button @click="openAddModal()"
                                                class="relative flex items-center bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-2 rounded-lg text-xs font-medium hover:from-green-600 hover:to-green-700 hover:shadow-lg hover:scale-105 active:from-green-700 active:to-green-800 active:scale-95 active:shadow-inner transition-all duration-200 ease-in-out transform before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                                                <i class="iconify align-middle mr-1.5 text-xs"
                                                    data-icon="mdi:plus"></i>
                                                <span class="align-middle">Add Approver</span>
                                            </button>
                                        </div>

                                        {{-- Table --}}
                                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                                            <div class="overflow-y-auto max-h-96">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-50 sticky top-0 z-10">
                                                        <tr>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                #</th>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Name</th>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Designation</th>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Office</th>
                                                            <th
                                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Actions</th>
                                                        </tr>
                                                    </thead>

                                                    {{-- Items List --}}
                                                    <tbody class="bg-white divide-y divide-gray-200"
                                                        x-show="!isLoadingItems && items.length > 0">
                                                        <template x-for="(item, idx) in items" :key="item.id">
                                                            <tr
                                                                class="hover:bg-gray-50 transition-colors duration-150">
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                                                    x-text="(currentPage - 1) * perPage + (idx + 1)">
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    <div class="text-sm font-medium text-gray-900">
                                                                        <span x-text="item.first_name"></span>
                                                                        <span x-text="item.middle_name"></span>
                                                                        <span x-text="item.last_name"></span>
                                                                    </div>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    <div class="text-sm text-gray-900"
                                                                        x-text="item.designation ? item.designation.name : 'N/A'">
                                                                    </div>
                                                                </td>
                                                                <td class="px-6 py-4">
                                                                    <div class="text-sm text-gray-900">
                                                                        <span
                                                                            x-text="item.offices && item.offices.length > 0 ? item.offices.map(o => o.name).join(', ') : 'N/A'"></span>
                                                                    </div>
                                                                </td>
                                                                <td
                                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                                    <div
                                                                        class="flex items-center justify-center space-x-2">
                                                                        <button @click="openEditModal(item.id)"
                                                                            class="relative flex items-center bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-2 rounded-lg text-xs font-medium hover:from-blue-700 hover:to-blue-800 hover:shadow-lg hover:scale-105 active:from-blue-800 active:to-blue-900 active:scale-95 active:shadow-inner transition-all duration-200 ease-in-out transform before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                                                                            <i class="iconify align-middle mr-1.5 text-xs"
                                                                                data-icon="mdi:pencil"></i>
                                                                            Edit
                                                                        </button>
                                                                        <button @click="deleteItem(item.id)"
                                                                            class="relative flex items-center bg-gradient-to-r from-red-600 to-red-700 text-white px-4 py-2 rounded-lg text-xs font-medium hover:from-red-700 hover:to-red-800 hover:shadow-lg hover:scale-105 active:from-red-800 active:to-red-900 active:scale-95 active:shadow-inner transition-all duration-200 ease-in-out transform before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                                                                            <i class="iconify align-middle mr-1.5 text-xs"
                                                                                data-icon="mdi:delete"></i>
                                                                            Delete
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                    </tbody>

                                                    {{-- Empty State --}}
                                                    <tbody class="bg-white divide-y divide-gray-200"
                                                        x-show="!isLoadingItems && items.length === 0">
                                                        <tr>
                                                            <td colspan="5" class="px-6 py-12 text-center">
                                                                <div class="flex flex-col items-center">
                                                                    <i class="iconify text-gray-400 text-4xl mb-3"
                                                                        data-icon="mdi:database-off"></i>
                                                                    <p class="text-gray-500 text-sm">No approvers found
                                                                    </p>
                                                                    <button @click="openAddModal()"
                                                                        class="mt-3 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                                        Add your first approver
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>

                                                    {{-- Loading State --}}
                                                    <tbody class="bg-white divide-y divide-gray-200"
                                                        x-show="isLoadingItems">
                                                        <tr>
                                                            <td colspan="5" class="px-6 py-12 text-center">
                                                                <div class="flex flex-col items-center">
                                                                    <i class="iconify text-blue-500 text-4xl mb-3 animate-spin"
                                                                        data-icon="mdi:loading"></i>
                                                                    <p class="text-gray-500 text-sm">Loading items...
                                                                    </p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            {{-- Pagination --}}
                                            <div x-show="lastPage > 1"
                                                class="flex items-center justify-between px-6 py-3 bg-gray-50 border-t border-gray-200">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-sm text-gray-700">
                                                        Showing <span class="font-medium"
                                                            x-text="((currentPage - 1) * perPage) + 1"></span>
                                                        to <span class="font-medium"
                                                            x-text="Math.min(currentPage * perPage, total)"></span>
                                                        of <span class="font-medium" x-text="total"></span> results
                                                    </span>
                                                </div>
                                                <div class="flex space-x-2">
                                                    <button @click="loadItems(currentPage - 1)"
                                                        :disabled="currentPage === 1"
                                                        :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' :
                                                            'hover:bg-gray-300'"
                                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg transition-colors duration-150">
                                                        <i class="iconify mr-1" data-icon="mdi:chevron-left"></i>
                                                        Previous
                                                    </button>
                                                    <button @click="loadItems(currentPage + 1)"
                                                        :disabled="currentPage === lastPage"
                                                        :class="currentPage === lastPage ? 'opacity-50 cursor-not-allowed' :
                                                            'hover:bg-gray-300'"
                                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg transition-colors duration-150">
                                                        Next
                                                        <i class="iconify ml-1" data-icon="mdi:chevron-right"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Add Modal --}}
                            <div x-show="addModalOpen" style="display: none;"
                                class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm">
                                <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl mx-4">
                                    <div class="p-6 border-b border-gray-200">
                                        <h3 class="text-lg font-semibold text-gray-900">Add New Approver</h3>
                                        <p class="text-sm text-gray-500 mt-1">Fill in the required information below
                                        </p>
                                    </div>
                                    <div class="p-6 space-y-5">
                                        <div class="grid grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name
                                                    <span class="text-red-500">*</span></label>
                                                <input type="text" x-model="newFirstName"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500"
                                                    placeholder="Enter first name" maxlength="25">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Middle
                                                    Name</label>
                                                <input type="text" x-model="newMiddleName"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500"
                                                    placeholder="Enter middle name" maxlength="25">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name
                                                    <span class="text-red-500">*</span></label>
                                                <input type="text" x-model="newLastName"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500"
                                                    placeholder="Enter last name" maxlength="25">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Designation
                                                <span class="text-red-500">*</span></label>
                                            <select x-model="newDesignationId"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Select designation</option>
                                                @foreach ($designations as $designation)
                                                    <option value="{{ $designation->id }}">{{ $designation->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Offices <span
                                                    class="text-red-500">*</span></label>
                                            <div
                                                class="border border-gray-300 rounded-lg p-3 bg-gray-50 max-h-48 overflow-y-auto">
                                                <div class="space-y-2">
                                                    @foreach ($offices as $office)
                                                        <label
                                                            class="flex items-center space-x-3 p-2 hover:bg-white rounded cursor-pointer transition-colors">
                                                            <input type="checkbox" :value="{{ $office->id }}"
                                                                x-model="newOfficeIds"
                                                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                                            <span
                                                                class="text-sm text-gray-700">{{ $office->name }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-2 flex items-center">
                                                <i class="iconify mr-1" data-icon="mdi:information-outline"></i>
                                                Select one or more offices
                                            </p>
                                        </div>
                                    </div>
                                    <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                                        <button @click="closeAddModal()"
                                            class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition-colors">
                                            Cancel
                                        </button>
                                        <button @click="addItem()"
                                            :disabled="!newFirstName.trim() || !newLastName.trim() || !newDesignationId ||
                                                newOfficeIds.length === 0"
                                            :class="!newFirstName.trim() || !newLastName.trim() || !newDesignationId ||
                                                newOfficeIds.length === 0 ?
                                                'opacity-50 cursor-not-allowed bg-green-400' :
                                                'hover:bg-green-700 bg-green-600'"
                                            class="px-5 py-2.5 text-white rounded-lg font-medium transition-all">
                                            <span class="flex items-center">
                                                <i class="iconify mr-1.5" data-icon="mdi:plus"></i>
                                                Add Approver
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Edit Modal --}}
                            <div x-show="editModalOpen" style="display: none;"
                                class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm">
                                <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl mx-4">
                                    <div class="p-6 border-b border-gray-200">
                                        <h3 class="text-lg font-semibold text-gray-900">Edit Approver</h3>
                                        <p class="text-sm text-gray-500 mt-1">Update the approver information below</p>
                                    </div>
                                    <div class="p-6 space-y-5">
                                        <div class="grid grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name
                                                    <span class="text-red-500">*</span></label>
                                                <input type="text" x-model="editFirstName"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500"
                                                    placeholder="Enter first name" maxlength="25">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Middle
                                                    Name</label>
                                                <input type="text" x-model="editMiddleName"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500"
                                                    placeholder="Enter middle name" maxlength="25">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name
                                                    <span class="text-red-500">*</span></label>
                                                <input type="text" x-model="editLastName"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500"
                                                    placeholder="Enter last name" maxlength="25">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Designation
                                                <span class="text-red-500">*</span></label>
                                            <select x-model="editDesignationId"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Select designation</option>
                                                @foreach ($designations as $designation)
                                                    <option value="{{ $designation->id }}">{{ $designation->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Offices <span
                                                    class="text-red-500">*</span></label>
                                            <div
                                                class="border border-gray-300 rounded-lg p-3 bg-gray-50 max-h-48 overflow-y-auto">
                                                <div class="space-y-2">
                                                    @foreach ($offices as $office)
                                                        <label
                                                            class="flex items-center space-x-3 p-2 hover:bg-white rounded cursor-pointer transition-colors">
                                                            <input type="checkbox" :value="{{ $office->id }}"
                                                                x-model="editOfficeIds"
                                                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                                            <span
                                                                class="text-sm text-gray-700">{{ $office->name }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-2 flex items-center">
                                                <i class="iconify mr-1" data-icon="mdi:information-outline"></i>
                                                Select one or more offices
                                            </p>
                                        </div>
                                    </div>
                                    <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                                        <button @click="closeEditModal()"
                                            class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition-colors">
                                            Cancel
                                        </button>
                                        <button @click="saveEdit()"
                                            :disabled="!editFirstName.trim() || !editLastName.trim() || !editDesignationId ||
                                                editOfficeIds.length === 0"
                                            :class="!editFirstName.trim() || !editLastName.trim() || !editDesignationId ||
                                                editOfficeIds.length === 0 ?
                                                'opacity-50 cursor-not-allowed bg-blue-400' :
                                                'hover:bg-blue-700 bg-blue-600'"
                                            class="px-5 py-2.5 text-white rounded-lg font-medium transition-all">
                                            <span class="flex items-center">
                                                <i class="iconify mr-1.5" data-icon="mdi:content-save"></i>
                                                Save Changes
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</x-page-layout>

<script>
    function showSuccessAlert(message) {
        const alertDiv = document.createElement("div");
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

        const closeBtn = document.createElement("button");
        closeBtn.textContent = "×";
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
        setTimeout(() => alertDiv.parentNode && alertDiv.remove(), 3000);
    }

    function showErrorAlert(message) {
        const alertDiv = document.createElement("div");
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

        const closeBtn = document.createElement("button");
        closeBtn.textContent = "×";
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
        setTimeout(() => alertDiv.parentNode && alertDiv.remove(), 3000);
    }

    function showConfirmationModal(message, onConfirm) {
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
                <button id="confirm-yes" style="
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
                <button id="confirm-no" style="
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
            @keyframes fadeOut {
                from { opacity: 1; }
                to { opacity: 0; }
            }
        `;
        document.head.appendChild(style);

        // Event listeners
        document.getElementById('confirm-yes').onclick = () => {
            confirmDiv.remove();
            onConfirm();
        };

        document.getElementById('confirm-no').onclick = () => {
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
</script>
