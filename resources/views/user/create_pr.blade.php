<x-page-layout>
    <x-slot name="header">
        <x-app-header :homeUrl="route('user')" :title="$pageTitle ?? __('DSWD-PRISM')" :userName="Auth::user()->first_name .
            (Auth::user()->middle_name ? ' ' . Auth::user()->middle_name : '') .
            ' ' .
            Auth::user()->last_name" :recentActivities="$recentActivities ?? collect()" />
    </x-slot>

    <!-- Main Content -->
    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-visible shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Create New Purchase Request') }}
                    </h2>
                    <a href="{{ route('user.requests') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        ← Back to My Requests
                    </a>
                </div>

                <div class="space-y-6">
                    <form method="POST" action="{{ route('purchase-requests.store') }}" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf

                        <!-- Basic Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="entity_name" class="block text-sm font-medium text-gray-700">Entity
                                    Name <span class="text-red-500">*</span></label>
                                <select name="entity_name" id="entity_name" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">Select entity</option>
                                    @foreach ($entities as $entity)
                                        <option value="{{ $entity->name }}"
                                            {{ old('entity_name') == $entity->name ? 'selected' : '' }}>
                                            {{ $entity->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="fund_cluster" class="block text-sm font-medium text-gray-700">Fund
                                    Cluster <span class="text-red-500">*</span></label>
                                <select name="fund_cluster" id="fund_cluster" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">Select fund cluster</option>
                                    @foreach ($fundClusters as $fundCluster)
                                        <option value="{{ $fundCluster->name }}"
                                            {{ old('fund_cluster') == $fundCluster->name ? 'selected' : '' }}>
                                            {{ $fundCluster->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="office_section"
                                    class="block text-sm font-medium text-gray-700">Office/Section <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="office_section" id="office_section"
                                    value="{{ old('office_section', auth()->user()->office) }}" required readonly
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>

                            <div>
                                <label for="responsibility_center_code"
                                    class="block text-sm font-medium text-gray-700">Responsibility Center Code <span
                                        class="text-red-500">*</span></label>
                                <select name="responsibility_center_code" id="responsibility_center_code" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">Select responsibility code</option>
                                    @foreach ($responsibilityCodes as $code)
                                        <option value="{{ $code->name }}"
                                            {{ old('responsibility_center_code') == $code->name ? 'selected' : '' }}>
                                            {{ $code->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700">Date <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="date" id="date"
                                    value="{{ old('date', date('Y-m-d')) }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>

                            <div>
                                <label for="stoc_property_no"
                                    class="block text-sm font-medium text-gray-700">STOC/Property No.</label>
                                <input type="text" name="stoc_property_no" id="stoc_property_no"
                                    value="{{ old('stoc_property_no') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>
                        </div>

                        <!-- Item Details -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Item Details</h3>
                            <div id="items-container">
                                <div class="item-fields border border-gray-200 rounded-lg p-4 mb-4">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Unit <span
                                                    class="text-red-500">*</span></label>
                                            <select name="unit[]" required
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                <option value="">Select unit</option>
                                                @foreach ($metricUnits as $unit)
                                                    <option value="{{ $unit->name }}">{{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Quantity <span
                                                    class="text-red-500">*</span></label>
                                            <input type="number" name="quantity[]" min="1" required
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Unit Cost (₱) <span
                                                    class="text-red-500">*</span></label>
                                            <input type="number" name="unit_cost[]" min="0" step="0.01"
                                                required
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        </div>
                                        <div class="md:col-span-3 mt-2">
                                            <label class="block text-sm font-medium text-gray-700">Item Description
                                                <span class="text-red-500">*</span></label>
                                            <textarea name="item_description[]" rows="3" required
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                                        </div>
                                        <div class="md:col-span-3 mt-2 flex justify-end">
                                            <button type="button"
                                                class="remove-item-btn relative bg-gradient-to-r from-red-500 to-red-600 text-white font-bold py-1 px-3 rounded text-sm
                                                       hover:from-red-600 hover:to-red-700 hover:shadow-lg hover:scale-105
                                                       active:from-red-700 active:to-red-800 active:scale-95 active:shadow-inner
                                                       transition-all duration-200 ease-in-out transform
                                                       before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded
                                                       hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200"
                                                style="display: none;">
                                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                                Remove Item
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="add-item-btn"
                                class="relative mt-2 bg-gradient-to-r from-green-500 to-green-600 text-white font-bold py-2 px-4 rounded-lg
                                       hover:from-green-600 hover:to-green-700 hover:shadow-lg hover:scale-105
                                       active:from-green-700 active:to-green-800 active:scale-95 active:shadow-inner
                                       transition-all duration-200 ease-in-out transform
                                       before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                                       hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Another Item
                            </button>
                        </div>

                        <!-- Delivery Information -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Delivery Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="delivery_period"
                                        class="block text-sm font-medium text-gray-700">Delivery Period <span
                                            class="text-red-500">*</span></label>
                                    <select name="delivery_period" id="delivery_period" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option value="">Select delivery period</option>
                                        @foreach ($deliveryPeriods as $period)
                                            <option value="{{ $period->name }}"
                                                {{ old('delivery_period') == $period->name ? 'selected' : '' }}>
                                                {{ $period->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="delivery_address"
                                        class="block text-sm font-medium text-gray-700">Delivery Address <span
                                            class="text-red-500">*</span></label>
                                    <select name="delivery_address" id="delivery_address" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option value="">Select delivery address</option>
                                        @foreach ($deliveryAddresses as $address)
                                            <option value="{{ $address->name }}"
                                                {{ old('delivery_address') == $address->name ? 'selected' : '' }}>
                                                {{ $address->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Purpose -->
                        <div class="border-t pt-6">
                            <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose <span
                                    class="text-red-500">*</span></label>
                            <textarea name="purpose" id="purpose" rows="3" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('purpose') }}</textarea>
                        </div>

                        <!-- Requested By -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Requested By</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="requested_by_name"
                                        class="block text-sm font-medium text-gray-700">Printed Name <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="requested_by_name" id="requested_by_name"
                                        value="{{ old('requested_by_name', auth()->user()->first_name . (auth()->user()->middle_name ? ' ' . auth()->user()->middle_name : '') . ' ' . auth()->user()->last_name) }}"
                                        required readonly
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </div>

                                <div>
                                    <label for="requested_by_designation"
                                        class="block text-sm font-medium text-gray-700">Designation <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="requested_by_designation"
                                        id="requested_by_designation"
                                        value="{{ old('requested_by_designation', auth()->user()->designation) }}"
                                        required readonly
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </div>

                                {{-- <div>
                                    <label for="requested_by_signature"
                                        class="block text-sm font-medium text-gray-700">Signature (Optional)</label>
                                    <input type="file" name="requested_by_signature" id="requested_by_signature"
                                        accept="image/*"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div> --}}
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end space-x-3 pt-6">
                            <a href="{{ route('user.requests') }}"
                                class="relative bg-gradient-to-r from-gray-500 to-gray-600 text-white font-bold py-2 px-4 rounded-lg
                                       hover:from-gray-600 hover:to-gray-700 hover:shadow-lg hover:scale-105
                                       active:from-gray-700 active:to-gray-800 active:scale-95 active:shadow-inner
                                       transition-all duration-200 ease-in-out transform
                                       before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                                       hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel
                            </a>
                            <button type="submit"
                                class="relative bg-gradient-to-r from-blue-500 to-blue-600 text-white font-bold py-2 px-4 rounded-lg
                                       hover:from-blue-600 hover:to-blue-700 hover:shadow-lg hover:scale-105
                                       active:from-blue-700 active:to-blue-800 active:scale-95 active:shadow-inner
                                       transition-all duration-200 ease-in-out transform
                                       before:absolute before:inset-0 before:bg-white before:opacity-0 before:rounded-lg
                                       hover:before:opacity-10 active:before:opacity-20 before:transition-opacity before:duration-200">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Create Purchase Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const itemsContainer = document.getElementById('items-container');
                const addItemBtn = document.getElementById('add-item-btn');

                // Function to update remove button visibility
                function updateRemoveButtons() {
                    const itemFields = itemsContainer.querySelectorAll('.item-fields');
                    itemFields.forEach((item, index) => {
                        const removeBtn = item.querySelector('.remove-item-btn');
                        if (itemFields.length > 1) {
                            removeBtn.style.display = 'inline-block';
                        } else {
                            removeBtn.style.display = 'none';
                        }
                    });
                }

                // Function to create a new item
                function createNewItem() {
                    const firstItem = itemsContainer.querySelector('.item-fields');
                    const newItem = firstItem.cloneNode(true);

                    // Clear all input values
                    newItem.querySelectorAll('input, textarea, select').forEach(input => {
                        if (input.type === 'select-one') {
                            input.selectedIndex = 0;
                        } else {
                            input.value = '';
                        }
                    });

                    // Add event listener to the remove button
                    const removeBtn = newItem.querySelector('.remove-item-btn');
                    removeBtn.addEventListener('click', function() {
                        if (itemsContainer.querySelectorAll('.item-fields').length > 1) {
                            newItem.remove();
                            updateRemoveButtons();
                        }
                    });

                    return newItem;
                }

                // Add event listener to existing remove button
                const existingRemoveBtn = itemsContainer.querySelector('.remove-item-btn');
                if (existingRemoveBtn) {
                    existingRemoveBtn.addEventListener('click', function() {
                        if (itemsContainer.querySelectorAll('.item-fields').length > 1) {
                            existingRemoveBtn.closest('.item-fields').remove();
                            updateRemoveButtons();
                        }
                    });
                }

                // Add item button functionality
                addItemBtn.addEventListener('click', function() {
                    const newItem = createNewItem();
                    itemsContainer.appendChild(newItem);
                    updateRemoveButtons();
                });

                // Initial update of remove button visibility
                updateRemoveButtons();
            });
        </script>
</x-page-layout>
