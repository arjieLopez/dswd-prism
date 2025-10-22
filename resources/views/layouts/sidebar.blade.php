<aside
    class="fixed top-16 left-0 w-60 h-[calc(100vh-4rem)] bg-gradient-to-t from-blue-100 to-white border-r border-white flex flex-col py-6">
    <nav class="flex-1 space-y-2">
        @auth
            @if (Auth::user()->role === 'admin')
                <a href="{{ route('admin') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin') ? 'bg-gradient-to-r from-blue-300 to-white font-bold text-gray-900' : 'text-gray-700 hover:bg-blue-50' }}">
                    <i class="iconify w-5 h-5 mr-3 {{ request()->routeIs('admin') ? 'text-gray-900' : 'text-gray-600' }}"
                        data-icon="mdi:view-dashboard"></i>
                    Dashboard
                </a>

                <a href="{{ route('admin.reports') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.reports') ? 'bg-gradient-to-r from-blue-300 to-white font-bold text-gray-900' : 'text-gray-700 hover:bg-blue-50' }}">
                    <i class="iconify w-5 h-5 mr-3 {{ request()->routeIs('admin.reports') ? 'text-gray-900' : 'text-gray-600' }}"
                        data-icon="mdi:file-document"></i>
                    Reports
                </a>

                <a href="{{ route('admin.user_management') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.user_management') ? 'bg-gradient-to-r from-blue-300 to-white font-bold text-gray-900' : 'text-gray-700 hover:bg-blue-50' }}">
                    <i class="iconify w-5 h-5 mr-3 {{ request()->routeIs('admin.user_management') ? 'text-gray-900' : 'text-gray-600' }}"
                        data-icon="mdi:account-cog"></i>
                    User Management
                </a>

                <a href="{{ route('admin.system_selections') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.system_selections') ? 'bg-gradient-to-r from-blue-300 to-white font-bold text-gray-900' : 'text-gray-700 hover:bg-blue-50' }}">
                    <i class="iconify w-5 h-5 mr-3 {{ request()->routeIs('admin.system_selections') ? 'text-gray-900' : 'text-gray-600' }}"
                        data-icon="mdi:tune-variant"></i>
                    System Selections
                </a>

                <a href="{{ route('admin.audit_logs') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.audit_logs') ? 'bg-gradient-to-r from-blue-300 to-white font-bold text-gray-900' : 'text-gray-700 hover:bg-blue-50' }}">
                    <i class="iconify w-5 h-5 mr-3 {{ request()->routeIs('admin.audit_logs') ? 'text-gray-900' : 'text-gray-600' }}"
                        data-icon="mdi:folder"></i>
                    Audit Logs
                </a>
            @elseif(Auth::user()->role === 'staff')
                <a href="{{ route('staff') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('staff') ? 'bg-gradient-to-r from-blue-300 to-white font-bold text-gray-900' : 'text-gray-700 hover:bg-blue-50' }}">
                    <i class="iconify w-5 h-5 mr-3 {{ request()->routeIs('staff') ? 'text-gray-900' : 'text-gray-600' }}"
                        data-icon="mdi:view-dashboard"></i>
                    Dashboard
                </a>

                <a href="{{ route('staff.pr_review') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('staff.pr_review') || request()->routeIs('staff.generate_po.*') ? 'bg-gradient-to-r from-blue-300 to-white font-bold text-gray-900' : 'text-gray-700 hover:bg-blue-50' }}">
                    <i class="iconify w-5 h-5 mr-3 {{ request()->routeIs('staff.pr_review') || request()->routeIs('staff.generate_po.*') ? 'text-gray-900' : 'text-gray-600' }}"
                        data-icon="mdi:file-document"></i>
                    Request Review
                </a>

                <a href="{{ route('staff.po_generation') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('staff.po_generation') || request()->routeIs('po-documents.*') ? 'bg-gradient-to-r from-blue-300 to-white font-bold text-gray-900' : 'text-gray-700 hover:bg-blue-50' }}">
                    <i class="iconify w-5 h-5 mr-3 {{ request()->routeIs('staff.po_generation') || request()->routeIs('po-documents.*') ? 'text-gray-900' : 'text-gray-600' }}"
                        data-icon="mdi:cart"></i>
                    Purchase Orders
                </a>

                <a href="{{ route('staff.suppliers') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('staff.suppliers') ? 'bg-gradient-to-r from-blue-300 to-white font-bold text-gray-900' : 'text-gray-700 hover:bg-blue-50' }}">
                    <i class="iconify w-5 h-5 mr-3 {{ request()->routeIs('staff.suppliers') ? 'text-gray-900' : 'text-gray-600' }}"
                        data-icon="mdi:star"></i>
                    Suppliers
                </a>
            @else
                <a href="{{ route('user') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('user') ? 'bg-gradient-to-r from-blue-300 to-white font-bold text-gray-900' : 'text-gray-700 hover:bg-blue-50' }}">
                    <i class="iconify w-5 h-5 mr-3 {{ request()->routeIs('user') ? 'text-gray-900' : 'text-gray-600' }}"
                        data-icon="mdi:view-dashboard"></i>
                    Dashboard
                </a>

                <a href="{{ route('user.requests') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('user.requests') || request()->routeIs('purchase-requests.*') || request()->routeIs('uploaded-documents.*') ? 'bg-gradient-to-r from-blue-300 to-white font-bold text-gray-900' : 'text-gray-700 hover:bg-blue-50' }}">
                    <i class="iconify w-5 h-5 mr-3 {{ request()->routeIs('user.requests') ? 'text-gray-900' : 'text-gray-600' }}"
                        data-icon="mdi:file-document"></i>
                    My Requests
                </a>
            @endif
        @endauth
    </nav>
</aside>
