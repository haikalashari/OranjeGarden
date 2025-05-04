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

        <button onclick="openAddModal()" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 md:w-auto text-center">+ Tambah Order</button>
    </div>

    <div class="relative overflow-hidden rounded-lg shadow-md">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                <thead class="text-xs text-white uppercase bg-orange-500 dark:bg-orange-600">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-semibold">Order ID</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Nama Customer</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Tanggal Order Dibuat</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Durasi Rental</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Alamat Order</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Total Harga Order</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Status Pembayaran</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Status Order</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Pengantar</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800">
                    @foreach ($order as $item)
                    <tr class="border-b last:rounded-b-lg last:border-none hover:bg-orange-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $item->id }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $item->customer->name }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($item->order_date)->translatedFormat('d F Y') }}</td>                        <td class="px-6 py-4">{{ $item->rental_duration }} Hari</td>
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
                        <td class="px-6 py-4">{{ $item->deliverer->name }}</td>
                        <td class="px-6 py-4 space-y-2 md:space-y-0 md:space-x-2 flex flex-col md:flex-row">
                            <form action="{{ route('dashboard.kelola.order.hapus', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Anda yakin ingin menghapus data order ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 focus:ring-2 focus:ring-red-300 focus:outline-none text-center">
                                    Hapus
                                </button>
                            </form>
                            <a href="#" class="inline-block px-3 py-1 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 focus:ring-2 focus:ring-blue-300 focus:outline-none text-center">
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
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
        <h2 class="text-2xl font-bold text-orange-600 mb-4">Tambah Order</h2>

        <form id="addOrderForm" action="{{ route('dashboard.kelola.order.tambah') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Customer Selection -->
            <div class="mb-4">
                <label for="customer_id" class="block text-sm font-medium text-gray-700">Pilih Customer</label>
                <select name="customer_id" id="customer_id" class="block w-full mt-1 p-2 border rounded-lg" onchange="toggleNewCustomerForm(this)">
                    <option value="">-- Pilih Customer --</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->contact_no }}</option>
                    @endforeach
                    <option value="new">+ Tambah Customer Baru</option>
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
                    <label for="new_customer_email" class="block text-sm font-medium text-gray-700">Email Customer Baru</label>
                    <input type="email" name="new_customer_email" id="new_customer_email" class="block w-full mt-1 p-2 border rounded-lg">
                </div>
            </div>

            <!-- Rental Duration -->
            <div class="mb-4">
                <label for="rental_duration" class="block text-sm font-medium text-gray-700">Durasi Rental (Hari)</label>
                <input type="number" name="rental_duration" id="rental_duration" class="block w-full mt-1 p-2 border rounded-lg" required>
            </div>

            <!-- Delivery Address -->
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
                            <option value="{{ $plant->id }}">{{ $plant->name }}</option>
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



<!-- <div id="qrCodeModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden px-4">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md text-center">
        <h2 class="text-2xl font-bold text-orange-600 mb-4">Plant QR Code</h2>

        <div id="qr-code-inner" class="flex justify-center mb-6">
        </div>

        <div class="mb-6 space-y-2">
            <p class="text-sm"><span class="font-medium">Plant ID:</span> <span id="qr-plant-id"></span></p>
            <p class="text-sm"><span class="font-medium">Name:</span> <span id="qr-plant-name"></span></p>
        </div>

        <div class="flex justify-between">
            <button onclick="printQRCode()" class="px-4 py-2 bg-blue-500 text-white rounded-lg flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </button>
            
            <button onclick="downloadQRCode()" class="px-4 py-2 bg-green-500 text-white rounded-lg flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download
            </button>
            
            <button onclick="closeQRModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg">
                Close
            </button>
        </div>
    </div>
</div> -->



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
        newPlantItem.classList.add('plant-item', 'flex', 'gap-2', 'mb-2');
        newPlantItem.innerHTML = `
            <select name="plants[${plantIndex}][plant_id]" class="block w-full p-2 border rounded-lg">
                <option value="">-- Pilih Tanaman --</option>
                @foreach ($plants as $plant)
                    <option value="{{ $plant->id }}">{{ $plant->name }}</option>
                @endforeach
            </select>

            <input type="number" name="plants[${plantIndex}][quantity]" placeholder="Jumlah" min="1" class="block w-24 p-2 border rounded-lg">

            <button type="button" onclick="addPlantItem()" class="bg-green-500 text-white px-2 rounded hover:bg-green-600">
                +
            </button>
        `;

        plantsSection.appendChild(newPlantItem);
        plantIndex++;
    }

    // function openQRModal(plantId, plantName, qrCodeUrl) {
    //     document.getElementById('qrCodeModal').classList.remove('hidden');

    //     // Update the plant info
    //     document.getElementById('qr-plant-id').textContent = plantId;
    //     document.getElementById('qr-plant-name').textContent = plantName;

    //     // Fetch the QR Code file dynamically
    //     fetch(qrCodeUrl)
    //         .then(response => response.text())
    //         .then(svg => {
    //             document.getElementById('qr-code-inner').innerHTML = svg;
    //         });
    // }

    // function closeQRModal() {
    //     document.getElementById('qrCodeModal').classList.add('hidden');
    // }

    // function printQRCode() {
    //     const printWindow = window.open('', '_blank');
    //     const qrContent = document.getElementById('qr-code-inner').innerHTML;
    //     const plantName = document.getElementById('qr-plant-name').textContent;
    //     const plantId = document.getElementById('qr-plant-id').textContent;

    //     printWindow.document.write(`
    //         <html>
    //             <head><title>Print QR Code</title></head>
    //             <body style="text-align: center; padding-top: 50px;">
    //                 ${qrContent}
    //                 <h2>${plantName} (ID: ${plantId})</h2>
    //             </body>
    //         </html>
    //     `);
    //     printWindow.document.close();
    //     printWindow.print();
    // }

    // function downloadQRCode() {
    //     const svgElement = document.getElementById('qr-code-inner').querySelector('svg');
    //     if (!svgElement) {
    //         alert('QR code not found!');
    //         return;
    //     }

    //     const svgData = new XMLSerializer().serializeToString(svgElement);
    //     const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
    //     const url = URL.createObjectURL(svgBlob);

    //     const link = document.createElement('a');
    //     link.href = url;
    //     link.download = 'plant-qr-code.svg';
    //     document.body.appendChild(link);
    //     link.click();
    //     document.body.removeChild(link);
    //     URL.revokeObjectURL(url);
    // }
</script>
@endsection
