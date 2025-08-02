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
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Generate Purchase Order') }}
                    </h2>
                    <a href="{{ route('staff.po_generation') }}"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg">
                        Back to PO Generation
                    </a>
                </div>

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="post" action="{{ route('staff.generate_po.store', $purchaseRequest) }}"
                    class="space-y-6">
                    @csrf

                    <!-- PR Information Section -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Purchase Request Information</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">PR Number</label>
                                <input type="text" value="{{ $purchaseRequest->pr_number }}" readonly
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Requesting Unit</label>
                                <input type="text" value="{{ $purchaseRequest->user->name }}" readonly
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                            </div>
                        </div>
                    </div>

                    <!-- Supplier Information Section -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Supplier Information</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="supplier_id" class="block text-sm font-medium text-gray-700">Supplier
                                    *</label>
                                <select name="supplier_id" id="supplier_id" required onchange="updateSupplierInfo()"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" data-address="{{ $supplier->address }}"
                                            data-tin="{{ $supplier->tin }}">
                                            {{ $supplier->supplier_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="po_number" class="block text-sm font-medium text-gray-700">PO Number</label>
                                <input type="text" name="po_number" id="po_number"
                                    value="{{ $autoGeneratedPONumber }}" readonly
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                            </div>
                            <div>
                                <label for="supplier_address" class="block text-sm font-medium text-gray-700">Supplier
                                    Address</label>
                                <textarea name="supplier_address" id="supplier_address" rows="2" readonly
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100"></textarea>
                            </div>
                            <div>
                                <label for="supplier_tin" class="block text-sm font-medium text-gray-700">TIN</label>
                                <input type="text" name="supplier_tin" id="supplier_tin" readonly
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                            </div>
                        </div>
                    </div>

                    <!-- Procurement Details Section -->
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Procurement Details</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="mode_of_procurement" class="block text-sm font-medium text-gray-700">Mode of
                                    Procurement *</label>
                                <select name="mode_of_procurement" id="mode_of_procurement" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Mode</option>
                                    <option value="Shopping">Shopping</option>
                                    <option value="Small Value Procurement">Small Value Procurement</option>
                                    <option value="Direct Contracting">Direct Contracting</option>
                                    <option value="Limited Source Bidding">Limited Source Bidding</option>
                                    <option value="Competitive Bidding">Competitive Bidding</option>
                                </select>
                            </div>
                            <div>
                                <label for="place_of_delivery" class="block text-sm font-medium text-gray-700">Place
                                    of Delivery</label>
                                <input type="text" name="place_of_delivery" id="place_of_delivery"
                                    value="{{ $purchaseRequest->delivery_address }}" readonly
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                            </div>
                            <div>
                                <label for="delivery_term" class="block text-sm font-medium text-gray-700">Delivery
                                    Term *</label>
                                <input type="text" name="delivery_term" id="delivery_term" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="e.g., Within 30 days from receipt of PO">
                            </div>
                            <div>
                                <label for="payment_term" class="block text-sm font-medium text-gray-700">Payment Term
                                    *</label>
                                <input type="text" name="payment_term" id="payment_term" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="e.g., 30 days after delivery">
                            </div>
                            <div>
                                <label for="date_of_delivery" class="block text-sm font-medium text-gray-700">Date of
                                    Delivery *</label>
                                <input type="date" name="date_of_delivery" id="date_of_delivery" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Item Details Section -->
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Item Details (from PR)</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Stock/Unit</label>
                                <input type="text" value="{{ $purchaseRequest->unit }}" readonly
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea rows="3" readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100">{{ $purchaseRequest->item_description }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Quantity</label>
                                <input type="text" value="{{ $purchaseRequest->quantity }}" readonly
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Unit Cost</label>
                                <input type="text" value="₱{{ number_format($purchaseRequest->unit_cost, 2) }}"
                                    readonly
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Total Cost</label>
                                <input type="text" value="₱{{ number_format($purchaseRequest->total_cost, 2) }}"
                                    readonly
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-lg font-semibold">
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-4 pt-6">
                        <a href="{{ route('staff.po_generation') }}"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg">
                            Cancel
                        </a>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                            Generate PO
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateSupplierInfo() {
            const supplierSelect = document.getElementById('supplier_id');
            const selectedOption = supplierSelect.options[supplierSelect.selectedIndex];

            if (selectedOption.value) {
                document.getElementById('supplier_address').value = selectedOption.getAttribute('data-address') || '';
                document.getElementById('supplier_tin').value = selectedOption.getAttribute('data-tin') || '';
            } else {
                document.getElementById('supplier_address').value = '';
                document.getElementById('supplier_tin').value = '';
            }
        }
    </script>
</x-page-layout>
