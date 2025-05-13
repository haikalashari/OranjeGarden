<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | OranjeGarden</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    .btn-orange {
      @apply bg-orange-500 text-white hover:bg-orange-600 transition-colors duration-200;
    }
  </style>
</head>
<body class="bg-orange-50 min-h-screen flex items-center justify-center px-4 font-sans">

  <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-6 sm:p-8 space-y-6">
    <!-- Logo / Brand -->
    <div class="text-center">
      <h1 class="text-3xl font-extrabold text-orange-500 tracking-wide">OranjeGarden</h1>
    </div>

    <!-- Form Login -->
    <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
      @csrf

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" id="email" name="email" required
          class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-orange-400 focus:outline-none focus:border-orange-500 text-sm">
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
        <input type="password" id="password" name="password" required
          class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-orange-400 focus:outline-none focus:border-orange-500 text-sm">
      </div>

      <!-- Error Handling -->
      @if (session('error'))
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded-md text-sm">
          {{ session('error') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded-md text-sm">
          <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <!-- Submit -->
      <div>
        <button type="submit" class="btn-orange w-full py-2 rounded-lg text-sm font-semibold">Masuk</button>
      </div>
    </form>
  </div>

</body>
</html>
