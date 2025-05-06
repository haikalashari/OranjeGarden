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
        <span class="px-3 py-1 rounded-full text-sm font-semibold self-center
            {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
            {{ $order->payment_status === 'paid' ? 'Sudah Dibayar' : 'Belum Dibayar' }}
        </span>
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
                        @if ($order->payment_proof)
                            <p class="font-medium mb-2">Bukti Pembayaran:</p>
                            <img src="{{ asset('storage/' . $order->payment_proof) }}" alt="Bukti Pembayaran" class="h-40 w-40 object-cover rounded-lg border">
                        @else
                            <p class="text-xs text-gray-500 italic">Tidak ada bukti pembayaran.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Item Order -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-orange-600 mb-4">Item dalam Order</h2>

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

    <!-- Invoice Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-orange-600 mb-2">Invoice</h2>
        <p class="text-sm text-gray-500 italic">Fitur invoice akan tersedia di sini.</p>
    </div>

</div>
@endsection
