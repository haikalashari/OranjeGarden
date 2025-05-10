@extends('layouts.app')

@section('title')
    Detail Order | OranjeGarden
@endsection

@section('content')
<div class="mx-auto p-6 space-y-10">

    <!-- Header & Payment Status -->
    <div class="flex justify-between items-start flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-orange-600">Detail Order #{{ $order->id }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Tanggal: {{ \Carbon\Carbon::parse($order->order_date)->translatedFormat('d F Y') }} - 
                {{ \Carbon\Carbon::parse($order->end_date)->translatedFormat('d F Y') }} 
                ({{ $order->rental_duration }} Hari)
            </p>
        </div>
        <div class="flex flex-col items-start gap-3">
            <span class="px-3 py-1 rounded-full text-sm font-semibold self-center
                {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $order->payment_status === 'paid' ? 'Sudah Dibayar' : 'Belum Dibayar' }}
            </span>
            <!-- Button Edit Order -->

            @if ($order->latestStatus->status_category->status === 'Proses Pengantaran')
            <a href="{{ route('dashboard.kelola.order.edit.tampilkan', $order->id) }}" 
               class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                Edit Order
            </a>
            @endif
        </div>
    </div>

    <!-- Main Content Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Informasi Customer & Items -->
        <div class="space-y-6 lg:col-span-2">

            <!-- Informasi Customer -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-orange-600 mb-4">Informasi Customer</h2>
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex-1 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        <p><span class="font-medium">Nama:</span> {{ $order->customer->name }}</p>
                        <p><span class="font-medium">No. Hp:</span> {{ $order->customer->contact_no }}</p>
                        <p><span class="font-medium">No. Hp Pengganti:</span> {{ $order->customer->secondary_contact_no ?? '-'}}</p>
                        <p><span class="font-medium">Email:</span> {{ $order->customer->email }}</p>
                        <p><span class="font-medium">Alamat Pengiriman:</span> {{ $order->delivery_address }}</p>
                    </div>
                    <!-- Bukti Pembayaran -->
                    <div class="flex-shrink-0 self-start">
                        <p class="font-medium mb-2">Bukti Pembayaran:</p>
                        <button onclick="openPaymentProofModal('{{ $order->payment_proof ? asset('storage/' . $order->payment_proof) : '' }}')" 
                                class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                            Kelola Bukti Pembayaran
                        </button>
                    </div>
                </div>
            </div>

        <!-- Item Order -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-orange-600">Item dalam Order</h2>

                    <!-- Tombol Tambah Tanaman Batch Baru -->
                    @if ($order->latestStatus->status_category->status === 'Proses Penggantian Tanaman')
                        <button onclick="openAddBatchModal()" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                            + Tambah Tanaman Batch Baru
                        </button>
                    @endif

                        <!-- Tombol Assign Deliverer Pengambilan Kembali -->
                    @if ($order->latestStatus->status_category->status === 'Proses Pengambilan Kembali')
                        <button onclick="openAssignDelivererModal()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Assign Deliverer Pengambilan Kembali
                        </button>
                    @endif
                </div>

                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($order->orderItems as $item)
                        <div class="flex items-center justify-between py-4">
                            <!-- Gambar -->
                            <div class="flex items-center gap-4">
                                <img src="{{ asset('storage/' . $item->plant->photo) }}" alt="{{ $item->plant->name }}" class="h-24 w-24 object-cover rounded-md border">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $item->plant->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Kategori: {{ $item->plant->category ?? '-' }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Replacement Batch: <span class="text-orange-600">{{ $item->replacement_batch ?? 'N/A' }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="text-right text-sm text-gray-700 dark:text-gray-200 w-12">
                                x{{ $item->quantity }}
                            </div>

                            <div class="text-right font-semibold text-sm text-gray-900 dark:text-white w-28">
                                Rp {{ number_format($item->plant->price, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>


        </div>

        <!-- Right Column: Status -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm h-fit space-y-6">
            <h2 class="text-lg font-semibold text-orange-600">Status Order</h2>

            <!-- Status Saat Ini -->
            <div class="text-sm text-gray-700 dark:text-gray-300">
                <p><span class="font-medium">Status Saat Ini:</span> {{ $order->latestStatus->status_category->status }}</p>
            </div>

            <!-- Riwayat Status (Tracking Style) -->
            <div class="space-y-4">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Riwayat Status</h3>
                <div class="relative pl-6 border-l-2 border-orange-400 space-y-3">
                    @foreach ($order->status as $status)
                        <div class="relative">
                            <div class="absolute left-[-0.6rem] top-1 w-3 h-3 bg-orange-500 rounded-full"></div>
                            <div class="ml-2">
                                <p class="text-sm font-medium">{{ $status->status_category->status }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($status->created_at)->translatedFormat('d F Y H:i') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pemisah -->
            <hr class="border-t border-gray-300 dark:border-gray-600">

            <!-- Riwayat Pengiriman -->
            <div class="space-y-4">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Riwayat Pengiriman</h3>
                <div class="space-y-3">
                    @foreach ($order->deliverer as $delivery)
                        <div class="flex items-start gap-4">
                            <div class="flex-1 text-sm text-gray-700 dark:text-gray-300">
                                <p class="font-medium">Batch {{ $delivery->delivery_batch }} - {{ $delivery->status }}</p>
                                <p class="text-xs">Oleh: {{ $delivery->user->name ?? '-' }}</p>
                            </div>
                            @if ($delivery->delivery_photo)
                                <img src="{{ asset('storage/' . $delivery->delivery_photo) }}" class="h-16 w-16 object-cover rounded-md border">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

                        <!-- Tombol Order Selesai dan Order Dibatalkan -->
            <div class="flex gap-4 mt-4">
                <!-- Tombol Order Selesai -->
                <form action="{{ route('dashboard.kelola.order.selesai', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan order ini?')">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        Order Selesai
                    </button>
                </form>

                <!-- Tombol Order Dibatalkan -->
                <form action="{{ route('dashboard.kelola.order.batalkan', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan order ini?')">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                        Order Dibatalkan
                    </button>
                </form>
            </div>

        </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-orange-600 mb-4">Invoice</h2>

                <!-- Field untuk Biaya Tambahan -->
                <form action="{{ route('dashboard.kelola.order.generate.invoice', $order->id) }}" method="GET">
                    <div class="mb-4">
                        <label for="installation_fee" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Biaya Instalasi</label>
                        <input type="number" name="installation_fee" id="installation_fee" value="0" min="0" class="block w-full p-2 border rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 dark:border-gray-600">
                    </div>

                    <!-- Field untuk Keterangan -->
                    <div class="mb-4">
                        <label for="invoice_note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
                        <textarea name="invoice_note" id="invoice_note" rows="3" placeholder="Tambahkan keterangan untuk invoice..." class="block w-full p-2 border rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 dark:border-gray-600"></textarea>
                    </div>

                    <!-- Tombol Generate Invoice -->
                    <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                        Generate Invoice
                    </button>
                </form>
            </div> 
    </div>

</div>


    <!-- Modal Tambah Tanaman Batch Baru -->
    <div id="addBatchModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden px-4">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg max-h-screen overflow-y-auto">
            <h2 class="text-2xl font-bold text-orange-600 mb-4">Tambah Tanaman Batch Baru</h2>

            <form id="addBatchForm" action="{{ route('dashboard.kelola.order.tambah.tanamanbatch', $order->id) }}" method="POST">
                @csrf

                <!-- Pilih Deliverer -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Deliverer</label>
                    <select name="deliverer_id" class="block w-full p-2 border rounded-lg">
                        <option value="">-- Pilih Deliverer --</option>
                        @foreach ($deliverers as $deliverer)
                            <option value="{{ $deliverer->id }}">{{ $deliverer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Pilih Tanaman -->
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

                        <button type="button" onclick="addPlantItem()" class="bg-orange-500 text-white px-2 rounded hover:bg-orange-600">
                            +
                        </button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end">
                    <button type="button" onclick="closeAddBatchModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg mr-2">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="assignDelivererModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden px-4">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg max-h-screen overflow-y-auto">
        <h2 class="text-2xl font-bold text-blue-600 mb-4">Assign Deliverer Pengambilan Kembali</h2>

        <form id="assignDelivererForm" action="{{ route('dashboard.kelola.order.tambah.deliverer.ambilkembali', $order->id) }}" method="POST">
            @csrf

            <!-- Pilih Deliverer -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Deliverer</label>
                <select name="deliverer_id" class="block w-full p-2 border rounded-lg">
                    <option value="">-- Pilih Deliverer --</option>
                    @foreach ($deliverers as $deliverer)
                        <option value="{{ $deliverer->id }}">{{ $deliverer->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Actions -->
            <div class="flex justify-end">
                <button type="button" onclick="closeAssignDelivererModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg mr-2">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>



<div id="paymentProofModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden px-4">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg max-h-screen overflow-y-auto">
        <h2 class="text-2xl font-bold text-orange-600 mb-4">Kelola Bukti Pembayaran</h2>

        <!-- Preview Bukti Pembayaran -->
        <div id="paymentProofPreview" class="mb-4 hidden">
            <img id="paymentProofImage" src="" alt="Bukti Pembayaran" class="h-40 w-40 object-cover rounded-lg border mx-auto">
            <div class="flex justify-end mt-4">
            </div>
        </div>

        <!-- Form Upload Bukti Pembayaran -->
        <form id="paymentProofForm" action="{{ route('dashboard.kelola.order.editPaymentProof', $order->id) }}" method="POST" enctype="multipart/form-data" class="hidden">
            @csrf
            <div class="mb-4">
                <label for="payment_proof" class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Pembayaran</label>
                <input type="file" name="payment_proof" id="payment_proof" accept="image/*" class="block w-full p-2 border rounded-lg">
            </div>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="closePaymentProofModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg">Simpan</button>
            </div>
        </form>

        <!-- Tombol Hapus Bukti Pembayaran -->
        <form id="deletePaymentProofForm" action="{{ route('dashboard.kelola.order.deletePaymentProof', $order->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
            <div class="flex justify-between gap-4">
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 w-full">
                Hapus Bukti Pembayaran
            </button>
            <button type="button" onclick="closePaymentProofModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let plantIndex = 1;

    function openAddBatchModal() {
        document.getElementById('addBatchModal').classList.remove('hidden');
    }

    function closeAddBatchModal() {
        document.getElementById('addBatchModal').classList.add('hidden');
    }

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

    function openAssignDelivererModal() {
        document.getElementById('assignDelivererModal').classList.remove('hidden');
    }

    function closeAssignDelivererModal() {
        document.getElementById('assignDelivererModal').classList.add('hidden');
    }

    function openPaymentProofModal(imageUrl = null) {
        const modal = document.getElementById('paymentProofModal');
        const preview = document.getElementById('paymentProofPreview');
        const image = document.getElementById('paymentProofImage');
        const uploadForm = document.getElementById('paymentProofForm');
        const deleteForm = document.getElementById('deletePaymentProofForm');

        if (imageUrl) {
            // Jika ada bukti pembayaran, tampilkan preview dan tombol hapus
            preview.classList.remove('hidden');
            image.src = imageUrl;
            uploadForm.classList.add('hidden');
            deleteForm.classList.remove('hidden');
        } else {
            // Jika tidak ada bukti pembayaran, tampilkan form upload
            preview.classList.add('hidden');
            uploadForm.classList.remove('hidden');
            deleteForm.classList.add('hidden');
        }

        modal.classList.remove('hidden');
    }

    function closePaymentProofModal() {
        const modal = document.getElementById('paymentProofModal');
        modal.classList.add('hidden');
    }
</script>
@endsection
