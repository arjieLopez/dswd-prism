<x-page-layout>
    <x-slot name="header">
        <a href="/admin">
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

    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('GSO Dashboard') }}
                </h2>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-3 gap-6">
                <!-- Pending PRs Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-8 py-6 flex items-center justify-between">
                        <div>
                            <div class="font-semibold text-gray-400 tracking-wide">
                                {{ __('Pending PRs') }}
                            </div>
                            <div class="font-semibold text-2xl text-gray-700">
                                {{ $pendingPRs }}
                            </div>
                            <div class="text-xs text-gray-400 tracking-wide">
                                <p><span
                                        class="{{ $pendingPercentageChange >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $pendingPercentageChange >= 0 ? '+' : '' }}{{ $pendingPercentageChange }}%</span>
                                    {{ __('from') }}</p>
                                <p>{{ __('last month') }}</p>
                            </div>
                        </div>
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center ml-4">
                            <i class="iconify w-8 h-8 text-yellow-600" data-icon="mdi:clock-outline"></i>
                        </div>
                    </div>
                </div>

                <!-- Approved PRs Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-8 py-6 flex items-center justify-between">
                        <div>
                            <div class="font-semibold text-gray-400 tracking-wide">
                                {{ __('Approved PRs') }}
                            </div>
                            <div class="font-semibold text-2xl text-gray-700">
                                {{ $approvedPRs }}
                            </div>
                            <div class="text-xs text-gray-400 tracking-wide">
                                <p><span
                                        class="{{ $approvedPercentageChange >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $approvedPercentageChange >= 0 ? '+' : '' }}{{ $approvedPercentageChange }}%</span>
                                    {{ __('from') }}</p>
                                <p>{{ __('last month') }}</p>
                            </div>
                        </div>
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center ml-4">
                            <i class="iconify w-8 h-8 text-green-600" data-icon="mdi:check-circle-outline"></i>
                        </div>
                    </div>
                </div>

                <!-- POs Generated Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-8 py-6 flex items-center justify-between">
                        <div>
                            <div class="font-semibold text-gray-400 tracking-wide">
                                {{ __('POs Generated') }}
                            </div>
                            <div class="font-semibold text-2xl text-gray-700">
                                {{ $poGenerated }}
                            </div>
                            <div class="text-xs text-gray-400 tracking-wide">
                                <p><span
                                        class="{{ $poGeneratedPercentageChange >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $poGeneratedPercentageChange >= 0 ? '+' : '' }}{{ $poGeneratedPercentageChange }}%</span>
                                    {{ __('from') }}</p>
                                <p>{{ __('last month') }}</p>
                            </div>
                        </div>
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center ml-4">
                            <i class="iconify w-8 h-8 text-blue-600" data-icon="mdi:cart-outline"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Generated Purchase Orders Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ __('Generated Purchase Orders') }}
                    </h3>
                </div>

                @if ($generatedPOs->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        PO #
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Supplier
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date Generated
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Delivery Term
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Payment Term
                                    </th>
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
        </div>
    </div>

</x-page-layout>
