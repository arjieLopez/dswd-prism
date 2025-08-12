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
                                    value="{{ old('office_section') }}" required
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
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="unit" class="block text-sm font-medium text-gray-700">Unit</label>
                                    <input type="text" name="unit" id="unit" value="{{ old('unit') }}"
                                        required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div>
                                    <label for="quantity"
                                        class="block text-sm font-medium text-gray-700">Quantity</label>
                                    <input type="number" name="quantity" id="quantity"
                                        value="{{ old('quantity') }}" min="1" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div>
                                    <label for="unit_cost" class="block text-sm font-medium text-gray-700">Unit Cost
                                        (₱)</label>
                                    <input type="number" name="unit_cost" id="unit_cost"
                                        value="{{ old('unit_cost') }}" min="0" step="0.01" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="item_description" class="block text-sm font-medium text-gray-700">Item
                                    Description</label>
                                <textarea name="item_description" id="item_description" rows="3" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('item_description') }}</textarea>
                            </div>
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
                                        value="{{ old('requested_by_name', auth()->user()->name) }}" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div>
                                    <label for="requested_by_designation"
                                        class="block text-sm font-medium text-gray-700">Designation</label>
                                    <input type="text" name="requested_by_designation"
                                        id="requested_by_designation" value="{{ old('requested_by_designation') }}"
                                        required
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
</x-page-layout>
