<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Page Heading -->
        @if (isset($header))
        <header class="bg-white shadow sticky top-0 z-30">
            <div class="flex flex-row items-center max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <!-- Sidebar -->
        <div class="flex min-h-screen">
            <aside
                class="w-1/3 max-w-xs bg-gradient-to-t from-indigo-50 to-white border-r border-gray-200 flex flex-col py-6 px-4 min-h-screen">
                <nav class="flex-1 space-y-2">
                    @auth
                    @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin') }}" class="block px-4 py-2 rounded hover:bg-blue-100">Admin
                        Dashboard</a>
                    {{-- <a href="{{ route('admin.users') }}" class="block px-4 py-2 rounded hover:bg-blue-100">Manage
                        Users</a>
                    <a href="{{ route('admin.reports') }}" class="block px-4 py-2 rounded hover:bg-blue-100">Reports</a>
                    --}}
                    @elseif(Auth::user()->role === 'staff')
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded hover:bg-blue-100">Staff
                        Dashboard</a>
                    <a href="{{ route('staff.tasks') }}" class="block px-4 py-2 rounded hover:bg-blue-100">My Tasks</a>
                    <a href="{{ route('staff.reports') }}" class="block px-4 py-2 rounded hover:bg-blue-100">Submit
                        Report</a>
                    @else
                    <a href="{{ route('admin') }}" class="block px-4 py-2 rounded hover:bg-blue-100">User
                        Dashboard</a>
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 rounded hover:bg-blue-100">Profile</a>
                    @endif
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="block px-4 py-2 rounded hover:bg-red-100 text-red-600 mt-8">
                        Log Out
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                    @endauth
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>

        </div>

</body>

</html>