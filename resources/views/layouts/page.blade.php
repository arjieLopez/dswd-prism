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

<body class="font-sans antialiased h-screen">
    <div class="min-h-screen bg-gray-100">
        <!-- Page Heading -->
        @if (isset($header))
        <header class="fixed top-0 left-0 w-full h-16 bg-white shadow z-30">
            <div class="flex items-center max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8 h-full">
                {{ $header }}
            </div>
        </header>
        @endif


        <div class="flex min-h-screen">
            <!-- Sidebar -->
            <aside
                class="fixed top-16 left-0 w-80 h-[calc(100vh-4rem)] bg-gradient-to-t from-blue-100 to-white border-r border-white flex flex-col py-6">
                <nav class="flex-1 space-y-2">
                    @auth
                    @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin') }}" class="block px-4 py-2 rounded font-semibold hover:bg-blue-100">Admin
                        Dashboard</a>
                    <a href="#" class="block px-4 py-2 rounded hover:bg-blue-100">Reports</a>
                    <a href="#" class="block px-4 py-2 rounded hover:bg-blue-100">User Management</a>
                    <a href="#" class="block px-4 py-2 rounded hover:bg-blue-100">Audit Logs</a>

                    @elseif(Auth::user()->role === 'staff')
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded hover:bg-blue-100">Staff
                        Dashboard</a>
                    <a href="{{ route('staff.tasks') }}" class="block px-4 py-2 rounded hover:bg-blue-100">My Tasks</a>
                    <a href="{{ route('staff.reports') }}" class="block px-4 py-2 rounded hover:bg-blue-100">Submit
                        Report</a>
                    @else
                    <a href="{{ route('admin') }}" class="block px-4 py-2 rounded hover:bg-blue-100">User
                        Dashboard</a>
                    @endif
                    @endauth
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="pt-16 pl-80 w-full">
                {{ $slot }}
            </main>

        </div>

</body>

</html>