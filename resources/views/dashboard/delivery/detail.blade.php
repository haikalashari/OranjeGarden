@extends('layouts.app')

@section('title')
    Detail Order | OranjeGarden
@endsection

@section('content')
<div class="p-6 space-y-6">
    <!-- Informasi Customer -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-orange-600 mb-4">Informasi Customer</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama Customer:</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $order->customer->name }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Kontak:</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $order->customer->contact_no }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Alamat Pengiriman:</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $order->delivery_address }}</p>
            </div>
        </div>
    </div>

    <!-- Daftar Item yang Dikirim -->
    <!-- Item Order -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
        <h2 class="text-xl font-bold text-orange-600 mb-4">Item dalam Order</h2>

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
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($delivery->created_at)->translatedFormat('d F Y H:i') }}</p>
                            </div>
                            @if ($delivery->delivery_photo)
                                <img src="{{ asset('storage/' . $delivery->delivery_photo) }}" class="h-16 w-16 object-cover rounded-md border">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>


    <!-- Upload Bukti Pengiriman -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-orange-600 mb-4">Upload Bukti Pengiriman</h2>
        <form action="{{ route('dashboard.kelola.delivery.konfirmasi', $order->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="delivery_photos" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unggah Foto Bukti Pengiriman</label>
                <input type="file" name="delivery_photos[]" id="delivery_photos" multiple class="block w-full mt-1 p-2 border rounded-lg">
            </div>
            <div id="preview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4"></div>
            <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">Konfirmasi Pengiriman</button>
        </form>
    </div>
</div>

@section('scripts')
<script>
    // Preview uploaded images
    const deliveryPhotosInput = document.getElementById('delivery_photos');
    const previewContainer = document.getElementById('preview');

    deliveryPhotosInput.addEventListener('change', function () {
        previewContainer.innerHTML = ''; // Clear previous previews
        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'h-20 w-20 object-cover rounded-lg border';
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });
</script>
@endsection
@endsection
