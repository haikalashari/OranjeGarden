@extends('layouts.app')

@section('title')
    Dashboard | OranjeGarden
@endsection

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-orange-600">Halaman Order</h1>
    </div>

    <div class="flex flex-col md:flex-row items-start md:items-center justify-between pb-4 gap-3">
    <form action="{{ route('dashboard.kelola.order') }}" method="GET" class="relative w-full md:w-auto">
        <label for="table-search" class="sr-only">Search</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M8 4a4 4 0 1 1 0 8 4 4 0 0 1 0-8ZM2 8a6 6 0 1 1 11.293 3.707l4 4a1 1 0 0 1-1.414 1.414l-4-4A6 6 0 0 1 2 8Z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <input type="text" name="search" id="table-search" value="{{ request('search') }}" class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Cari Nama Customer">
        </div>
    </form>
    <button onclick="openAddModal()" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 md:w-auto text-center">+ Tambah Order</button>
    </div>

    <div class="relative overflow-hidden rounded-lg shadow-md">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                <thead class="text-xs text-white uppercase bg-orange-500 dark:bg-orange-600">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-semibold">Nomor</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Nama Customer</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Tanggal Order</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Tanggal Sewa Berakhir</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Durasi Sewa</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Hari Berjalan</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Alamat Order</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Total Harga Order</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Status Pembayaran</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Status Order</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800">
                    @foreach ($order as $index => $item)
                    <tr class="border-b last:rounded-b-lg last:border-none hover:bg-orange-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $item->customer->name }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($item->order_date)->translatedFormat('d F Y') }}</td>      
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($item->end_date)->translatedFormat('d F Y') }}</td>                  
                        <td class="px-6 py-4">{{ $item->rental_duration }} Hari</td>
                        <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($item->order_date)->startOfDay()->diffInDays(\Carbon\Carbon::now()->startOfDay()) + 1 }} Hari
                        </td>
                        <td class="px-6 py-4">{{ $item->delivery_address}}</td>
                        <td class="px-6 py-4 font-semibold text-orange-600 dark:text-orange-400">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                        @if($item->payment_status == 'paid')
                        <td class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800">
                        Sudah Dibayar
                        </td>
                        @else
                        <td class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-800">
                        Belum Dibayar
                        </td>
                        @endif
                        <td class="px-6 py-4">{{ $item->latestStatus->status_category->status }}</td>
                        <td class="px-6 py-4 space-y-2 md:space-y-0 md:space-x-2 flex flex-col md:flex-row">
                            <form action="{{ route('dashboard.kelola.order.hapus', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Anda yakin ingin menghapus data order ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 focus:ring-2 focus:ring-red-300 focus:outline-none text-center">
                                    Hapus
                                </button>
                            </form>
                            <a href="{{ route('dashboard.kelola.order.detail', $item->id) }}" class="inline-block px-3 py-1 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 focus:ring-2 focus:ring-blue-300 focus:outline-none text-center">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach

                    @if ($order->isEmpty())
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">Tidak ada Order Saat ini.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">
        {{ $order->links() }}
    </div>
</div>


