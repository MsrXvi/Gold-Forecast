<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Gold Forecast' }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-active {
            background-color: rgba(255, 193, 7, 0.1);
            border-left: 4px solid #FFC107;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-50">

    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">

            <!-- Header -->
            @include('layouts.header')

            <!-- Content -->
            <main class="p-8">
                @yield('content')
            </main>

        </div>

    </div>

    @stack('scripts')

</body>

</html>
