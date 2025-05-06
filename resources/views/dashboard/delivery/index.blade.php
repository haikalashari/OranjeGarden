@extends('layouts.app')

@section('title')
    Dashboard | OranjeGarden
@endsection

@section('content')
<div class="mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-orange-600 mb-6">Daftar Order Siap Antar</h1>

    @if ($orders->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($orders as $order)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 space-y-4 border border-orange-100">
                    <div>
                        <p class="text-sm text-gray-500 uppercase">Nama Customer</p>
                        <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            {{ $order->customer->name }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 uppercase">Alamat</p>
                        <p class="text-base font-medium text-gray-700 dark:text-gray-300">
                            {{ $order->delivery_address }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 uppercase">Kontak Utama</p>
                        <p class="text-base font-medium text-gray-700 dark:text-gray-300">
                            {{ $order->customer->contact_no }}
                        </p>
                    </div>

                    @if ($order->customer->secondary_contact_no)
                        <div>
                            <p class="text-sm text-gray-500 uppercase">Kontak Cadangan</p>
                            <p class="text-base font-medium text-gray-700 dark:text-gray-300">
                                {{ $order->customer->secondary_contact_no }}
                            </p>
                        </div>
                    @endif

                    <div>
                        <p class="text-sm text-gray-500 uppercase">Proses</p>
                        <p class="text-base font-semibold text-orange-600">
                            {{ $order->process_type ?? 'Pengantaran' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 uppercase">Status Terakhir</p>
                        <p class="text-base text-gray-700 dark:text-gray-300">
                            {{ $order->status->last()->status_category->status ?? 'Belum ada status' }}
                        </p>
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('dashboard.kelola.delivery.detail', $order->id) }}"
                           class="inline-block bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center text-gray-500 text-base mt-10">Tidak ada order siap antar saat ini.</div>
    @endif
</div>
@endsection
