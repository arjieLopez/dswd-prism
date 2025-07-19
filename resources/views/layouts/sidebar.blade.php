<aside
    class="fixed top-16 left-0 w-80 h-[calc(100vh-4rem)] bg-gradient-to-t from-blue-100 to-white border-r border-white flex flex-col py-6">
    <nav class="flex-1 space-y-2">
        @auth
            @if (Auth::user()->role === 'admin')
                <a href="{{ route('admin') }}" class="block px-4 py-2 rounded hover:bg-blue-100">Admin
                    Dashboard</a>
                <a href="{{ route('admin.reports') }}" class="block px-4 py-2 rounded hover:bg-blue-100">Reports</a>
                <a href="{{ route('admin.user_management') }}" class="block px-4 py-2 rounded hover:bg-blue-100">User
                    Management</a>
                <a href="{{ route('admin.audit_logs') }}" class="block px-4 py-2 rounded hover:bg-blue-100">Audit Logs</a>
            @elseif(Auth::user()->role === 'staff')
                <a href="{{ route('staff') }}" class="block px-4 py-2 rounded hover:bg-blue-100"> GSO
                    Dashboard</a>
                <a href="{{ route('staff.pr_review') }}" class="block px-4 py-2 rounded hover:bg-blue-100">PR Review</a>
                <a href="{{ route('staff.po_generation') }}" class="block px-4 py-2 rounded hover:bg-blue-100">PO
                    Generation</a>
                <a href="{{ route('staff.suppliers') }}" class="block px-4 py-2 rounded hover:bg-blue-100">Suppliers</a>
            @else
                <a href="{{ route('user') }}" class="block px-4 py-2 rounded hover:bg-blue-100">
                    Dashboard</a>
                <a href="{{ route('user.requests') }}" class="block px-4 py-2 rounded hover:bg-blue-100">My Requests</a>
            @endif
        @endauth
    </nav>
</aside>
