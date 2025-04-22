<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | OranjeGarden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="d-flex">
        @include('layouts.partials.sidebar')

        <div class="flex-grow-1">
            @include('layouts.partials.header')

            <main class="p-4">
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>
