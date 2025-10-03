<x-page-layout>
    <x-slot name="header">
        <x-app-header :homeUrl="route('user')" :title="$pageTitle ?? __('DSWD-PRISM')" :userName="Auth::user()->first_name .
            (Auth::user()->middle_name ? ' ' . Auth::user()->middle_name : '') .
            ' ' .
            Auth::user()->last_name" :recentActivities="$recentActivities ?? collect()" />
    </x-slot>

    <!-- Main Content -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Create New Purchase Request') }}
                </h2>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('purchase-requests.store') }}" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf

                        <!-- Basic Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="entity_name" class="block text-sm font-medium text-gray-700">Entity
                                    Name</label>
                                <input type="text" name="entity_name" id="entity_name"
                                    value="{{ old('entity_name') }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="fund_cluster" class="block text-sm font-medium text-gray-700">Fund
                                    Cluster</label>
                                <input type="text" name="fund_cluster" id="fund_cluster"
                                    value="{{ old('fund_cluster') }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="office_section"
                                    class="block text-sm font-medium text-gray-700">Office/Section</label>
                                <input type="text" name="office_section" id="office_section"
                                    value="{{ old('office_section', auth()->user()->office) }}" required readonly
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="responsibility_center_code"
                                    class="block text-sm font-medium text-gray-700">Responsibility Center Code</label>
                                <input type="text" name="responsibility_center_code" id="responsibility_center_code"
                                    value="{{ old('responsibility_center_code') }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                                <input type="date" name="date" id="date"
                                    value="{{ old('date', date('Y-m-d')) }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="stoc_property_no"
                                    class="block text-sm font-medium text-gray-700">STOC/Property No.</label>
                                <input type="text" name="stoc_property_no" id="stoc_property_no"
                                    value="{{ old('stoc_property_no') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
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
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Select unit</option>

                                                <!-- Basic -->
                                                <option value="pcs">pcs</option>
                                                <option value="set">set</option>
                                                <option value="pair">pair</option>
                                                <option value="dozen">dozen</option>
                                                <option value="lot">lot</option>

                                                <!-- Packaging -->
                                                <option value="box">box</option>
                                                <option value="pack">pack</option>
                                                <option value="carton">carton</option>
                                                <option value="case">case</option>
                                                <option value="roll">roll</option>
                                                <option value="ream">ream</option>
                                                <option value="bundle">bundle</option>
                                                <option value="tube">tube</option>
                                                <option value="bottle">bottle</option>
                                                <option value="can">can</option>
                                                <option value="jar">jar</option>
                                                <option value="sachet">sachet</option>
                                                <option value="drum">drum</option>
                                                <option value="barrel">barrel</option>
                                                <option value="bag">bag</option>

                                                <!-- Weight -->
                                                <option value="g">g</option>
                                                <option value="kg">kg</option>
                                                <option value="lb">lb</option>
                                                <option value="ton">ton</option>

                                                <!-- Volume -->
                                                <option value="ml">ml</option>
                                                <option value="l">L</option>
                                                <option value="gal">gal</option>

                                                <!-- Length -->
                                                <option value="mm">mm</option>
                                                <option value="cm">cm</option>
                                                <option value="m">m</option>
                                                <option value="km">km</option>

                                                <!-- Area -->
                                                <option value="sqm">sqm</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Quantity <span
                                                    class="text-red-500">*</span></label>
                                            <input type="number" name="quantity[]" min="1" required
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Unit Cost (₱) <span
                                                    class="text-red-500">*</span></label>
                                            <input type="number" name="unit_cost[]" min="0" step="0.01"
                                                required
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <div class="md:col-span-3 mt-2">
                                            <label class="block text-sm font-medium text-gray-700">Item Description
                                                <span class="text-red-500">*</span></label>
                                            <textarea name="item_description[]" rows="3" required
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                                        </div>
                                        <div class="md:col-span-3 mt-2 flex justify-end">
                                            <button type="button"
                                                class="remove-item-btn bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm"
                                                style="display: none;">
                                                Remove Item
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="add-item-btn"
                                class="mt-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                + Add Another Item
                            </button>
                        </div>

                        <!-- Delivery Information -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Delivery Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="delivery_period"
                                        class="block text-sm font-medium text-gray-700">Delivery Period</label>
                                    <input type="text" name="delivery_period" id="delivery_period"
                                        value="{{ old('delivery_period') }}" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div>
                                    <label for="delivery_address"
                                        class="block text-sm font-medium text-gray-700">Delivery Address</label>
                                    <textarea name="delivery_address" id="delivery_address" rows="3" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('delivery_address') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Purpose -->
                        <div class="border-t pt-6">
                            <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose</label>
                            <textarea name="purpose" id="purpose" rows="3" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('purpose') }}</textarea>
                        </div>

                        <!-- Requested By -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Requested By</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="requested_by_name"
                                        class="block text-sm font-medium text-gray-700">Printed Name</label>
                                    <input type="text" name="requested_by_name" id="requested_by_name"
                                        value="{{ old('requested_by_name', auth()->user()->first_name . (auth()->user()->middle_name ? ' ' . auth()->user()->middle_name : '') . ' ' . auth()->user()->last_name) }}"
                                        required readonly
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div>
                                    <label for="requested_by_designation"
                                        class="block text-sm font-medium text-gray-700">Designation</label>
                                    <input type="text" name="requested_by_designation"
                                        id="requested_by_designation"
                                        value="{{ old('requested_by_designation', auth()->user()->designation) }}"
                                        required readonly
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
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
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Cancel
                            </a>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Create Purchase Request
                            </button>
                        </div>
                    </form>
                </div>
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
