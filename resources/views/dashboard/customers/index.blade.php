@extends('layouts.app')

@section('title')
    Dashboard | OranjeGarden
@endsection

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-orange-600">Halaman Pelanggan</h1>
    </div>

    <div class="flex flex-col md:flex-row items-start md:items-center justify-between pb-4 gap-3">
        <label for="table-search" class="sr-only">Search</label>
        <div class="relative w-full md:w-auto">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M8 4a4 4 0 1 1 0 8 4 4 0 0 1 0-8ZM2 8a6 6 0 1 1 11.293 3.707l4 4a1 1 0 0 1-1.414 1.414l-4-4A6 6 0 0 1 2 8Z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <input type="text" id="table-search" class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for products">
        </div>

        <button onclick="openAddModal()" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 md:w-auto text-center">+ Tambah Pelanggan</button>
    </div>

    <div class="relative overflow-hidden rounded-lg shadow-md">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                <thead class="text-xs text-white uppercase bg-orange-500 dark:bg-orange-600">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-semibold">Nama</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Nomor Hp</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Email</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Total Order</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Total Pengeluaran Uang</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800">
                    @foreach ($customers as $customer)
                    <tr class="border-b last:rounded-b-lg last:border-none hover:bg-orange-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $customer->name }}</td>
                        <td class="px-6 py-4">{{ $customer->contact_no }}</td>
                        <td class="px-6 py-4">{{ $customer->email }}</td>
                        <td class="px-6 py-4">{{ $customer->total_orders }}</td>
                        <td class="px-6 py-4 font-semibold text-orange-600 dark:text-orange-400">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 space-y-2 md:space-y-0 md:space-x-2 flex flex-col md:flex-row">                    
                            <a href="#" onclick="openEditModal({{ $customer->id }}, '{{ $customer->name }}', '{{ $customer->contact_no }}', '{{ $customer->email }}', '{{ $customer->total_orders }}', '{{ $customer->total_spent }}')" class="inline-block px-3 py-1 text-sm font-medium text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-300 focus:outline-none text-center">Edit</a>
                            <form action="{{ route('dashboard.kelola.customer.hapus', $customer->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Anda yakin ingin menghapus data customer ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 focus:ring-2 focus:ring-red-300 focus:outline-none text-center">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach

                    @if ($customers->isEmpty())
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">Tidak Ada Pelanggan untuk Sekarang.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Add Plant Modal -->
<div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden px-4">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-orange-600 mb-4">Tambah Pelanggan</h2>
        <form id="addForm" action="{{ route('dashboard.kelola.customer.tambah') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Pelanggan</label>
                <input type="text" name="name" id="name" class="block w-full mt-1 p-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label for="contact_no" class="block text-sm font-medium text-gray-700">Nomor Hp</label>
                <input type="tel" name="contact_no" id="contact_no" class="block w-full mt-1 p-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" class="block w-full mt-1 p-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label for="total_orders" class="block text-sm font-medium text-gray-700">Total Order</label>
                <input type="number" name="total_orders" id="total_orders" class="block w-full mt-1 p-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label for="total_spent" class="block text-sm font-medium text-gray-700">Total Pengeluaran Uang</label>
                <input type="number" name="total_spent" id="total_spent" class="block w-full mt-1 p-2 border rounded-lg">
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg mr-2">Batal</button>
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg">Tambah</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Customer Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden px-4">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-orange-600 mb-4">Edit Pelanggan</h2>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="edit-name" class="block text-sm font-medium text-gray-700">Nama Pelanggan</label>
                <input type="text" name="name" id="edit-name" class="block w-full mt-1 p-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label for="edit-contact_no" class="block text-sm font-medium text-gray-700">Nomor Hp</label>
                <input type="tel" name="contact_no" id="edit-contact_no" class="block w-full mt-1 p-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label for="edit-email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="edit-email" class="block w-full mt-1 p-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label for="edit-total_orders" class="block text-sm font-medium text-gray-700">Total Order</label>
                <input type="number" name="total_orders" id="edit-total_orders" class="block w-full mt-1 p-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label for="edit-total_spent" class="block text-sm font-medium text-gray-700">Total Pengeluaran Uang</label>
                <input type="number" name="total_spent" id="edit-total_spent" class="block w-full mt-1 p-2 border rounded-lg">
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg mr-2">Batal</button>
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>



@endsection

@section('scripts')
<script>
    // Function to open the add plant modal
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }

    // Function to close the add plant modal
    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }

    // Function to open the edit plant modal with pre-filled data
    function openEditModal(id, name, contact_no, email, total_orders, total_spent) {
        const editModal = document.getElementById('editModal');
        const editForm = document.getElementById('editForm');
        const editName = document.getElementById('edit-name');
        const editContactNo = document.getElementById('edit-contact_no');
        const editEmail = document.getElementById('edit-email');
        const editTotalOrders = document.getElementById('edit-total_orders');
        const editTotalSpent = document.getElementById('edit-total_spent');

        // Set form values
        editName.value = name;
        editContactNo.value = contact_no;
        editEmail.value = email;
        editTotalOrders.value = total_orders;
        editTotalSpent.value = total_spent;

        // Set the form action with the correct URL and ID
        editForm.action = `/dashboard/customers/${id}`;


        // Show modal
        editModal.classList.remove('hidden');
    }

    // Function to close the edit plant modal
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

</script>
@endsection
