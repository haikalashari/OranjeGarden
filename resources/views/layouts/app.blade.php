<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) 
</head>
<body class="bg-gray-50 dark:bg-gray-900">

    @include('layouts.partials.header')     <!-- Navbar -->
    @include('layouts.partials.sidebar')    <!-- Sidebar -->

    <main class="p-4 sm:ml-64 mt-16">
        @yield('content')           <!-- Konten halaman -->
    </main>
    
</body>
@yield('scripts')
</html>