<div id="addOrderModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden px-4">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg max-h-screen overflow-y-auto"">
        <h2 class="text-2xl font-bold text-orange-600 mb-4">Tambah Order</h2>

        <form id="addOrderForm" action="{{ route('dashboard.kelola.order.tambah') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Customer Selection -->
            <div class="mb-4">
                <label for="customer_id" class="block text-sm font-medium text-gray-700">Pilih Customer</label>
                <select name="customer_id" id="customer_id" class="block w-full mt-1 p-2 border rounded-lg" onchange="toggleNewCustomerForm(this)">
                    <option value="">-- Pilih Customer --</option>
                    <option value="new">+ Tambah Customer Baru</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->contact_no }}</option>
                    @endforeach
                </select>
            </div>

            <!-- New Customer Form (Hidden Initially) -->
            <div id="newCustomerForm" class="hidden">
                <div class="mb-4">
                    <label for="new_customer_name" class="block text-sm font-medium text-gray-700">Nama Customer Baru</label>
                    <input type="text" name="new_customer_name" id="new_customer_name" class="block w-full mt-1 p-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label for="new_customer_contact" class="block text-sm font-medium text-gray-700">Nomor HP Customer Baru</label>
                    <input type="text" name="new_customer_contact" id="new_customer_contact" class="block w-full mt-1 p-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label for="new_secondary_customer_contact" class="block text-sm font-medium text-gray-700">Nomor HP Pengganti Customer Baru</label>
                    <input type="text" name="new_secondary_customer_contact" id="new_secondary_customer_contact" class="block w-full mt-1 p-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label for="new_customer_email" class="block text-sm font-medium text-gray-700">Email Customer Baru</label>
                    <input type="email" name="new_customer_email" id="new_customer_email" class="block w-full mt-1 p-2 border rounded-lg">
                </div>
            </div>

            <div class="mb-4">
                <label for="rental_range" class="block text-sm font-medium text-gray-700">Pilih Durasi Sewa</label>
                <input type="text" id="rental_range" name="rental_range" class="block w-full mt-1 p-2 border rounded-lg" placeholder="--Pilih Rentang Tanggal--" required>
            </div>

            <input type="hidden" name="order_date" id="order_date">
            <input type="hidden" name="end_date" id="end_date">


            <div class="mb-4">
                <label for="delivery_address" class="block text-sm font-medium text-gray-700">Alamat Pengiriman</label>
                <textarea name="delivery_address" id="delivery_address" rows="3" class="block w-full mt-1 p-2 border rounded-lg" required></textarea>
            </div>

            <div id="plantsSection" class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Tanaman & Jumlah</label>

                <div class="plant-item flex gap-2 mb-2">
                    <select name="plants[0][plant_id]" class="block w-full p-2 border rounded-lg">
                        <option value="">-- Pilih Tanaman --</option>
                        @foreach ($plants as $plant)
                            <option value="{{ $plant->id }}">{{ $plant->name }} -- {{ $plant->category }}</option>
                        @endforeach
                    </select>

                    <input type="number" name="plants[0][quantity]" placeholder="Jumlah" min="1" class="block w-24 p-2 border rounded-lg">

                    <button type="button" onclick="addPlantItem()" class="bg-green-500 text-white px-2 rounded hover:bg-green-600">
                        +
                    </button>
                </div>
            </div>

            <!-- Proof of Payment -->
            <div id="proofPaymentField" class="mb-4">
                <label for="payment_proof" class="block text-sm font-medium text-gray-700">Upload Bukti Pembayaran</label>
                <input type="file" name="payment_proof" id="payment_proof" class="block w-full mt-1 p-2 border rounded-lg">
            </div>

            <!-- Deliverer Selection -->
            <div class="mb-4">
                <label for="assigned_deliverer_id" class="block text-sm font-medium text-gray-700">Pilih Pengantar</label>
                <select name="assigned_deliverer_id" id="assigned_deliverer_id" class="block w-full mt-1 p-2 border rounded-lg">
                    <option value="">-- Pilih Pengantar --</option>
                    @foreach ($deliverers as $deliverer)
                        <option value="{{ $deliverer->id }}">{{ $deliverer->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Actions -->
            <div class="flex justify-end">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg mr-2">Batal</button>
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg">Simpan Order</button>
            </div>
        </form>
    </div>
</div>



@endsection

@section('scripts')
<script>
    // Function to open the add plant modal
    function openAddModal() {
        document.getElementById('addOrderModal').classList.remove('hidden');
    }

    // Function to close the add plant modal
    function closeAddModal() {
        document.getElementById('addOrderModal').classList.add('hidden');
    }

    function toggleNewCustomerForm(select) {
        const newCustomerForm = document.getElementById('newCustomerForm');
        if (select.value === 'new') {
            newCustomerForm.classList.remove('hidden');
        } else {
            newCustomerForm.classList.add('hidden');
        }
    }

    let plantIndex = 1;

    function addPlantItem() {
    const plantsSection = document.getElementById('plantsSection');

    const newPlantItem = document.createElement('div');
    newPlantItem.className = 'plant-item flex gap-2 mb-2';
    newPlantItem.innerHTML = `
        <select name="plants[${plantIndex}][plant_id]" class="block w-full p-2 border rounded-lg">
            <option value="">-- Pilih Tanaman --</option>
            @foreach ($plants as $plant)
                <option value="{{ $plant->id }}">{{ $plant->name }} -- {{ $plant->category }}</option>
            @endforeach
        </select>

        <input type="number" name="plants[${plantIndex}][quantity]" placeholder="Jumlah" min="1" class="block w-24 p-2 border rounded-lg">

        <button type="button" class="bg-red-500 text-white px-2 rounded hover:bg-red-600" onclick="this.parentElement.remove()">
            &times;
        </button>
    `;
    plantsSection.appendChild(newPlantItem);
    plantIndex++;
}

    flatpickr("#rental_range", {
        mode: "range",
        dateFormat: "Y-m-d",
        onChange: function(selectedDates) {
            if (selectedDates.length === 2) {
                document.getElementById('order_date').value = toDateInputValue(selectedDates[0]);
                
                // Kurangi satu hari dari tanggal akhir
                const adjustedEndDate = new Date(selectedDates[1]);
                adjustedEndDate.setDate(adjustedEndDate.getDate());
                document.getElementById('end_date').value = toDateInputValue(adjustedEndDate);
            }
        }
    });

    const toDateInputValue = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0'); 
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    
</script>
@endsection
