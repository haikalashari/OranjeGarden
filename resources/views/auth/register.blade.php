<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | OranjeGarden</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    .btn-orange {
      @apply bg-orange-500 text-white hover:bg-orange-600 transition-colors duration-200;
    }
  </style>
</head>
<body class="bg-orange-50 min-h-screen flex items-center justify-center px-4 font-sans">

  <div class="w-full max-w-lg bg-white shadow-xl rounded-2xl p-6 sm:p-8 space-y-6">
    <!-- Brand -->
    <div class="text-center">
      <h1 class="text-3xl font-extrabold text-orange-500 tracking-wide">OranjeGarden</h1>
    </div>

    <!-- Form -->
    <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
      @csrf

      <!-- Nama Lengkap -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
        <input type="text" id="name" name="name" required
          class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-orange-400 focus:outline-none focus:border-orange-500 text-sm">
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" id="email" name="email" required
          class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-orange-400 focus:outline-none focus:border-orange-500 text-sm">
      </div>

      <!-- Kata Sandi -->
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
        <input type="password" id="password" name="password" required
          class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-orange-400 focus:outline-none focus:border-orange-500 text-sm">
      </div>

      <!-- Konfirmasi Kata Sandi -->
      <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Kata Sandi</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required
          class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-orange-400 focus:outline-none focus:border-orange-500 text-sm">
      </div>

      <!-- Pilih Role -->
      <div>
        <label for="role" class="block text-sm font-medium text-gray-700">Pilih Role</label>
        <select id="role" name="role" required
          class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-orange-400 focus:outline-none focus:border-orange-500 text-sm bg-white">
          <option value="" disabled selected>Pilih Role</option>
          <option value="admin">Admin</option>
          <option value="delivery">Delivery</option>
        </select>
      </div>

      <!-- Error Message -->
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
        <button type="submit" class="btn-orange w-full py-2 rounded-lg text-sm font-semibold">Daftar</button>
      </div>

      <!-- Login Link -->
      <div class="text-center">
        <p class="text-sm text-gray-600">Sudah punya akun?
          <a href="{{ route('login') }}" class="text-orange-500 hover:underline font-medium">Masuk di sini</a>
        </p>
      </div>
    </form>
  </div>

</body>
</html>
