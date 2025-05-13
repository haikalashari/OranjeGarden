@extends('layouts.app')

@section('title')
    Dashboard | OranjeGarden
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Form -->
    <div>
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
                    @if (Auth::check() && Auth::user()->role === 'super admin')
                        <option value="admin">Admin</option>
                        <option value="delivery">Delivery</option>
                    @else
                        <option value="delivery">Delivery</option>
                    @endif
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
        </form>
    </div>

    <!-- Table CRUD -->
    <div>
        <div class="overflow-x-auto bg-white rounded-lg shadow-md p-4">
            <h2 class="text-lg font-bold text-orange-600 mb-4">Daftar User</h2>
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="text-xs text-white uppercase bg-orange-500">
                    <tr>
                        <th class="px-4 py-2">Nama</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Role</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($allUser as $u)
                            <tr class="border-b hover:bg-orange-50">
                                <td class="px-4 py-2">{{ $u->name }}</td>
                                <td class="px-4 py-2">{{ $u->email }}</td>
                                <td class="px-4 py-2 capitalize">{{ $u->role }}</td>
                                <td class="px-4 py-2">
                                    <form action="{{ route('dashboard.kelola.user.hapus', $u->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection