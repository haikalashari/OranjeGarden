<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <title>@yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) 
</head>
<body class="bg-gray-50 dark:bg-gray-900">

    @include('layouts.partials.header')     <!-- Navbar -->
    @if (Auth::user()->role === 'admin' || Auth::user()->role === 'super admin')
        @include('layouts.partials.sidebar') <!-- Sidebar untuk Admin -->
    @elseif (Auth::user()->role === 'delivery')
        @include('layouts.partials.sidebar_deliverer') <!-- Sidebar untuk Delivery -->
    @endif

    <main class="p-4 sm:ml-64 mt-16">
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        
        
        @yield('content')           <!-- Konten halaman -->
    </main>
    
</body>
@yield('scripts')
</html>
